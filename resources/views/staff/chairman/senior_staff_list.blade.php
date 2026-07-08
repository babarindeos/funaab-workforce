<x-staff-layout title="Chairman DAPC — Senior Staff List">

    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">{{ $aperForm->name }}</h1>
            <p class="pg-sub">Senior Non-Teaching Staff recommended for promotion — {{ $chairman->department?->department_name }}</p>
        </div>
        <a href="{{ route('staff.chairman.index') }}" class="btn-wf-ghost btn-wf-sm">
            <i class="ti ti-arrow-left text-sm"></i> Back
        </a>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-xs font-semibold px-4 py-3 rounded-lg mb-4">
            <i class="ti ti-circle-check text-base"></i> {{ session('success') }}
        </div>
    @endif

    <div class="flex items-center gap-2 bg-funaab-50 border border-funaab-100 text-funaab-700 text-xs font-semibold px-4 py-3 rounded-lg mb-4">
        <i class="ti ti-info-circle text-base"></i>
        Only Senior Non-Teaching staff recommended for promotion by their HOD are listed here.
    </div>

    <div class="wf-card">
        <div class="flex items-center justify-between mb-3">
            <div class="wf-card-title mb-0">
                <i class="ti ti-users text-gray-400"></i> Staff for Promotion Scoring
            </div>
            <span class="text-[10.5px] text-gray-400 font-semibold">
                {{ $staffList->count() }} staff recommended for promotion
            </span>
        </div>

        <table class="wf-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>STAFF NAME</th>
                    <th>FILE NO.</th>
                    <th>GRADE LEVEL</th>
                    <th>SCORING</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staffList as $s)
                    @php
                        $personal = \App\Models\AperFormSeniorPersonal::where('aper_form_id', $aperForm->id)
                                        ->where('staff_id', $s->id)->first();
                    @endphp
                    <tr>
                        <td class="text-gray-400 text-[11px]">{{ $loop->iteration }}</td>
                        <td class="col-name">{{ $s->surname }}, {{ $s->firstname }}</td>
                        <td class="col-mono">{{ $s->staff_no }}</td>
                        <td>{{ $personal?->present_grade_level_salary ?? '—' }}</td>
                        <td>
                            @if(in_array($s->id, $scoredStaffIds))
                                <span class="badge badge-active">Scored</span>
                            @else
                                <span class="badge badge-inactive">Pending</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('staff.hod.view_senior_submission', [$aperForm->uuid, $s->uuid]) }}"
                                   class="btn-wf-ghost btn-wf-sm">
                                    <i class="ti ti-eye text-xs"></i> Submission
                                </a>
                                <a href="{{ route('staff.chairman.senior.assess', [$aperForm->uuid, $s->uuid]) }}"
                                   class="btn-wf-ghost btn-wf-sm">
                                    @if(in_array($s->id, $scoredStaffIds))
                                        <i class="ti ti-pencil text-xs"></i> View / Edit
                                    @else
                                        <i class="ti ti-pencil text-xs"></i> Score
                                    @endif
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-gray-400">
                            <i class="ti ti-users text-3xl block mb-2"></i>
                            No Senior Non-Teaching staff have been recommended for promotion yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-staff-layout>
