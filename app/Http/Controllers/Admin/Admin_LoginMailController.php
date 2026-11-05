<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Division;
use App\Models\LoginMailLog;
use App\Models\Staff;
use App\Services\LoginMailService;
use Illuminate\Http\Request;

/**
 * Registry sends login mails: to one staff member, or in bulk to serving
 * staff who have an e-mail address and have never been sent one.
 */
class Admin_LoginMailController extends Controller
{
    /** Mails sent per bulk request; the page keeps calling until none remain. */
    private const BATCH = 10;

    public function __construct(private LoginMailService $mails) {}

    public function index(Request $request)
    {
        $f = $this->filters($request);
        $staff = $this->mails->query($f)
            ->with(['title', 'user', 'contacts', 'employment.department'])
            ->orderBy('surname')->orderBy('firstname')
            ->paginate(50)->withQueryString();

        $counts      = $this->mails->counts($f);
        $divisions   = Division::orderBy('division_name')->get(['id', 'division_name', 'short_name']);
        $departments = Department::when($f['division'] ?? null, fn ($q, $d) => $q->where('division_id', $d))->orderBy('department_name')->get(['id', 'department_name']);
        $failures    = LoginMailLog::where('status', 'failed')->where('created_at', '>=', now()->subDays(7))->latest()->limit(20)->get()
            ->keyBy('staff_id');

        return view('admin.login_mails.index', [
            'staff' => $staff, 'counts' => $counts, 'f' => $f, 'divisions' => $divisions, 'departments' => $departments,
            'mails' => $this->mails, 'failures' => $failures, 'statuses' => LoginMailService::STATUSES,
        ]);
    }

    public function send(Request $request, Staff $staff)
    {
        [$ok, $message] = $this->mails->send($staff->load(['user', 'contacts', 'title']), $request->user());
        return back()->with($ok ? 'success' : 'error', "{$staff->surname} {$staff->firstname}: {$message}");
    }

    /** One batch of the bulk send (called repeatedly by the page). */
    public function bulk(Request $request)
    {
        @set_time_limit(300);
        $f    = $this->filters($request);
        $skip = array_map('intval', (array) $request->input('skip', []));   // failed earlier in this run

        $batch = $this->mails->neverSent($f)->whereNotIn('id', $skip)
            ->with(['user', 'contacts', 'title'])->orderBy('id')->limit(self::BATCH)->get();

        $sent = 0; $failed = [];
        foreach ($batch as $s) {
            [$ok, $message] = $this->mails->send($s, $request->user());
            $ok ? $sent++ : $failed[] = ['id' => $s->id, 'staff_no' => $s->staff_no, 'name' => "{$s->surname} {$s->firstname}", 'error' => $message];
        }

        return response()->json([
            'sent'      => $sent,
            'failed'    => $failed,
            'remaining' => $this->mails->neverSent($f)->whereNotIn('id', [...$skip, ...array_column($failed, 'id')])->count(),
        ]);
    }

    private function filters(Request $request): array
    {
        return array_filter([
            'division'   => $request->integer('division') ?: null,
            'department' => $request->integer('department') ?: null,
            'status'     => array_key_exists((string) $request->input('status'), LoginMailService::STATUSES) ? $request->input('status') : null,
            'q'          => trim((string) $request->input('q')) ?: null,
        ]);
    }
}
