<x-settings-page title="Deployments" subtitle="Staff deployment records across departments and posts"
    back-route="admin.settings.index"
    add-label="New Deployment"
    add-route="admin.deployments.create">

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-3 mb-4">
        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-funaab-50 flex items-center justify-center">
                    <i class="ti ti-arrows-exchange text-funaab-700 text-sm"></i>
                </div>
                TOTAL DEPLOYMENTS
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $total }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-green-50 flex items-center justify-center">
                    <i class="ti ti-user-check text-green-600 text-sm"></i>
                </div>
                ONGOING
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $ongoing }}</div>
            <div class="text-[10.5px] text-green-600 mt-1">No end date set</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-gray-50 flex items-center justify-center">
                    <i class="ti ti-check text-gray-500 text-sm"></i>
                </div>
                COMPLETED
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $total - $ongoing }}</div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex gap-2 mb-4 flex-wrap">
        <div class="relative flex-1 min-w-[200px]">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by staff name or ID…"
                   class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg
                          focus:outline-none focus:border-funaab-500 bg-white" />
        </div>
        <select name="department_id"
                class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none
                       focus:border-funaab-500 bg-white">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                    {{ $dept->department_name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn-wf-primary">
            <i class="ti ti-filter text-sm"></i> Filter
        </button>
        @if(request()->hasAny(['search','department_id']))
            <a href="{{ route('admin.deployments.index') }}" class="btn-wf-ghost">Clear</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="wf-card">
        <table class="wf-table">
            <thead>
                <tr>
                    <th>STAFF ID</th>
                    <th>STAFF NAME</th>
                    <th>DEPARTMENT</th>
                    <th>POST</th>
                    <th>START DATE</th>
                    <th>EXPECTED END</th>
                    <th>ACTUAL END</th>
                    <th>STATUS</th>
                    <th class="text-right">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deployments as $dep)
                <tr>
                    <td class="col-mono">{{ $dep->staff?->staff_no ?? '—' }}</td>
                    <td class="col-name">
                        {{ $dep->staff?->surname }}, {{ $dep->staff?->firstname }}
                    </td>
                    <td>{{ $dep->department?->department_name ?? '—' }}</td>
                    <td>{{ $dep->deploymentPost?->deployment_post ?? '—' }}</td>
                    <td>{{ $dep->start_date?->format('d M Y') ?? '—' }}</td>
                    <td>{{ $dep->expected_end_date?->format('d M Y') ?? '—' }}</td>
                    <td>{{ $dep->end_date?->format('d M Y') ?? '—' }}</td>
                    <td>
                        @if($dep->end_date)
                            <span class="badge badge-inactive">Completed</span>
                        @else
                            <span class="badge badge-active">Ongoing</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.deployments.show', $dep) }}"
                               class="btn-wf-ghost btn-wf-sm">
                                <i class="ti ti-eye text-xs"></i>
                            </a>
                            <a href="{{ route('admin.deployments.edit', $dep) }}"
                               class="btn-wf-ghost btn-wf-sm">
                                <i class="ti ti-edit text-xs"></i>
                            </a>
                            <form action="{{ route('admin.deployments.delete', $dep) }}" method="POST"
                                  onsubmit="return confirm('Delete this deployment record?')">
                                @csrf @method('DELETE')
                                <button class="btn-wf-danger btn-wf-sm">
                                    <i class="ti ti-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-10 text-gray-400">
                        <i class="ti ti-arrows-exchange text-3xl block mb-2"></i>
                        No deployment records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($deployments->hasPages())
            <div class="p-3 border-t border-gray-100 flex items-center justify-between">
                <p class="text-xs text-gray-400">
                    Showing {{ $deployments->firstItem() }}–{{ $deployments->lastItem() }}
                    of {{ $deployments->total() }} records
                </p>
                {{ $deployments->links() }}
            </div>
        @endif
    </div>

</x-settings-page>
