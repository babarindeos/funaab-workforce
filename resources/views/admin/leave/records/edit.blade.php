<x-admin-layout title="Edit Leave Record">

    <div class="flex items-center gap-2 mb-5">
        <a href="{{ route('admin.leaves.index') }}" class="text-gray-400 hover:text-funaab-700 transition-colors">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="pg-title">Edit Leave Record</h1>
            <p class="pg-sub">
                {{ $leave->staff->surname ?? '' }}, {{ $leave->staff->firstname ?? '' }}
                — {{ $leave->leaveType->leave_type ?? '' }}
            </p>
        </div>
    </div>

    <div class="max-w-2xl">
        <div class="wf-card">
            <form action="{{ route('admin.leaves.update', $leave) }}" method="POST" class="space-y-4">
                @csrf @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">STAFF MEMBER</label>
                        <select name="staff_id" required
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500">
                            <option value="">— Select Staff —</option>
                            @foreach($staff as $s)
                                <option value="{{ $s->id }}" {{ old('staff_id', $leave->staff_id) == $s->id ? 'selected' : '' }}>
                                    {{ $s->staff_no }} — {{ $s->surname }}, {{ $s->firstname }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">LEAVE TYPE</label>
                        <select name="leave_type_id" required
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500">
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}" {{ old('leave_type_id', $leave->leave_type_id) == $type->id ? 'selected' : '' }}>
                                    {{ $type->leave_type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">LEAVE YEAR</label>
                        <input type="number" name="leave_year"
                               value="{{ old('leave_year', $leave->leave_year) }}"
                               min="2000" max="2099" required
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500" />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">START DATE</label>
                        <input type="date" name="start_date"
                               value="{{ old('start_date', $leave->start_date?->format('Y-m-d')) }}" required
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500" />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">END DATE</label>
                        <input type="date" name="end_date"
                               value="{{ old('end_date', $leave->end_date?->format('Y-m-d')) }}" required
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500" />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">RESUMPTION DATE</label>
                        <input type="date" name="resumption_date"
                               value="{{ old('resumption_date', $leave->resumption_date?->format('Y-m-d')) }}" required
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500" />
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">APPROVAL STATUS</label>
                        <select name="approval_status" required
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500">
                            <option value="pending"  {{ old('approval_status', $leave->approval_status) === 'pending'  ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ old('approval_status', $leave->approval_status) === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ old('approval_status', $leave->approval_status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">REMARK</label>
                        <textarea name="remark" rows="3"
                                  class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500 resize-none">{{ old('remark', $leave->remark) }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <a href="{{ route('admin.leaves.index') }}" class="btn-wf-ghost">Cancel</a>
                    <button type="submit" class="btn-wf-primary">
                        <i class="ti ti-device-floppy text-sm"></i> Update Record
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-admin-layout>