<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Deployment;
use App\Models\NextOfKin;
use App\Models\OffEmployment;
use App\Models\Staff;
use App\Models\StaffStatus;
use App\Services\StaffRecordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Registry manages a staff member's Next of Kin, Contacts, Deployments and
 * Off-Employment from the staff record tabs.
 */
class Admin_StaffRecordController extends Controller
{
    public function __construct(private StaffRecordService $records) {}

    private function done(Staff $staff, string $tab, string $message): RedirectResponse
    {
        return redirect()->to(route('admin.staff.show', $staff) . '#' . $tab)->with('success', $message);
    }

    private function owned(Staff $staff, $model): void
    {
        abort_unless((int) $model->staff_id === (int) $staff->id, 404);
    }

    /** Fields left blank on an edit are cleared, not kept from before. */
    private function blanks(array $data, array $optional): array
    {
        return $data + array_fill_keys($optional, null);
    }

    // ── Next of Kin ──────────────────────────────────────────

    private function kinRules(): array
    {
        return [
            'relationship' => ['required', 'string', 'max:50'],
            'surname'      => ['required', 'string', 'max:255'],
            'firstname'    => ['required', 'string', 'max:255'],
            'othernames'   => ['nullable', 'string', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:30'],
            'email'        => ['nullable', 'email', 'max:255'],
            'address'      => ['nullable', 'string', 'max:500'],
            'city'         => ['nullable', 'string', 'max:100'],
            'state_id'     => ['nullable', 'exists:states,id'],
        ];
    }

    public function storeKin(Request $request, Staff $staff): RedirectResponse
    {
        $data = $request->validateWithBag('kin', $this->kinRules());
        $this->records->addNextOfKin($staff, $data);
        return $this->done($staff, 'nextofkin', 'Next of kin added and set as current. The previous one is kept in the history.');
    }

    public function updateKin(Request $request, Staff $staff, NextOfKin $kin): RedirectResponse
    {
        $this->owned($staff, $kin);
        $data = $request->validateWithBag('kin', $this->kinRules());
        $kin->update($this->blanks($data, ['othernames', 'phone', 'email', 'address', 'city', 'state_id']));
        return $this->done($staff, 'nextofkin', 'Next of kin updated.');
    }

    public function makeKinCurrent(Staff $staff, NextOfKin $kin): RedirectResponse
    {
        $this->owned($staff, $kin);
        $this->records->makeNextOfKinCurrent($kin);
        return $this->done($staff, 'nextofkin', "{$kin->surname}, {$kin->firstname} is now the current next of kin.");
    }

    public function destroyKin(Staff $staff, NextOfKin $kin): RedirectResponse
    {
        $this->owned($staff, $kin);
        $this->records->deleteNextOfKin($kin);
        return $this->done($staff, 'nextofkin', 'Next of kin deleted.');
    }

    // ── Contacts ─────────────────────────────────────────────

    private function contactRules(Request $request): array
    {
        $isEmail = str_contains(strtolower((string) \App\Models\ContactType::whereKey($request->contact_type_id)->value('contact_type_name')), 'email');
        return [
            'contact_type_id' => ['required', 'exists:contact_types,id'],
            'contact_value'   => array_merge(['required', 'string', 'max:255'], $isEmail ? ['email'] : []),
        ];
    }

    public function storeContact(Request $request, Staff $staff): RedirectResponse
    {
        $data = $request->validateWithBag('contact', $this->contactRules($request));
        $staff->contacts()->create($data);
        return $this->done($staff, 'contact', 'Contact added.');
    }

    public function updateContact(Request $request, Staff $staff, Contact $contact): RedirectResponse
    {
        $this->owned($staff, $contact);
        $contact->update($request->validateWithBag('contact', $this->contactRules($request)));
        return $this->done($staff, 'contact', 'Contact updated.');
    }

    public function destroyContact(Staff $staff, Contact $contact): RedirectResponse
    {
        $this->owned($staff, $contact);
        $contact->delete();
        return $this->done($staff, 'contact', 'Contact deleted.');
    }

    // ── Deployments ──────────────────────────────────────────

    private function deploymentRules(bool $new): array
    {
        return [
            'from_department_id' => ['nullable', 'exists:departments,id'],
            'department_id'      => ['required', 'exists:departments,id', 'different:from_department_id'],
            'deployment_post_id' => ['required', 'exists:deployment_posts,id'],
            'start_date'         => [$new ? 'required' : 'nullable', 'date'],
            'expected_end_date'  => ['nullable', 'date', 'after_or_equal:start_date'],
            'end_date'           => ['nullable', 'date', 'after_or_equal:start_date'],
            'remark'             => ['nullable', 'string', 'max:500'],
        ];
    }

    private function deploymentMessages(): array
    {
        return [
            'department_id.required'      => 'Choose the department the staff member is going to.',
            'department_id.different'     => 'The "to" department must be different from the "from" department.',
            'deployment_post_id.required' => 'Choose the post.',
            'start_date.required'         => 'Enter the date the deployment starts.',
        ];
    }

    public function storeDeployment(Request $request, Staff $staff): RedirectResponse
    {
        $data = $request->validateWithBag('deployment', $this->deploymentRules(true), $this->deploymentMessages());
        $this->records->addDeployment($staff, $data);
        return $this->done($staff, 'deployments', 'Deployment recorded. Any previous ongoing deployment has been ended.');
    }

    public function updateDeployment(Request $request, Staff $staff, Deployment $deployment): RedirectResponse
    {
        $this->owned($staff, $deployment);
        $data = $request->validateWithBag('deployment', $this->deploymentRules(false), $this->deploymentMessages());
        $deployment->update($this->blanks($data, ['from_department_id', 'start_date', 'expected_end_date', 'end_date', 'remark']));
        return $this->done($staff, 'deployments', 'Deployment updated.');
    }

    public function destroyDeployment(Staff $staff, Deployment $deployment): RedirectResponse
    {
        $this->owned($staff, $deployment);
        $deployment->delete();
        return $this->done($staff, 'deployments', 'Deployment deleted.');
    }

    // ── Off-Employment ───────────────────────────────────────

    private function offRules(): array
    {
        $activeId = StaffStatus::where('status', 'Active')->value('id');
        return [
            'staff_status_id'    => ['required', 'exists:staff_statuses,id', Rule::notIn(array_filter([$activeId]))],
            'effective_date'     => ['required', 'date'],
            'reason'             => ['required', 'string', 'max:1000'],
            'document_reference' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function storeOff(Request $request, Staff $staff): RedirectResponse
    {
        $data = $request->validateWithBag('offemp', $this->offRules(), ['staff_status_id.not_in' => 'Choose how the staff member left service (not "Active").']);
        $this->records->addOffEmployment($staff, $data);
        $status = StaffStatus::find($data['staff_status_id'])?->status;
        return $this->done($staff, 'offemployments', "Off-employment recorded. Staff status changed to {$status}.");
    }

    public function updateOff(Request $request, Staff $staff, OffEmployment $offEmployment): RedirectResponse
    {
        $this->owned($staff, $offEmployment);
        $data = $request->validateWithBag('offemp', $this->offRules(), ['staff_status_id.not_in' => 'Choose how the staff member left service (not "Active").']);
        $this->records->updateOffEmployment($offEmployment, $data + ['document_reference' => null]);
        return $this->done($staff, 'offemployments', 'Off-employment record updated.');
    }

    public function destroyOff(Staff $staff, OffEmployment $offEmployment): RedirectResponse
    {
        $this->owned($staff, $offEmployment);
        $this->records->deleteOffEmployment($offEmployment);
        $status = $staff->fresh()->staffStatus?->status ?? 'Active';
        return $this->done($staff, 'offemployments', "Off-employment record deleted. Staff status is now {$status}.");
    }
}
