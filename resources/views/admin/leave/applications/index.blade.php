<x-admin-layout title="Leave Applications">

    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">Leave Applications</h1>
            <p class="pg-sub">Review, approve or reject staff leave applications</p>
        </div>
    </div>

    {{-- Status filter tabs --}}
    <div class="flex gap-2 mb-4 flex-wrap">
        <a href="{{ route('admin.leave_applications.index') }}"
           class="px-4 py-2 rounded-lg text-xs font-bold border transition-colors
                  {{ $status === 'all' ? 'bg-funaab-700 text-white border-funaab-700' : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50' }}">
            All Applications
        </a>
        <a href="{{ route('admin.leave_applications.index', ['status' => 'pending']) }}"
           class="px-4 py-2 rounded-lg text-xs font-bold border transition-colors flex items-center gap-1.5
                  {{ $status === 'pending' ? 'bg-yellow-500 text-white border-yellow-500' : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50' }}">
            Pending
            @if($pendingCount)
                <span class="bg-white/30 px-1.5 py-0.5 rounded-full text-[10px]">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.leave_applications.index', ['status' => 'approved']) }}"
           class="px-4 py-2 rounded-lg text-xs font-bold border transition-colors
                  {{ $status === 'approved' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50' }}">
            Approved
        </a>
        <a href="{{ route('admin.leave_applications.index', ['status' => 'rejected']) }}"
           class="px-4 py-2 rounded-lg text-xs font-bold border transition-colors
                  {{ $status === 'rejected' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50' }}">
            Rejected
        </a>
    </div>

    {{-- Applications table --}}
    <div class="wf-card">
        <table class="wf-table">
            <thead>
                <tr>
                    <th>STAFF ID</th>
                    <th>APPLICANT</th>
                    <th>DEPARTMENT</th>
                    <th>LEAVE TYPE</th>
                    <th>FROM</th>
                    <th>TO</th>
                    <th>DAYS</th>
                    <th>APPLIED</th>
                    <th>STATUS</th>
                    <th class="text-right">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                    <tr>
                        <td class="col-mono">{{ $app->staff->staff_no ?? '—' }}</td>
                        <td class="col-name">
                            {{ $app->staff->surname ?? '—' }}, {{ $app->staff->firstname ?? '' }}
                        </td>
                        <td class="text-gray-500 text-xs">
                            {{ $app->staff->employment->department->department_name ?? '—' }}
                        </td>
                        <td>{{ $app->leaveType->leave_type ?? '—' }}</td>
                        <td>{{ $app->start_date?->format('d M Y') }}</td>
                        <td>{{ $app->end_date?->format('d M Y') }}</td>
                        <td class="font-bold text-gray-700">{{ $app->total_days }}</td>
                        <td class="text-gray-400 text-xs">{{ $app->created_at->format('d M Y') }}</td>
                        <td>
                            @if($app->approval_status === 'approved')
                                <span class="badge badge-approved">Approved</span>
                            @elseif($app->approval_status === 'rejected')
                                <span class="badge badge-rejected">Rejected</span>
                            @else
                                <span class="badge badge-pending">Pending</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-1">

                                <a href="{{ route('admin.leave_applications.show', $app) }}"
                                   class="btn-wf-ghost btn-wf-sm">
                                    <i class="ti ti-eye text-xs"></i>
                                </a>

                                @if($app->approval_status === 'pending')
                                    {{-- Approve --}}
                                    <form action="{{ route('admin.leave_applications.approve', $app) }}"
                                          method="POST">
                                        @csrf @method('PATCH')
                                        <button class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md
                                                       bg-funaab-700 text-white text-[11px] font-bold
                                                       hover:bg-funaab-600 transition-colors">
                                            <i class="ti ti-check text-xs"></i> Approve
                                        </button>
                                    </form>

                                    {{-- Reject --}}
                                    <button onclick="openReject({{ $app->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md
                                                   bg-red-50 text-red-600 text-[11px] font-bold border border-red-200
                                                   hover:bg-red-100 transition-colors">
                                        <i class="ti ti-x text-xs"></i> Reject
                                    </button>
                                @endif

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-10 text-gray-400">
                            <i class="ti ti-clipboard-text text-3xl block mb-2"></i>
                            No applications found for this filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($applications->hasPages())
            <div class="mt-4 pt-3 border-t border-gray-100">
                {{ $applications->links() }}
            </div>
        @endif
    </div>

    {{-- Reject modal --}}
    <div id="modal-reject" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                    <i class="ti ti-alert-circle text-red-500"></i> Reject Application
                </h2>
                <button onclick="document.getElementById('modal-reject').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
            </div>
            <form id="reject-form" method="POST" class="px-5 py-4">
                @csrf @method('PATCH')
                <p class="text-sm text-gray-500 mb-3">
                    Optionally provide a reason for rejecting this application.
                </p>
                <div class="mb-4">
                    <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">REASON / REMARK</label>
                    <textarea name="remark" rows="3"
                              placeholder="Optional reason for rejection…"
                              class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                     focus:outline-none focus:border-red-400 resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button"
                            onclick="document.getElementById('modal-reject').classList.add('hidden')"
                            class="btn-wf-ghost">Cancel</button>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-red-600 text-white
                                   text-xs font-bold rounded-md hover:bg-red-700 transition-colors">
                        <i class="ti ti-x text-sm"></i> Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openReject(id) {
            document.getElementById('reject-form').action = `/admin/leave-applications/${id}/reject`;
            document.getElementById('modal-reject').classList.remove('hidden');
        }
    </script>

</x-admin-layout>
