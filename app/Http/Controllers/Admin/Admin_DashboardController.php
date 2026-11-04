<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AperSubmission;
use App\Models\Department;
use App\Models\Division;
use App\Models\Leave;
use App\Models\Promotion;
use App\Models\Staff;
use App\Models\StaffCategory;
use App\Models\StaffDocument;
use App\Models\StaffStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Admin dashboard — every figure is read live from the database.
 */
class Admin_DashboardController extends Controller
{
    public function index()
    {
        $today    = Carbon::today();
        $inactive = StaffStatus::whereIn('status', config('workforce.inactive_staff_statuses', []))->pluck('id');

        $serving = fn () => Staff::query()->when($inactive->isNotEmpty(),
            fn ($q) => $q->where(fn ($w) => $w->whereNull('staff_status_id')->orWhereNotIn('staff_status_id', $inactive)));

        // ── 1. Summary boxes ─────────────────────────────────────────────
        $totalStaff     = $serving()->count();
        $addedThisMonth = Staff::where('created_at', '>=', $today->copy()->startOfMonth())->count();

        $approvedLeave = fn () => Leave::where('approval_status', 'approved');
        $onLeave       = $approvedLeave()->whereDate('start_date', '<=', $today)->whereDate('end_date', '>=', $today)->count();
        $resumingSoon  = $approvedLeave()->whereBetween('resumption_date', [$today->toDateString(), $today->copy()->addDays(7)->toDateString()])->count();

        $pendingPromotions = Promotion::where(fn ($q) => $q->where('status', 'pending')->orWhereDate('effective_date', '>', $today))->count();

        $totalDepartments = Department::count();
        $totalDivisions   = Division::count();

        // ── 2. Recent activity (built from real events) ──────────────────
        $recentActivity = $this->recentActivity();

        // ── 3. Staff by category / employment type ───────────────────────
        $byCategory = DB::table('staff')
            ->leftJoin('employments', 'employments.staff_id', '=', 'staff.id')
            ->when($inactive->isNotEmpty(), fn ($q) => $q->where(fn ($w) => $w->whereNull('staff.staff_status_id')->orWhereNotIn('staff.staff_status_id', $inactive)))
            ->selectRaw('employments.staff_category_id AS cat, COUNT(*) AS n')
            ->groupBy('employments.staff_category_id')
            ->pluck('n', 'cat');

        $categoryNames = StaffCategory::pluck('category', 'id');
        $categoryRows  = $byCategory->map(fn ($n, $cat) => [
            'label' => $categoryNames[$cat] ?? 'Category not set',
            'count' => (int) $n,
            'pct'   => $totalStaff ? round($n / $totalStaff * 100, 1) : 0,
        ])->sortByDesc('count')->values();

        $byType = DB::table('staff')
            ->leftJoin('employments', 'employments.staff_id', '=', 'staff.id')
            ->leftJoin('employment_types', 'employment_types.id', '=', 'employments.employment_type_id')
            ->when($inactive->isNotEmpty(), fn ($q) => $q->where(fn ($w) => $w->whereNull('staff.staff_status_id')->orWhereNotIn('staff.staff_status_id', $inactive)))
            ->selectRaw('employment_types.employment_type AS t, COUNT(*) AS n')
            ->groupBy('employment_types.employment_type')
            ->pluck('n', 't');

        $fullTime      = (int) $byType->filter(fn ($n, $t) => str_contains(strtolower((string) $t), 'full'))->sum();
        $otherTime     = (int) $byType->filter(fn ($n, $t) => $t && !str_contains(strtolower($t), 'full'))->sum();
        $typeNotSet    = (int) $byType->filter(fn ($n, $t) => $t === '' || $t === null)->sum();

        // ── 4. Recently added staff ──────────────────────────────────────
        $recentStaff = Staff::with(['title', 'employment.department', 'employment.staffCategory', 'staffStatus'])
            ->latest()->take(5)->get();

        $onLeaveIds = $approvedLeave()->whereDate('start_date', '<=', $today)->whereDate('end_date', '>=', $today)
            ->whereIn('staff_id', $recentStaff->pluck('id'))->pluck('staff_id')->all();

        return view('admin.dashboard', compact(
            'totalStaff', 'addedThisMonth', 'onLeave', 'resumingSoon', 'pendingPromotions',
            'totalDepartments', 'totalDivisions', 'recentActivity', 'categoryRows',
            'fullTime', 'otherTime', 'typeNotSet', 'recentStaff', 'onLeaveIds'
        ));
    }

    /**
     * The latest events across the system, newest first.
     * Each item: when, text (HTML-escaped parts), colour.
     */
    private function recentActivity(int $limit = 8): Collection
    {
        $name = fn ($s) => $s ? e(trim("{$s->surname}, {$s->firstname}")) : 'Unknown staff';
        $events = collect();

        Staff::latest()->take($limit)->get()->each(fn ($s) => $events->push([
            'at' => $s->created_at, 'colour' => 'bg-funaab-500',
            'html' => "New staff record — <strong>{$name($s)}</strong> ({$s->staff_no})",
        ]));

        Leave::with('staff')->whereIn('approval_status', ['approved', 'rejected'])->latest('updated_at')->take($limit)->get()
            ->each(fn ($l) => $events->push([
                'at' => $l->updated_at, 'colour' => 'bg-gold-500',
                'html' => 'Leave ' . e($l->approval_status) . " — <strong>{$name($l->staff)}</strong>",
            ]));

        Promotion::with(['staff', 'salaryStep.salaryLevel.salaryStructure'])->latest()->take($limit)->get()
            ->each(function ($p) use ($events, $name) {
                $lvl = $p->salaryStep?->salaryLevel;
                $to  = $lvl ? ' → ' . e(trim(($lvl->salaryStructure?->salary_structure_name ?? '') . ' ' . $lvl->level)) : '';
                $events->push(['at' => $p->created_at, 'colour' => 'bg-blue-400',
                    'html' => "Promotion recorded — <strong>{$name($p->staff)}</strong>{$to}"]);
            });

        AperSubmission::with(['staff', 'aperForm'])->whereNotNull('staff_submitted_at')->latest('staff_submitted_at')->take($limit)->get()
            ->each(fn ($a) => $events->push([
                'at' => $a->staff_submitted_at, 'colour' => 'bg-purple-400',
                'html' => 'APER submitted (' . e($a->aperForm?->name) . ") — <strong>{$name($a->staff)}</strong>",
            ]));

        StaffDocument::with(['staff', 'type'])->latest()->take($limit)->get()
            ->each(fn ($d) => $events->push([
                'at' => $d->created_at, 'colour' => 'bg-gray-400',
                'html' => ($d->source === 'registry' ? 'Document added by Registry' : 'Document uploaded for approval')
                    . ' — ' . e($d->type?->name) . " — <strong>{$name($d->staff)}</strong>",
            ]));

        StaffDocument::with(['staff', 'type'])->where('source', 'staff')->whereNotNull('reviewed_at')->latest('reviewed_at')->take($limit)->get()
            ->each(fn ($d) => $events->push([
                'at' => $d->reviewed_at, 'colour' => $d->isApproved() ? 'bg-green-500' : 'bg-red-400',
                'html' => 'Document ' . ($d->isApproved() ? 'approved' : 'rejected') . ' — ' . e($d->type?->name) . " — <strong>{$name($d->staff)}</strong>",
            ]));

        return $events->filter(fn ($e) => $e['at'])->sortByDesc('at')->take($limit)->values();
    }
}
