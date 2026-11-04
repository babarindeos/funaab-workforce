<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Qualification;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Registry adds, edits and deletes a staff member's qualifications
 * (Staff record → Qualifications tab).
 */
class Admin_StaffQualificationController extends Controller
{
    private function rules(): array
    {
        return [
            'degree_id'       => ['required', 'exists:degrees,id'],
            'institution'     => ['required', 'string', 'max:255'],
            'start_date'      => ['nullable', 'date'],
            'end_date'        => ['nullable', 'date', 'after_or_equal:start_date'],
            'degree_class_id' => ['nullable', 'exists:degree_classes,id'],
            'remark'          => ['nullable', 'string', 'max:1000'],
        ];
    }

    private function messages(): array
    {
        return [
            'degree_id.required'      => 'Choose the degree or certificate.',
            'institution.required'    => 'Enter the institution.',
            'end_date.after_or_equal' => 'The end date cannot be before the start date.',
        ];
    }

    private function back(Staff $staff, string $message): RedirectResponse
    {
        return redirect()->to(route('admin.staff.show', $staff) . '#qualifications')->with('success', $message);
    }

    public function store(Request $request, Staff $staff): RedirectResponse
    {
        $data = $request->validateWithBag('qualification', $this->rules(), $this->messages());
        $staff->qualifications()->create($data);

        return $this->back($staff, 'Qualification added.');
    }

    public function update(Request $request, Staff $staff, Qualification $qualification): RedirectResponse
    {
        abort_unless($qualification->staff_id === $staff->id, 404);
        $data = $request->validateWithBag('qualification', $this->rules(), $this->messages());
        // Optional fields left blank are cleared, not kept from before.
        $qualification->update($data + array_fill_keys(['start_date', 'end_date', 'degree_class_id', 'remark'], null));

        return $this->back($staff, 'Qualification updated.');
    }

    public function destroy(Staff $staff, Qualification $qualification): RedirectResponse
    {
        abort_unless($qualification->staff_id === $staff->id, 404);
        $qualification->delete();

        return $this->back($staff, 'Qualification deleted.');
    }
}
