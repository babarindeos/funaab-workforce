<x-admin-layout title="Leave Records">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">Leave Records</h1>
            <p class="pg-sub">Active and historical leave records for all staff</p>
        </div>
        <a href="{{ route('admin.leaves.create') }}" class="btn-wf-primary">
            <i class="ti ti-plus text-sm"></i> New Record
        </a>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-3 gap-3 mb-4">
        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-gold-50 flex items-center justify-center">
                    <i class="ti ti-calendar-off text-gold-600 text-sm"></i>
                </div>
                CURRENTLY ON LEAVE
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $totalOnLeave }}</div>
            <div class="text-[10.5px] text-gray-400 mt-1">Active approved leaves</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-funaab-50 flex items-center justify-center">
                    <i class="ti ti-calendar-check text-funaab-600 text-sm"></i>
                </div>
                RESUMING THIS WEEK
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $resumingThisWeek }}</div>
            <div class="text-[10.5px] text-gray-400 mt-1">Within the next 7 days</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-blue-50 flex items-center justify-center">
                    <i class="ti ti-calendar-stats text-blue-600 text-sm"></i>
                </div>
                LEAVE THIS YEAR
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $totalThisYear }}</div>
            <div class="text-[10.5px] text-gray-400 mt-1">Total records — {{ now()->year }}</div>
        </div>
    </div>

    {{-- Countdown cards: staff currently on leave --}}
    @if($onLeave->count())
    <div class="wf-card mb-4">
        <div class="flex items-center justify-between mb-3">
            <div class="wf-card-title mb-0">
                <i class="ti ti-clock-hour-4 text-gray-400"></i>
                Staff Currently on Leave — Days to Resumption
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
            @foreach($onLeave as $leave)
                @php
                    $days = $leave->days_remaining;
                    $urgent = $days <= 3;
                @endphp
                <div class="rounded-lg border p-3 border-l-4
                            {{ $urgent ? 'border-red-200 border-l-red-400 bg-red-50/40' : 'border-gray-100 border-l-funaab-400 bg-white' }}">
                    <div class="text-xs font-bold text-gray-800 truncate">
                        {{ $leave->staff->surname ?? '—' }}, {{ $leave->staff->firstname ?? '' }}
                    </div>
                    <div class="text-[10.5px] text-gray-400 mt-0.5 truncate">
                        {{ $leave->leaveType->leave_type ?? '—' }}
                    </div>
                    <div class="text-xl font-bold mt-1.5 {{ $urgent ? 'text-red-600' : 'text-funaab-700' }}">
                        {{ $days }}
                        <span class="text-xs font-normal text-gray-400">
                            {{ $days == 1 ? 'day' : 'days' }} left
                        </span>
                    </div>
                    <div class="text-[10px] mt-1 {{ $urgent ? 'text-red-500 font-bold' : 'text-gray-400' }}">
                        Resumes: {{ $leave->resumption_date?->format('d M Y') }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Filters --}}
    <form method="GET" class="flex gap-2 mb-3 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search name or staff ID…"
               class="flex-1 min-w-[200px] px-3 py-2 text-sm border border-gray-200 rounded-lg
                      focus:outline-none focus:border-funaab-500 bg-white" />
        <select name="leave_type_id"
                class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none
                       focus:border-funaab-500 bg-white">
            <option value="">All Leave Types</option>
            @foreach($leaveTypes as $type)
                <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>
                    {{ $type->leave_type }}
                </option>
            @endforeach
        </select>
        <select name="status"
                class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none
                       focus:border-funaab-500 bg-white">
            <option value="">All Statuses</option>
            <option value="pending"  {{ request('status') == 'pending'  ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
        <button type="submit" class="btn-wf-primary">
            <i class="ti ti-search text-sm"></i> Filter
        </button>
        @if(request()->hasAny(['search','leave_type_id','status']))
            <a href="{{ route('admin.leaves.index') }}" class="btn-wf-ghost">Clear</a>
        @endif
    </form>

    {{-- Records table --}}
    <div class="wf-card">
        <table class="wf-table">
            <thead>
                <tr>
                    <th>STAFF ID</th>
                    <th>NAME</th>
                    <th>LEAVE TYPE</th>
                    <th>START DATE</th>
                    <th>END DATE</th>
                    <th>RESUMPTION</th>
                    <th>DAYS</th>
                    <th>YEAR</th>
                    <th>STATUS</th>
                    <th>REMARK</th>
                    <th class="text-right">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaves as $leave)
                    <tr>
                        <td class="col-mono">{{ $leave->staff->staff_no ?? '—' }}</td>
                        <td class="col-name">
                            {{ $leave->staff->surname ?? '—' }}, {{ $leave->staff->firstname ?? '' }}
                        </td>
                        <td>{{ $leave->leaveType->leave_type ?? '—' }}</td>
                        <td>{{ $leave->start_date?->format('d M Y') }}</td>
                        <td>{{ $leave->end_date?->format('d M Y') }}</td>
                        <td>{{ $leave->resumption_date?->format('d M Y') }}</td>
                        <td class="font-bold text-gray-700">{{ $leave->total_days }}</td>
                        <td>{{ $leave->leave_year }}</td>
                        <td>
                            @if($leave->approval_status === 'approved')
                                <span class="badge badge-approved">Approved</span>
                            @elseif($leave->approval_status === 'rejected')
                                <span class="badge badge-rejected">Rejected</span>
                            @else
                                <span class="badge badge-pending">Pending</span>
                            @endif
                        </td>
                        <td class="text-gray-400 max-w-[120px] truncate" title="{{ $leave->remark }}">
                            {{ $leave->remark ?? '—' }}
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.leaves.show', $leave) }}"
                                   class="btn-wf-ghost btn-wf-sm">
                                    <i class="ti ti-eye text-xs"></i>
                                </a>
                                <a href="{{ route('admin.leaves.edit', $leave) }}"
                                   class="btn-wf-ghost btn-wf-sm">
                                    <i class="ti ti-edit text-xs"></i>
                                </a>
                                <form action="{{ route('admin.leaves.destroy', $leave) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete this record?')">
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
                        <td colspan="11" class="text-center py-10 text-gray-400">
                            <i class="ti ti-calendar-off text-3xl block mb-2"></i>
                            No leave records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($leaves->hasPages())
            <div class="mt-4 pt-3 border-t border-gray-100">
                {{ $leaves->links() }}
            </div>
        @endif
    </div>

</x-admin-layout>
