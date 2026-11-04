@php
    $old = fn ($f) => $errors->getBag('deployment')->any() ? old($f) : null;
    $fromDefault = $currentDepartmentId;
@endphp
<div class="flex flex-wrap items-center justify-between gap-2 mb-3">
    <p class="text-[11px] text-gray-500">
        Home department (Employment): <strong>{{ $staff->employment?->department?->department_name ?? 'not set' }}</strong> — not changed by deployments.
    </p>
    <button type="button" class="btn-wf-primary btn-wf-sm" onclick="openRecordForm('deployment-form')">
        <i class="ti ti-plus text-xs"></i> Add Deployment
    </button>
</div>

@include('admin.staff.tabs._form_open', ['id' => 'deployment-form', 'bag' => 'deployment', 'store' => route('admin.staff.deployments.store', $staff), 'title' => 'Deployment'])
    <p class="text-[11px] text-gray-500 mb-3 add-only">Any deployment still ongoing will be ended automatically the day before this one starts.</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
            <label class="wf-label">From department</label>
            <select name="from_department_id" class="{{ $input }}" data-default="{{ $fromDefault }}">
                <option value="">— Not recorded —</option>
                @foreach($departments as $d)
                    <option value="{{ $d->id }}" {{ (string) ($old('from_department_id') ?? $fromDefault) === (string) $d->id ? 'selected' : '' }}>{{ $d->department_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="wf-label">To department <span class="text-red-500">*</span></label>
            <select name="department_id" class="{{ $input }}" required>
                <option value="">— Select —</option>
                @foreach($departments as $d)
                    <option value="{{ $d->id }}" {{ (string) $old('department_id') === (string) $d->id ? 'selected' : '' }}>{{ $d->department_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="wf-label">Post <span class="text-red-500">*</span></label>
            <select name="deployment_post_id" class="{{ $input }}" required>
                <option value="">— Select —</option>
                @foreach($deploymentPosts as $p)
                    <option value="{{ $p->id }}" {{ (string) $old('deployment_post_id') === (string) $p->id ? 'selected' : '' }}>{{ $p->deployment_post }}</option>
                @endforeach
            </select>
            <a href="{{ route('admin.deployment_posts.index') }}" class="text-[10px] text-funaab-700 hover:underline">Manage posts</a>
        </div>
        <div><label class="wf-label">Start date <span class="text-red-500 add-only">*</span></label><input type="date" name="start_date" value="{{ $old('start_date') }}" class="{{ $input }}"></div>
        <div><label class="wf-label">Expected end date</label><input type="date" name="expected_end_date" value="{{ $old('expected_end_date') }}" class="{{ $input }}"></div>
        <div><label class="wf-label">Actual end date</label><input type="date" name="end_date" value="{{ $old('end_date') }}" class="{{ $input }}"><span class="text-[10px] text-gray-400">Leave empty while ongoing</span></div>
        <div class="md:col-span-3"><label class="wf-label">Remark</label><input name="remark" value="{{ $old('remark') }}" class="{{ $input }}"></div>
    </div>
@include('admin.staff.tabs._form_close')

@if($staff->deployments->count())
    <div class="overflow-x-auto">
    <table class="wf-table">
        <thead><tr><th>FROM → TO</th><th>POST</th><th>START</th><th>EXPECTED END</th><th>ACTUAL END</th><th>REMARK</th><th class="text-right">ACTIONS</th></tr></thead>
        <tbody>
        @foreach($staff->deployments->sortByDesc(fn ($d) => ($d->start_date?->format('Y-m-d') ?? '0000') . sprintf('%08d', $d->id)) as $dep)
            <tr>
                <td class="col-name">
                    <span class="text-gray-500 font-normal">{{ $dep->fromDepartment?->department_name ?? '—' }}</span>
                    <i class="ti ti-arrow-right text-xs text-gray-400"></i>
                    {{ $dep->department?->department_name ?? '—' }}
                </td>
                <td>{{ $dep->deploymentPost?->deployment_post ?? '—' }}</td>
                <td class="whitespace-nowrap">{{ $dep->start_date?->format('d M Y') ?? '—' }}</td>
                <td class="whitespace-nowrap">{{ $dep->expected_end_date?->format('d M Y') ?? '—' }}</td>
                <td class="whitespace-nowrap">
                    @if($dep->end_date) {{ $dep->end_date->format('d M Y') }}
                    @else <span class="badge badge-active">Ongoing</span> @endif
                </td>
                <td class="text-gray-400 text-xs">{{ $dep->remark ?? '—' }}</td>
                <td>
                    <div class="flex justify-end gap-1">
                        <button type="button" class="btn-wf-ghost btn-wf-sm" onclick="openRecordForm('deployment-form', this)"
                                data-action="{{ route('admin.staff.deployments.update', [$staff, $dep]) }}"
                                data-from_department_id="{{ $dep->from_department_id }}" data-department_id="{{ $dep->department_id }}"
                                data-deployment_post_id="{{ $dep->deployment_post_id }}" data-start_date="{{ $dep->start_date?->format('Y-m-d') }}"
                                data-expected_end_date="{{ $dep->expected_end_date?->format('Y-m-d') }}" data-end_date="{{ $dep->end_date?->format('Y-m-d') }}"
                                data-remark="{{ $dep->remark }}">
                            <i class="ti ti-edit text-xs"></i> Edit
                        </button>
                        <form action="{{ route('admin.staff.deployments.destroy', [$staff, $dep]) }}" method="POST" onsubmit="return confirm('Delete this deployment from the history?')">
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
    <p class="text-center text-gray-400 py-8 text-sm">No deployment records.</p>
@endif
