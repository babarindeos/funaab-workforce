<x-staff-layout title="APER Form — No Submission">

    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">{{ $aperForm->name }}</h1>
            <p class="pg-sub">Annual Performance Evaluation Report</p>
        </div>
        <a href="{{ route('staff.aper.index') }}" class="btn-wf-ghost btn-wf-sm">
            <i class="ti ti-arrow-left text-sm"></i> Back to APER Forms
        </a>
    </div>

    <div class="wf-card flex flex-col items-center justify-center py-16 text-center">
        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mb-4">
            <i class="ti ti-file-off text-2xl text-gray-400"></i>
        </div>
        <h2 class="text-sm font-bold text-gray-700 mb-1">No Submission</h2>
        <p class="text-xs text-gray-400 max-w-sm">
            This APER form has closed and you did not make a submission.
            No further action is required.
        </p>
        <p class="text-[11px] text-gray-300 mt-4">
            Form closed: {{ \Carbon\Carbon::parse($aperForm->end_date)->format('d M Y') }}
        </p>
    </div>

</x-staff-layout>
