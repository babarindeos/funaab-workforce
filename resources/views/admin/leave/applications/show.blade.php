<x-admin-layout title="Application Detail">

    <div class="flex items-center gap-2 mb-5">
        <a href="{{ route('admin.leave_applications.index') }}" class="text-gray-400 hover:text-funaab-700">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="pg-title">Application Detail</h1>
            <p class="pg-sub">Leave application #{{ $leave->id }}</p>
        </div>
        @if($leave->approval_status === 'pending')
            <div class="ml-auto flex gap-2">
                <form action="{{ route('admin.leave_applications.approve', $leave) }}" method="POST">
                    @csrf @method('PATCH')
                    <button class="btn-wf-primary">
                        <i class="ti ti-check text-sm"></i> Approve
                    </button>
                </form>
                <button onclick="document.getElementById('modal-reject').classList.remove('hidden')"
                        class="btn-wf-danger">
                    <i class="ti ti-x text-sm"></i> Reject
                </button>
            </div>
        @endif
    </div>

    <div class="max-w-2xl space-y-4">

        {{-- Staff banner --}}
        <div class="wf-card bg-gradient-to-r from-funaab-800 to-funaab-600 text-white border-0">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-gold-500 flex items-center justify-center text-funaab-900 font-bold text-lg flex-shrink-0">
                    {{ strtoupper(substr($leave->staff->surname ?? 'S', 0, 1)) }}{{ strtoupper(substr($leave->staff->firstname ?? 'S', 0, 1)) }}
                </div>
                <div class="flex-1">
                    <div class="font-bold text-base">{{ $leave->staff->surname ?? '—' }}, {{ $leave->staff->firstname ?? '' }}</div>
                    <div class="text-white/70 text-xs font-mono mt-0.5">{{ $leave->staff->staff_no ?? '—' }}</div>
                    <div class="text-white/60 text-xs mt-0.5">{{ $leave->staff->employment->department->department_name ?? '' }}</div>
                </div>
                <div>
                    @if($leave->approval_status === 'approved')
                        <span class="badge badge-approved">Approved</span>
                    @elseif($leave->approval_status === 'rejected')
                        <span class="badge badge-rejected">Rejected</span>
                    @else
                        <span class="badge badge-pending">Pending Review</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="wf-card">
            <div class="wf-card-title"><i class="ti ti-calendar-off text-gray-400"></i> Application Details</div>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-gray-50 rounded-lg p-3"><div class="text-[10.5px] text-gray-400 font-bold tracking-wider mb-1">LEAVE TYPE</div><div class="text-sm font-bold text-gray-800">{{ $leave->leaveType->leave_type ?? '—' }}</div></div>
                <div class="bg-gray-50 rounded-lg p-3"><div class="text-[10.5px] text-gray-400 font-bold tracking-wider mb-1">LEAVE YEAR</div><div class="text-sm font-bold text-gray-800">{{ $leave->leave_year }}</div></div>
                <div class="bg-gray-50 rounded-lg p-3"><div class="text-[10.5px] text-gray-400 font-bold tracking-wider mb-1">START DATE</div><div class="text-sm font-bold text-gray-800">{{ $leave->start_date?->format('d M Y') }}</div></div>
                <div class="bg-gray-50 rounded-lg p-3"><div class="text-[10.5px] text-gray-400 font-bold tracking-wider mb-1">END DATE</div><div class="text-sm font-bold text-gray-800">{{ $leave->end_date?->format('d M Y') }}</div></div>
                <div class="bg-gray-50 rounded-lg p-3"><div class="text-[10.5px] text-gray-400 font-bold tracking-wider mb-1">RESUMPTION</div><div class="text-sm font-bold text-gray-800">{{ $leave->resumption_date?->format('d M Y') }}</div></div>
                <div class="bg-funaab-50 rounded-lg p-3 border border-funaab-100"><div class="text-[10.5px] text-funaab-600 font-bold tracking-wider mb-1">TOTAL DAYS</div><div class="text-2xl font-bold text-funaab-800">{{ $leave->total_days }}</div></div>
                @if($leave->remark)
                    <div class="col-span-2 bg-gray-50 rounded-lg p-3"><div class="text-[10.5px] text-gray-400 font-bold tracking-wider mb-1">REMARK</div><div class="text-sm text-gray-700">{{ $leave->remark }}</div></div>
                @endif
                <div class="bg-gray-50 rounded-lg p-3"><div class="text-[10.5px] text-gray-400 font-bold tracking-wider mb-1">DATE APPLIED</div><div class="text-sm font-bold text-gray-800">{{ $leave->created_at->format('d M Y, h:i A') }}</div></div>
            </div>
        </div>
    </div>

    {{-- Reject modal --}}
    <div id="modal-reject" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Reject Application</h2>
                <button onclick="document.getElementById('modal-reject').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
            </div>
            <form action="{{ route('admin.leave_applications.reject', $leave) }}" method="POST" class="px-5 py-4">
                @csrf @method('PATCH')
                <div class="mb-4">
                    <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">REASON / REMARK</label>
                    <textarea name="remark" rows="3" placeholder="Optional reason…"
                              class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-red-400 resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-reject').classList.add('hidden')" class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-red-600 text-white text-xs font-bold rounded-md hover:bg-red-700">
                        <i class="ti ti-x text-sm"></i> Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-admin-layout>
