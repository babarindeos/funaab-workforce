<x-admin-layout title="Staff Management">

    {{-- Page Header --}}
    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">Staff Management</h1>
            <p class="pg-sub">Federal University of Agriculture, Abeokuta — {{ $total }} total records</p>
        </div>
        <a href="{{ route('admin.staff.create') }}" class="btn-wf-primary">
            <i class="ti ti-plus text-sm"></i> Add Staff
        </a>
    </div>

    {{-- ── Summary Cards ── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-funaab-50 flex items-center justify-center">
                    <i class="ti ti-users text-funaab-700 text-sm"></i>
                </div>
                TOTAL STAFF
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ number_format($total) }}</div>
            <div class="text-[10.5px] text-gray-400 mt-1">All records</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-green-50 flex items-center justify-center">
                    <i class="ti ti-user-check text-green-600 text-sm"></i>
                </div>
                ACTIVE
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ number_format($totalActive) }}</div>
            <div class="text-[10.5px] text-green-600 mt-1">Currently serving</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-gold-50 flex items-center justify-center">
                    <i class="ti ti-calendar-off text-gold-600 text-sm"></i>
                </div>
                ON LEAVE
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ number_format($totalOnLeave) }}</div>
            <div class="text-[10.5px] text-gray-400 mt-1">Currently on leave</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-blue-50 flex items-center justify-center">
                    <i class="ti ti-building text-blue-600 text-sm"></i>
                </div>
                DEPARTMENTS
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ number_format($departments->count()) }}</div>
            <div class="text-[10.5px] text-gray-400 mt-1">Across all faculties</div>
        </div>
    </div>

    {{-- ── Filters ── --}}
    <form method="GET" class="flex gap-2 mb-4 flex-wrap">
        <div class="relative flex-1 min-w-[200px]">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search name or staff ID…"
                   class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg
                          focus:outline-none focus:border-funaab-500 bg-white" />
        </div>
        <select name="department_id"
                class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-funaab-500 bg-white">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                    {{ $dept->department_name }}
                </option>
            @endforeach
        </select>
        <select name="category_id"
                class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-funaab-500 bg-white">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->category }}
                </option>
            @endforeach
        </select>
        <select name="status_id"
                class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-funaab-500 bg-white">
            <option value="">All Statuses</option>
            @foreach($statuses as $status)
                <option value="{{ $status->id }}" {{ request('status_id') == $status->id ? 'selected' : '' }}>
                    {{ $status->status }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn-wf-primary">
            <i class="ti ti-filter text-sm"></i> Filter
        </button>
        @if(request()->hasAny(['search','department_id','category_id','status_id']))
            <a href="{{ route('admin.staff.index') }}" class="btn-wf-ghost">Clear</a>
        @endif
    </form>

    {{-- ── Staff Table ── --}}
    <div class="wf-card">
        <table class="wf-table">
            <thead>
                <tr>
                    <th>STAFF ID</th>
                    <th>NAME</th>
                    <th>DEPARTMENT</th>
                    <th>CATEGORY</th>
                    <th>EMPLOYMENT TYPE</th>
                    <th>STATUS</th>
                    <th class="text-right">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staffs as $staff)
                    <tr>
                        <td class="col-mono">{{ $staff->staff_no }}</td>
                        <td>
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-funaab-50 flex items-center justify-center
                                            text-[10px] font-bold text-funaab-700 flex-shrink-0">
                                    {{ strtoupper(substr($staff->surname,0,1)) }}{{ strtoupper(substr($staff->firstname,0,1)) }}
                                </div>
                                <div class="text-xs font-bold text-gray-800">
                                    {{ $staff->title?->title }} {{ $staff->surname }}, {{ $staff->firstname }}
                                    @if($staff->othernames) {{ $staff->othernames }} @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $staff->employment?->department?->department_name ?? '—' }}</td>
                        <td>{{ $staff->employment?->staffCategory?->category ?? '—' }}</td>
                        <td>{{ $staff->employment?->employmentType?->employment_type ?? '—' }}</td>
                        <td>
                            @php $s = $staff->staffStatus?->status; @endphp
                            @if(str_contains(strtolower($s ?? ''), 'active'))
                                <span class="badge badge-active">{{ $s }}</span>
                            @elseif(str_contains(strtolower($s ?? ''), 'leave'))
                                <span class="badge badge-leave">{{ $s }}</span>
                            @else
                                <span class="badge badge-inactive">{{ $s ?? '—' }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.staff.show', $staff) }}"
                                   class="btn-wf-ghost btn-wf-sm">
                                    <i class="ti ti-eye text-xs"></i> View
                                </a>
                                <a href="{{ route('admin.staff.edit', $staff) }}"
                                   class="btn-wf-ghost btn-wf-sm">
                                    <i class="ti ti-edit text-xs"></i>
                                </a>
                                <form action="{{ route('admin.staff.destroy', $staff) }}" method="POST"
                                      onsubmit="return confirm('Delete {{ addslashes($staff->surname) }}? This cannot be undone.')">
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
                        <td colspan="7" class="text-center py-12 text-gray-400">
                            <i class="ti ti-users text-4xl block mb-2"></i>
                            No staff records found.
                            <a href="{{ route('admin.staff.create') }}" class="text-funaab-700 font-bold ml-1">Add one →</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($staffs->hasPages())
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                <p class="text-xs text-gray-400">
                    Showing {{ $staffs->firstItem() }}–{{ $staffs->lastItem() }} of {{ $staffs->total() }} records
                </p>
                {{ $staffs->links() }}
            </div>
        @endif
    </div>

</x-admin-layout>