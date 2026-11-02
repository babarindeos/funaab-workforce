<x-staff-layout title="APER Form">

    {{-- Page Header --}}
    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">APER Form</h1>
            <p class="pg-sub">Annual Performance Evaluation Report — View and submit your APER forms</p>
        </div>
    </div>

    {{-- APER Forms Table --}}
    <div class="wf-card">
        <div class="flex items-center justify-between mb-3">
            <div class="wf-card-title mb-0">
                <i class="ti ti-file-certificate text-gray-400"></i> Available APER Forms
            </div>
        </div>

        <table class="wf-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>NAME</th>
                    <th>START DATE</th>
                    <th>END DATE</th>
                    <th>STATUS</th>
                    <th>MY FORM</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
                @forelse($aperForms as $form)
                    @php
                        $today  = \Carbon\Carbon::today();
                        $start  = \Carbon\Carbon::parse($form->start_date);
                        $end    = \Carbon\Carbon::parse($form->end_date);
                        $isOpen = $today->between($start, $end);
                    @endphp
                    <tr>
                        <td class="text-gray-400 text-[11px]">{{ $loop->iteration }}</td>
                        <td>
                            <a href="{{ route('staff.aper.open', $form->uuid) }}"
                               class="font-bold text-funaab-700 hover:underline">
                                {{ $form->name }}
                            </a>
                        </td>
                        <td>{{ $start->format('d M Y') }}</td>
                        <td>{{ $end->format('d M Y') }}</td>
                        <td>
                            @if($isOpen)
                                <span class="badge badge-active">Opened</span>
                            @else
                                <span class="badge badge-inactive">Closed</span>
                            @endif
                        </td>
                        <td>
                            @php $mySub = $submissions->get($form->id); @endphp
                            @if($mySub)
                                <span class="badge {{ $mySub->status === 'completed' ? 'badge-approved' : ($mySub->isDraft() ? 'badge-pending' : 'badge-info') }}">{{ $mySub->status_label }}</span>
                            @else
                                <span class="text-[11px] text-gray-400">Not started</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('staff.aper.open', $form->uuid) }}"
                               class="btn-wf-ghost btn-wf-sm">
                                @if($isOpen)
                                    <i class="ti ti-edit text-xs"></i> Fill / Continue
                                @else
                                    <i class="ti ti-eye text-xs"></i> View
                                @endif
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 text-gray-400">
                            <i class="ti ti-file-certificate text-3xl block mb-2"></i>
                            No APER forms are available at this time.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-staff-layout>
