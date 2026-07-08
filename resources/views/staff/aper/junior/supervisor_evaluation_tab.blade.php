<x-staff-layout title="APER Form — Supervisor's Evaluation">

    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">{{ $aperForm->name }}</h1>
            <p class="pg-sub">Junior Non-Teaching Staff — Supervisor's Evaluation</p>
        </div>
        <a href="{{ route('staff.aper.index') }}" class="btn-wf-ghost btn-wf-sm">
            <i class="ti ti-arrow-left text-sm"></i> Back to APER Forms
        </a>
    </div>

    {{-- Progress Indicator --}}
    <div class="wf-card mb-4">
        <div class="flex items-center gap-0">
            <div class="flex items-center gap-2 flex-1">
                <a href="{{ route('staff.aper.open', $aperForm->uuid) }}"
                   class="w-7 h-7 rounded-full bg-green-500 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                    <i class="ti ti-check text-xs"></i>
                </a>
                <a href="{{ route('staff.aper.open', $aperForm->uuid) }}"
                   class="text-xs font-bold text-green-600 hover:underline">Part A</a>
            </div>
            <div class="flex-1 h-px bg-green-200 mx-2"></div>
            <div class="flex items-center gap-2 flex-1">
                <div class="w-7 h-7 rounded-full bg-funaab-600 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">B</div>
                <span class="text-xs font-bold text-funaab-700">Part B</span>
            </div>
            <div class="flex-1 h-px bg-gray-200 mx-2"></div>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-xs font-bold flex-shrink-0">C</div>
                <span class="text-xs font-semibold text-gray-400">Part C</span>
            </div>
        </div>
    </div>

    {{-- Part B Tabs --}}
    <div class="flex gap-1 mb-4 bg-gray-100 rounded-lg p-1 w-fit">
        <a href="{{ route('staff.aper.junior.part_b', $aperForm->uuid) }}"
           class="px-4 py-1.5 text-xs font-semibold text-gray-500 rounded-md hover:bg-white/60">
            <i class="ti ti-list-check text-xs"></i> Your Submission
        </a>
        <div class="px-4 py-1.5 text-xs font-bold rounded-md bg-white text-funaab-700 shadow-sm">
            <i class="ti ti-user-check text-xs"></i> Supervisor's Evaluation
        </div>
    </div>

    {{-- Supervisor Evaluation Content --}}
    <div class="wf-card mb-4">
        <div class="wf-card-title">
            <i class="ti ti-user-check text-gray-400"></i> Particulars Supplied by Supervisor
            <span class="ml-auto text-[10px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-full">
                Submitted
            </span>
        </div>

        {{-- Item 15: Sick Leave --}}
        <div class="mb-5">
            <p class="text-[11px] font-bold tracking-wider text-gray-500 mb-3">15. NUMBER OF DAYS OF SICK LEAVE DURING PERIOD</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">15(a). WITH MEDICAL CERTIFICATE</p>
                    <p class="text-sm font-semibold text-gray-700">{{ $sickLeave?->with_medical_certificate ?? '—' }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">15(b). WITHOUT MEDICAL CERTIFICATE</p>
                    <p class="text-sm font-semibold text-gray-700">{{ $sickLeave?->without_medical_certificate ?? '—' }}</p>
                </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">FREQUENCY OF ATTENDANCE AT HEALTH CENTRE</p>
                <p class="text-sm font-semibold text-gray-700">{{ $frequency?->frequency ?? '—' }}</p>
            </div>
        </div>

        {{-- Item 16: Sanctions --}}
        <div>
            <p class="text-[11px] font-bold tracking-wider text-gray-500 mb-3">16. ANY SANCTIONS INCURRED DURING THE PERIOD</p>
            @if($sanctions->isNotEmpty())
                <div class="space-y-2">
                    @foreach($sanctions as $i => $sanction)
                        <div class="flex items-start gap-3 bg-gray-50 rounded-lg p-3">
                            <span class="text-[11px] font-bold text-gray-400 flex-shrink-0">{{ $i + 1 }}.</span>
                            <p class="text-sm text-gray-700">{{ $sanction->sanction }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-sm text-gray-400 italic">No sanctions recorded.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Navigation --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('staff.aper.junior.part_b', $aperForm->uuid) }}" class="btn-wf-ghost">
            <i class="ti ti-arrow-left text-sm"></i> Back to Part B
        </a>
        <a href="{{ route('staff.aper.junior.part_c', $aperForm->uuid) }}" class="btn-wf-primary">
            <i class="ti ti-arrow-right text-sm"></i> Continue to Part C
        </a>
    </div>

</x-staff-layout>
