@php $old = fn ($f) => $errors->getBag('contact')->any() ? old($f) : null; @endphp
<div class="flex justify-end mb-3">
    <button type="button" class="btn-wf-primary btn-wf-sm" onclick="openRecordForm('contact-form')">
        <i class="ti ti-plus text-xs"></i> Add Contact
    </button>
</div>

@include('admin.staff.tabs._form_open', ['id' => 'contact-form', 'bag' => 'contact', 'store' => route('admin.staff.contacts.store', $staff), 'title' => 'Contact'])
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
            <label class="wf-label">Contact type <span class="text-red-500">*</span></label>
            <select name="contact_type_id" class="{{ $input }}" required>
                <option value="">— Select —</option>
                @foreach($contactTypes as $ct)
                    <option value="{{ $ct->id }}" {{ (string) $old('contact_type_id') === (string) $ct->id ? 'selected' : '' }}>{{ $ct->contact_type_name }}</option>
                @endforeach
            </select>
            <a href="{{ route('admin.contact_types.index') }}" class="text-[10px] text-funaab-700 hover:underline">Manage contact types</a>
        </div>
        <div class="md:col-span-2">
            <label class="wf-label">Contact <span class="text-red-500">*</span></label>
            <input name="contact_value" value="{{ $old('contact_value') }}" class="{{ $input }}" required placeholder="e.g. 0803 123 4567 or name@funaab.edu.ng">
        </div>
    </div>
@include('admin.staff.tabs._form_close')

@if($staff->contacts->count())
    <table class="wf-table">
        <thead><tr><th>CONTACT TYPE</th><th>CONTACT</th><th>ADDED</th><th class="text-right">ACTIONS</th></tr></thead>
        <tbody>
        @foreach($staff->contacts->sortBy(fn ($c) => $c->contactType?->contact_type_name) as $contact)
            <tr>
                <td class="col-name">{{ $contact->contactType?->contact_type_name ?? '—' }}</td>
                <td>{{ $contact->contact_value }}</td>
                <td class="text-gray-400 text-xs">{{ $contact->created_at?->format('d M Y') }}</td>
                <td>
                    <div class="flex justify-end gap-1">
                        <button type="button" class="btn-wf-ghost btn-wf-sm" onclick="openRecordForm('contact-form', this)"
                                data-action="{{ route('admin.staff.contacts.update', [$staff, $contact]) }}"
                                data-contact_type_id="{{ $contact->contact_type_id }}" data-contact_value="{{ $contact->contact_value }}">
                            <i class="ti ti-edit text-xs"></i> Edit
                        </button>
                        <form action="{{ route('admin.staff.contacts.destroy', [$staff, $contact]) }}" method="POST" onsubmit="return confirm('Delete this contact?')">
                            @csrf @method('DELETE')
                            <button class="btn-wf-danger btn-wf-sm"><i class="ti ti-trash text-xs"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <p class="text-center text-gray-400 py-8 text-sm">No contact information on record.</p>
@endif
