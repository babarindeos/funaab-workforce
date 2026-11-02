<x-staff-layout title="Supervisor — Staff List">

    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">{{ $aperForm->name }}</h1>
            <p class="pg-sub">Junior Non-Teaching Staff — {{ $records->pluck('department.department_name')->filter()->unique()->implode(', ') }}</p>
        </div>
        <a href="{{ route('staff.supervisor.index') }}" class="btn-wf-ghost btn-wf-sm">
            <i class="ti ti-arrow-left text-sm"></i> Back
        </a>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-xs font-semibold px-4 py-3 rounded-lg mb-4">
            <i class="ti ti-circle-check text-base"></i> {{ session('success') }}
        </div>
    @endif

    <div class="wf-card">
        <div class="flex items-center justify-between mb-3">
            <div class="wf-card-title mb-0">
                <i class="ti ti-users text-gray-400"></i> Staff Submissions
            </div>
            <span class="text-[10.5px] text-gray-400 font-semibold">
                {{ $staffList->count() }} staff submitted Part B
            </span>
        </div>

        <table class="wf-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>STAFF NAME</th>
                    <th>FILE NO.</th>
                    <th>ASSESSMENT</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staffList as $s)
                    <tr>
                        <td class="text-gray-400 text-[11px]">{{ $loop->iteration }}</td>
                        <td class="col-name">{{ $s->surname }}, {{ $s->firstname }}</td>
                        <td class="col-mono">{{ $s->staff_no }}</td>
                        <td>
                            @if(in_array($s->id, $assessedStaffIds))
                                <span class="badge badge-active">Assessed</span>
                            @else
                                <span class="badge badge-inactive">Pending</span>
                            @endif
                        </td>
                        <td>                            
                            <a href="{{ route('staff.supervisor.view_submission', [$aperForm->uuid, $s->uuid]) }}"
                            class="btn-wf-ghost btn-wf-sm">
                                <i class="ti ti-eye text-xs"></i> Submission
                            </a>

                            <a href="{{ route('staff.supervisor.assess', [$aperForm->uuid, $s->uuid]) }}"
                               class="btn-wf-ghost btn-wf-sm">
                                @if(in_array($s->id, $assessedStaffIds))
                                    <i class="ti ti-eye text-xs"></i> View / Edit
                                @else
                                    <i class="ti ti-pencil text-xs"></i> Assess
                                @endif
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-10 text-gray-400">
                            <i class="ti ti-users text-3xl block mb-2"></i>
                            No Junior Non-Teaching staff have submitted Part B yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-staff-layout>
