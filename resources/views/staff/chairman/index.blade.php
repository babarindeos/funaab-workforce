<x-staff-layout title="Chairman DAPC — APER Assessment">

    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">Chairman DAPC Assessment</h1>
            <p class="pg-sub">APER forms available for your assessment — {{ $chairman->department?->department_name }}</p>
        </div>
    </div>

    <div class="wf-card">
        <div class="wf-card-title mb-3">
            <i class="ti ti-clipboard-list text-gray-400"></i> APER Forms
        </div>
        <table class="wf-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>NAME</th>
                    <th>START DATE</th>
                    <th>END DATE</th>
                    <th>STATUS</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
                @forelse($aperForms as $form)
                    @php $isOpen = \Carbon\Carbon::today()->between($form->start_date, $form->end_date); @endphp
                    <tr>
                        <td class="text-gray-400 text-[11px]">{{ $loop->iteration }}</td>
                        <td class="col-name">{{ $form->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($form->start_date)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($form->end_date)->format('d M Y') }}</td>
                        <td>
                            @if($isOpen)
                                <span class="badge badge-active">Opened</span>
                            @else
                                <span class="badge badge-inactive">Closed</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('staff.chairman.staff_list', $form->uuid) }}"
                                class="btn-wf-ghost btn-wf-sm">
                                    <i class="ti ti-users text-xs"></i> Junior
                                </a>
                                <a href="{{ route('staff.chairman.senior.staff_list', $form->uuid) }}"
                                class="btn-wf-ghost btn-wf-sm">
                                    <i class="ti ti-users text-xs"></i> Senior
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-gray-400">
                            <i class="ti ti-clipboard-list text-3xl block mb-2"></i>
                            No APER forms available.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-staff-layout>
