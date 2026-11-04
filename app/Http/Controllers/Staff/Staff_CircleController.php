<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffStatus;
use App\Services\AperWorkflow;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Circle — a read-only directory of the colleagues serving in the same
 * division as the signed-in staff member: photo, name, designation, e-mail.
 */
class Staff_CircleController extends Controller
{
    public function __construct(private AperWorkflow $wf) {}

    public function index(Request $request): View
    {
        $me         = $this->wf->currentStaff();
        $divisionId = $me->employment?->division_id;
        $division   = $me->employment?->division;
        $search     = trim((string) $request->query('q', ''));

        $members = collect();
        if ($divisionId) {
            $inactive = StaffStatus::whereIn('status', config('workforce.inactive_staff_statuses', []))->pluck('id');

            $members = Staff::query()
                ->whereHas('employment', fn ($q) => $q->where('division_id', $divisionId))
                ->when($inactive->isNotEmpty(), fn ($q) => $q->where(fn ($w) => $w->whereNull('staff_status_id')->orWhereNotIn('staff_status_id', $inactive)))
                ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w
                    ->where('surname', 'like', "%{$search}%")
                    ->orWhere('firstname', 'like', "%{$search}%")
                    ->orWhere('othernames', 'like', "%{$search}%")))
                ->with(['title', 'employment.designation.cadreRank', 'user.profile'])
                ->orderBy('surname')->orderBy('firstname')
                ->paginate(24)->withQueryString();
        }

        return view('staff.circle.index', compact('members', 'division', 'search', 'me'));
    }
}
