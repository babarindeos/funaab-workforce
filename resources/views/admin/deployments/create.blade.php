<x-settings-page title="New Deployment" subtitle="Record a new staff deployment"
    back-route="admin.deployments.index">

    <div class="max-w-2xl">
        <div class="wf-card">
            <form action="{{ route('admin.deployments.store') }}" method="POST" class="space-y-4">
                @csrf
                @php $input = 'w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50';
                     $label = 'block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5'; @endphp

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="{{ $label }}">STAFF MEMBER <span class="text-red-500">*</span></label>
                        <select name="staff_id" class="{{ $input }}" required>
                            <option value="">— Select Staff —</option>
                            @foreach($staff as $s)
                                <option value="{{ $s->id }}" {{ old('staff_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->staff_no }} — {{ $s->surname }}, {{ $s->firstname }}
                                </option>
                            @endforeach
                        </select>
                        @error('staff_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">DEPARTMENT <span class="text-red-500">*</span></label>
                        <select name="department_id" class="{{ $input }}" required>
                            <option value="">— Select Department —</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->department_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">DEPLOYMENT POST <span class="text-red-500">*</span></label>
                        <select name="deployment_post_id" class="{{ $input }}" required>
                            <option value="">— Select Post —</option>
                            @foreach($deploymentPosts as $post)
                                <option value="{{ $post->id }}" {{ old('deployment_post_id') == $post->id ? 'selected' : '' }}>
                                    {{ $post->deployment_post }}
                                </option>
                            @endforeach
                        </select>
                        @error('deployment_post_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">START DATE <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" class="{{ $input }}" required />
                        @error('start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">EXPECTED END DATE</label>
                        <input type="date" name="expected_end_date" value="{{ old('expected_end_date') }}" class="{{ $input }}" />
                    </div>

                    <div>
                        <label class="{{ $label }}">ACTUAL END DATE</label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}" class="{{ $input }}" />
                        <p class="text-[10.5px] text-gray-400 mt-1">Leave blank if deployment is still ongoing.</p>
                    </div>

                    <div class="col-span-2">
                        <label class="{{ $label }}">REMARK</label>
                        <textarea name="remark" rows="3"
                                  class="{{ $input }} resize-none"
                                  placeholder="Optional remarks…">{{ old('remark') }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <a href="{{ route('admin.deployments.index') }}" class="btn-wf-ghost">Cancel</a>
                    <button type="submit" class="btn-wf-primary">
                        <i class="ti ti-device-floppy text-sm"></i> Save Deployment
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-settings-page>
