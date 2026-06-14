<x-settings-page title="Edit Deployment"
    subtitle="{{ $deployment->staff?->surname }}, {{ $deployment->staff?->firstname }} — {{ $deployment->deploymentPost?->deployment_post }}"
    back-route="admin.deployments.index">

    <div class="max-w-2xl">
        <div class="wf-card">
            <form action="{{ route('admin.deployments.update', $deployment) }}" method="POST" class="space-y-4">
                @csrf @method('PUT')
                @php $input = 'w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500';
                     $label = 'block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5'; @endphp

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="{{ $label }}">STAFF MEMBER</label>
                        <select name="staff_id" class="{{ $input }}" required>
                            @foreach($staff as $s)
                                <option value="{{ $s->id }}" {{ old('staff_id', $deployment->staff_id) == $s->id ? 'selected' : '' }}>
                                    {{ $s->staff_no }} — {{ $s->surname }}, {{ $s->firstname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $label }}">DEPARTMENT</label>
                        <select name="department_id" class="{{ $input }}" required>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $deployment->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->department_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $label }}">DEPLOYMENT POST</label>
                        <select name="deployment_post_id" class="{{ $input }}" required>
                            @foreach($deploymentPosts as $post)
                                <option value="{{ $post->id }}" {{ old('deployment_post_id', $deployment->deployment_post_id) == $post->id ? 'selected' : '' }}>
                                    {{ $post->deployment_post }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $label }}">START DATE</label>
                        <input type="date" name="start_date"
                               value="{{ old('start_date', $deployment->start_date?->format('Y-m-d')) }}"
                               class="{{ $input }}" required />
                    </div>
                    <div>
                        <label class="{{ $label }}">EXPECTED END DATE</label>
                        <input type="date" name="expected_end_date"
                               value="{{ old('expected_end_date', $deployment->expected_end_date?->format('Y-m-d')) }}"
                               class="{{ $input }}" />
                    </div>
                    <div>
                        <label class="{{ $label }}">ACTUAL END DATE</label>
                        <input type="date" name="end_date"
                               value="{{ old('end_date', $deployment->end_date?->format('Y-m-d')) }}"
                               class="{{ $input }}" />
                    </div>
                    <div class="col-span-2">
                        <label class="{{ $label }}">REMARK</label>
                        <textarea name="remark" rows="3" class="{{ $input }} resize-none">{{ old('remark', $deployment->remark) }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <a href="{{ route('admin.deployments.index') }}" class="btn-wf-ghost">Cancel</a>
                    <button type="submit" class="btn-wf-primary">
                        <i class="ti ti-device-floppy text-sm"></i> Update Deployment
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-settings-page>
