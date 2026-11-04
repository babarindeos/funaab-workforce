@php $old = fn ($f) => $errors->getBag('kin')->any() ? old($f) : null; @endphp
<div class="flex justify-end mb-3">
    <button type="button" class="btn-wf-primary btn-wf-sm" onclick="openRecordForm('kin-form')">
        <i class="ti ti-plus text-xs"></i> Add Next of Kin
    </button>
</div>

@include('admin.staff.tabs._form_open', ['id' => 'kin-form', 'bag' => 'kin', 'store' => route('admin.staff.kin.store', $staff), 'title' => 'Next of Kin'])
    <p class="text-[11px] text-gray-500 mb-3 add-only">The new next of kin becomes <strong>current</strong>. The present one is kept in the history as previous.</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
            <label class="wf-label">Relationship <span class="text-red-500">*</span></label>
            <select name="relationship" class="{{ $input }}" required>
                <option value="">— Select —</option>
                @foreach(\App\Models\NextOfKin::RELATIONSHIPS as $r)
                    <option value="{{ $r }}" {{ $old('relationship') === $r ? 'selected' : '' }}>{{ $r }}</option>
                @endforeach
            </select>
        </div>
        <div><label class="wf-label">Surname <span class="text-red-500">*</span></label><input name="surname" value="{{ $old('surname') }}" class="{{ $input }}" required></div>
        <div><label class="wf-label">First name <span class="text-red-500">*</span></label><input name="firstname" value="{{ $old('firstname') }}" class="{{ $input }}" required></div>
        <div><label class="wf-label">Other names</label><input name="othernames" value="{{ $old('othernames') }}" class="{{ $input }}"></div>
        <div><label class="wf-label">Phone</label><input name="phone" value="{{ $old('phone') }}" class="{{ $input }}"></div>
        <div><label class="wf-label">E-mail</label><input type="email" name="email" value="{{ $old('email') }}" class="{{ $input }}"></div>
        <div class="md:col-span-2"><label class="wf-label">Address</label><input name="address" value="{{ $old('address') }}" class="{{ $input }}"></div>
        <div><label class="wf-label">City</label><input name="city" value="{{ $old('city') }}" class="{{ $input }}"></div>
        <div>
            <label class="wf-label">State</label>
            <select name="state_id" class="{{ $input }}">
                <option value="">— Select —</option>
                @foreach($states as $st)
                    <option value="{{ $st->id }}" {{ (string) $old('state_id') === (string) $st->id ? 'selected' : '' }}>{{ $st->state_name }}</option>
                @endforeach
            </select>
        </div>
    </div>
@include('admin.staff.tabs._form_close')

@if($staff->nextOfKins->count())
    <div class="overflow-x-auto">
    <table class="wf-table">
        <thead><tr><th>NAME</th><th>RELATIONSHIP</th><th>PHONE / E-MAIL</th><th>ADDRESS</th><th>STATUS</th><th class="text-right">ACTIONS</th></tr></thead>
        <tbody>
        @foreach($staff->nextOfKins as $k)
            <tr class="{{ $k->is_current ? '' : 'opacity-70' }}">
                <td class="col-name">{{ $k->surname }}, {{ $k->firstname }} {{ $k->othernames }}</td>
                <td>{{ $k->relationship ?? '—' }}</td>
                <td class="text-xs">{{ $k->phone ?? '—' }}@if($k->email)<br><span class="text-gray-400">{{ $k->email }}</span>@endif</td>
                <td class="text-xs">{{ collect([$k->address, $k->city, $k->state?->state_name])->filter()->implode(', ') ?: '—' }}</td>
                <td>
                    @if($k->is_current)
                        <span class="badge badge-active">Current</span>
                    @else
                        <span class="badge badge-inactive">Previous</span>
                        @if($k->replaced_at)<span class="block text-[10px] text-gray-400">replaced {{ $k->replaced_at->format('d M Y') }}</span>@endif
                    @endif
                </td>
                <td>
                    <div class="flex justify-end gap-1 flex-wrap">
                        <button type="button" class="btn-wf-ghost btn-wf-sm" onclick="openRecordForm('kin-form', this)"
                                data-action="{{ route('admin.staff.kin.update', [$staff, $k]) }}"
                                data-relationship="{{ $k->relationship }}" data-surname="{{ $k->surname }}" data-firstname="{{ $k->firstname }}"
                                data-othernames="{{ $k->othernames }}" data-phone="{{ $k->phone }}" data-email="{{ $k->email }}"
                                data-address="{{ $k->address }}" data-city="{{ $k->city }}" data-state_id="{{ $k->state_id }}">
                            <i class="ti ti-edit text-xs"></i> Edit
                        </button>
                        @unless($k->is_current)
                            <form action="{{ route('admin.staff.kin.current', [$staff, $k]) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="btn-wf-ghost btn-wf-sm" title="Make this the current next of kin"><i class="ti ti-star text-xs"></i> Make current</button>
                            </form>
                        @endunless
                        <form action="{{ route('admin.staff.kin.destroy', [$staff, $k]) }}" method="POST" onsubmit="return confirm('Delete this next of kin record?')">
                            @csrf @method('DELETE')
                            <button class="btn-wf-danger btn-wf-sm"><i class="ti ti-trash text-xs"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
@else
    <p class="text-center text-gray-400 py-8 text-sm">No next of kin on record.</p>
@endif
