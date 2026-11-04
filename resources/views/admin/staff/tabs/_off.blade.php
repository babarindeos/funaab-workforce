@php $old = fn ($f) => $errors->getBag('offemp')->any() ? old($f) : null; @endphp
<div class="flex flex-wrap items-center justify-between gap-2 mb-3">
    <p class="text-[11px] text-gray-500">Current status: <strong>{{ $staff->staffStatus?->status ?? 'Not set' }}</strong></p>
    <button type="button" class="btn-wf-danger btn-wf-sm" onclick="openRecordForm('off-form')">
        <i class="ti ti-plus text-xs"></i> Record Off-Employment
    </button>
</div>

@include('admin.staff.tabs._form_open', ['id' => 'off-form', 'bag' => 'offemp', 'store' => route('admin.staff.off.store', $staff), 'title' => 'Off-Employment'])
    <p class="text-[11px] text-amber-700 mb-3">Saving changes the staff member's status. They will no longer be counted as serving staff, nor appear in Circle or APER.</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
            <label class="wf-label">Status <span class="text-red-500">*</span></label>
            <select name="staff_status_id" class="{{ $input }}" required>
                <option value="">— Select —</option>
                @foreach($offStatuses as $st)
                    <option value="{{ $st->id }}" {{ (string) $old('staff_status_id') === (string) $st->id ? 'selected' : '' }}>{{ $st->status }}</option>
                @endforeach
            </select>
        </div>
        <div><label class="wf-label">Effective date <span class="text-red-500">*</span></label><input type="date" name="effective_date" value="{{ $old('effective_date') }}" class="{{ $input }}" required></div>
        <div><label class="wf-label">Document reference</label><input name="document_reference" value="{{ $old('document_reference') }}" class="{{ $input }}" placeholder="e.g. FUNAAB/REG/RT/123"></div>
        <div class="md:col-span-3"><label class="wf-label">Reason <span class="text-red-500">*</span></label><input name="reason" value="{{ $old('reason') }}" class="{{ $input }}" required placeholder="e.g. Mandatory retirement at 65"></div>
    </div>
@include('admin.staff.tabs._form_close')

@if($staff->offEmployments->count())
    <table class="wf-table">
        <thead><tr><th>STATUS</th><th>EFFECTIVE DATE</th><th>REASON</th><th>DOC REFERENCE</th><th>RECORDED</th><th class="text-right">ACTIONS</th></tr></thead>
        <tbody>
        @foreach($staff->offEmployments->sortByDesc(fn ($o) => $o->effective_date?->format('Y-m-d') . sprintf('%08d', $o->id)) as $off)
            <tr>
                <td><span class="badge badge-inactive">{{ $off->staffStatus?->status ?? '—' }}</span></td>
                <td class="whitespace-nowrap">{{ $off->effective_date?->format('d M Y') ?? '—' }}</td>
                <td>{{ $off->reason }}</td>
                <td class="col-mono">{{ $off->document_reference ?? '—' }}</td>
                <td class="text-gray-400 text-xs">{{ $off->created_at?->format('d M Y') }}</td>
                <td>
                    <div class="flex justify-end gap-1">
                        <button type="button" class="btn-wf-ghost btn-wf-sm" onclick="openRecordForm('off-form', this)"
                                data-action="{{ route('admin.staff.off.update', [$staff, $off]) }}"
                                data-staff_status_id="{{ $off->staff_status_id }}" data-effective_date="{{ $off->effective_date?->format('Y-m-d') }}"
                                data-reason="{{ $off->reason }}" data-document_reference="{{ $off->document_reference }}">
                            <i class="ti ti-edit text-xs"></i> Edit
                        </button>
                        <form action="{{ route('admin.staff.off.destroy', [$staff, $off]) }}" method="POST"
                              onsubmit="return confirm('Delete this off-employment record? If it is the latest one, the staff member\'s previous status will be restored.')">
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
    <p class="text-center text-gray-400 py-8 text-sm">No off-employment records.</p>
@endif
