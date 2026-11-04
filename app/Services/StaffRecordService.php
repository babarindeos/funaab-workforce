<?php

namespace App\Services;

use App\Models\Deployment;
use App\Models\NextOfKin;
use App\Models\OffEmployment;
use App\Models\Staff;
use App\Models\StaffStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Rules for the staff record history (Next of Kin, Deployments,
 * Off-Employment), shared by every page that changes them.
 */
class StaffRecordService
{
    // ── Next of Kin ──────────────────────────────────────────────────

    /** A new next of kin becomes current; the previous ones are kept. */
    public function addNextOfKin(Staff $staff, array $data): NextOfKin
    {
        return DB::transaction(function () use ($staff, $data) {
            NextOfKin::where('staff_id', $staff->id)->where('is_current', true)
                ->update(['is_current' => false, 'replaced_at' => now()]);

            return NextOfKin::create($data + ['staff_id' => $staff->id, 'is_current' => true, 'replaced_at' => null]);
        });
    }

    public function makeNextOfKinCurrent(NextOfKin $kin): void
    {
        DB::transaction(function () use ($kin) {
            NextOfKin::where('staff_id', $kin->staff_id)->where('id', '<>', $kin->id)->where('is_current', true)
                ->update(['is_current' => false, 'replaced_at' => now()]);
            $kin->update(['is_current' => true, 'replaced_at' => null]);
        });
    }

    /** Deleting the current one makes the most recent remaining one current. */
    public function deleteNextOfKin(NextOfKin $kin): void
    {
        DB::transaction(function () use ($kin) {
            $wasCurrent = $kin->is_current;
            $staffId    = $kin->staff_id;
            $kin->delete();

            if ($wasCurrent) {
                $next = NextOfKin::where('staff_id', $staffId)->orderByDesc('id')->first();
                $next?->update(['is_current' => true, 'replaced_at' => null]);
            }
        });
    }

    // ── Deployments ──────────────────────────────────────────────────

    /** Where the staff member is now: their ongoing deployment, else the home department. */
    public function currentDepartmentId(Staff $staff): ?int
    {
        return Deployment::where('staff_id', $staff->id)->whereNull('end_date')->orderByDesc('start_date')->value('department_id')
            ?? $staff->employment?->department_id;
    }

    /**
     * Adds a deployment. Any deployment still ongoing that started before the
     * new one is ended the day before the new one starts. The staff member's
     * home department (Employment) is never changed.
     */
    public function addDeployment(Staff $staff, array $data): Deployment
    {
        return DB::transaction(function () use ($staff, $data) {
            $start = Carbon::parse($data['start_date']);

            // Where they are moving from — worked out before the ongoing deployment is ended.
            $data['from_department_id'] ??= $this->currentDepartmentId($staff);

            Deployment::where('staff_id', $staff->id)
                ->whereNull('end_date')
                ->where(fn ($q) => $q->whereNull('start_date')->orWhereDate('start_date', '<', $start))
                ->get()
                ->each(fn ($d) => $d->update(['end_date' => $start->copy()->subDay()->toDateString()]));

            return Deployment::create($data + ['staff_id' => $staff->id]);
        });
    }

    // ── Off-Employment ───────────────────────────────────────────────

    /** Records how the staff member left service and changes their status. */
    public function addOffEmployment(Staff $staff, array $data): OffEmployment
    {
        return DB::transaction(function () use ($staff, $data) {
            $record = OffEmployment::create($data + [
                'staff_id'                 => $staff->id,
                'previous_staff_status_id' => $staff->staff_status_id,
            ]);
            $staff->update(['staff_status_id' => $data['staff_status_id']]);
            return $record;
        });
    }

    /** Editing the latest record keeps the staff status in step with it. */
    public function updateOffEmployment(OffEmployment $record, array $data): void
    {
        DB::transaction(function () use ($record, $data) {
            $record->update($data);
            if ($this->isLatest($record)) {
                Staff::whereKey($record->staff_id)->update(['staff_status_id' => $record->staff_status_id]);
            }
        });
    }

    /**
     * Deleting the latest record (entered in error) puts the staff member back
     * to the status they had before it — Active if that is not known.
     */
    public function deleteOffEmployment(OffEmployment $record): void
    {
        DB::transaction(function () use ($record) {
            $latest  = $this->isLatest($record);
            $restore = $record->previous_staff_status_id
                ?? StaffStatus::where('status', 'Active')->value('id');
            $staffId = $record->staff_id;
            $record->delete();

            if ($latest) {
                $remaining = OffEmployment::where('staff_id', $staffId)->orderByDesc('effective_date')->orderByDesc('id')->first();
                Staff::whereKey($staffId)->update(['staff_status_id' => $remaining?->staff_status_id ?? $restore]);
            }
        });
    }

    private function isLatest(OffEmployment $record): bool
    {
        return OffEmployment::where('staff_id', $record->staff_id)
            ->orderByDesc('effective_date')->orderByDesc('id')->value('id') === $record->id;
    }
}
