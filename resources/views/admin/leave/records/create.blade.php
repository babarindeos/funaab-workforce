<x-admin-layout title="New Leave Record">

    <div class="flex items-center gap-2 mb-5">
        <a href="{{ route('admin.leaves.index') }}" class="text-gray-400 hover:text-funaab-700 transition-colors">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="pg-title">New Leave Record</h1>
            <p class="pg-sub">Create a leave record for a staff member</p>
        </div>
    </div>

    <div class="max-w-2xl">
        <div class="wf-card">
            <form action="{{ route('admin.leaves.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    {{-- Staff --}}
                    <div class="col-span-2">
                        <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">
                            STAFF MEMBER <span class="text-red-500">*</span>
                        </label>
                        <select name="staff_id" required
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                       focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50">
                            <option value="">— Select Staff —</option>
                            @foreach($staff as $s)
                                <option value="{{ $s->id }}" {{ old('staff_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->staff_no }} — {{ $s->surname }}, {{ $s->firstname }}
                                </option>
                            @endforeach
                        </select>
                        @error('staff_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Leave Type --}}
                    <div>
                        <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">
                            LEAVE TYPE <span class="text-red-500">*</span>
                        </label>
                        <select name="leave_type_id" required
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                       focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50">
                            <option value="">— Select Type —</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->leave_type }}
                                </option>
                            @endforeach
                        </select>
                        @error('leave_type_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Leave Year --}}
                    <div>
                        <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">
                            LEAVE YEAR <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="leave_year" value="{{ old('leave_year', date('Y')) }}"
                               min="2000" max="2099" required
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                      focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50" />
                        @error('leave_year')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Start Date --}}
                    <div>
                        <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">
                            START DATE <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" required
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                      focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50" />
                        @error('start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- End Date --}}
                    <div>
                        <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">
                            END DATE <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}" required
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                      focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50" />
                        @error('end_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Resumption Date --}}
                    <div>
                        <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">
                            RESUMPTION DATE <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="resumption_date" value="{{ old('resumption_date') }}" required
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                      focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50" />
                        @error('resumption_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Approval Status --}}
                    <div>
                        <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">
                            APPROVAL STATUS <span class="text-red-500">*</span>
                        </label>
                        <select name="approval_status" required
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                       focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50">
                            <option value="pending"  {{ old('approval_status','pending') === 'pending'  ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ old('approval_status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ old('approval_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        @error('approval_status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Remark --}}
                    <div class="col-span-2">
                        <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">
                            REMARK
                        </label>
                        <textarea name="remark" rows="3"
                                  placeholder="Optional notes or remarks…"
                                  class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                         focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50
                                         resize-none">{{ old('remark') }}</textarea>
                        @error('remark')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <a href="{{ route('admin.leaves.index') }}" class="btn-wf-ghost">Cancel</a>
                    <button type="submit" class="btn-wf-primary">
                        <i class="ti ti-device-floppy text-sm"></i> Save Record
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-admin-layout>