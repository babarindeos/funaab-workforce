<x-admin-layout title="Leave Record Detail">

    <div class="flex items-center gap-2 mb-5">
        <a href="{{ route('admin.leaves.index') }}" class="text-gray-400 hover:text-funaab-700">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="pg-title">Leave Record Detail</h1>
            <p class="pg-sub">Ref #{{ $leave->id }} — {{ $leave->leaveType->leave_type ?? '—' }}</p>
        </div>
        <div class="ml-auto flex gap-2">
            <a href="{{ route('admin.leaves.edit', $leave) }}" class="btn-wf-ghost">
                <i class="ti ti-edit text-sm"></i> Edit
            </a>
        </div>
    </div>

    <div class="max-w-2xl space-y-4">

        {{-- Staff info banner --}}
        <div class="wf-card bg-gradient-to-r from-funaab-800 to-funaab-600 text-white border-0">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-gold-500 flex items-center justify-center
                            text-funaab-900 font-bold text-lg flex-shrink-0">
                    {{ strtoupper(substr($leave->staff->surname ?? 'S', 0, 1)) }}{{ strtoupper(substr($leave->staff->firstname ?? 'S', 0, 1)) }}
                </div>
                <div>
                    <div class="font-bold text-base">
                        {{ $leave->staff->surname ?? '—' }}, {{ $leave->staff->firstname ?? '' }}
                    </div>
                    <div class="text-white/70 text-xs mt-0.5 font-mono">
                        {{ $leave->staff->staff_no ?? '—' }}
                    </div>
                </div>
                <div class="ml-auto">
                    @if($leave->approval_status === 'approved')
                        <span class="badge badge-approved">Approved</span>
                    @elseif($leave->approval_status === 'rejected')
                        <span class="badge badge-rejected">Rejected</span>
                    @else
                        <span class="badge badge-pending">Pending</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Leave details --}}
        <div class="wf-card">
            <div class="wf-card-title"><i class="ti ti-calendar-off text-gray-400"></i> Leave Details</div>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-[10.5px] text-gray-400 font-bold tracking-wider mb-1">LEAVE TYPE</div>
                    <div class="text-sm text-gray-800 font-bold">{{ $leave->leaveType->leave_type ?? '—' }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-[10.5px] text-gray-400 font-bold tracking-wider mb-1">LEAVE YEAR</div>
                    <div class="text-sm text-gray-800 font-bold">{{ $leave->leave_year }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-[10.5px] text-gray-400 font-bold tracking-wider mb-1">START DATE</div>
                    <div class="text-sm text-gray-800 font-bold">{{ $leave->start_date?->format('d M Y') }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-[10.5px] text-gray-400 font-bold tracking-wider mb-1">END DATE</div>
                    <div class="text-sm text-gray-800 font-bold">{{ $leave->end_date?->format('d M Y') }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-[10.5px] text-gray-400 font-bold tracking-wider mb-1">RESUMPTION DATE</div>
                    <div class="text-sm text-gray-800 font-bold">{{ $leave->resumption_date?->format('d M Y') }}</div>
                </div>
                <div class="bg-funaab-50 rounded-lg p-3 border border-funaab-100">
                    <div class="text-[10.5px] text-funaab-600 font-bold tracking-wider mb-1">TOTAL DAYS</div>
                    <div class="text-2xl text-funaab-800 font-bold">{{ $leave->total_days }}</div>
                </div>
                @if($leave->remark)
                <div class="col-span-2 bg-gray-50 rounded-lg p-3">
                    <div class="text-[10.5px] text-gray-400 font-bold tracking-wider mb-1">REMARK</div>
                    <div class="text-sm text-gray-700">{{ $leave->remark }}</div>
                </div>
                @endif
            </div>
        </div>

    </div>

</x-admin-layout>