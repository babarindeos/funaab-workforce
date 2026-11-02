{{--
    APER status banner for the staff member's own form.
    Needs: $aperForm. Optional: $submission (resolved here when absent),
           $canSubmit (bool) – show the final "Submit" button.
--}}
@php
    $wf  = app(\App\Services\AperWorkflow::class);
    $me  = $wf->currentStaff();
    $sub = $submission ?? $wf->submissionFor($aperForm, $me, false);
    $open = $wf->isWindowOpen($aperForm);
    $isJunior = ($sub->form_type ?? $wf->formTypeFor($me)) === 'junior';

    $steps = $isJunior
        ? ['draft' => 'You (Parts A & B)', 'submitted' => 'Supervisor', 'supervisor_done' => 'HOD', 'hod_done' => 'Your comment', 'staff_commented' => 'Assessment Panel', 'completed' => 'Completed']
        : ['draft' => 'You (Part A)', 'submitted' => 'HOD (Part B)', 'hod_done' => 'Your comment', 'staff_commented' => 'Assessment Panel', 'completed' => 'Completed'];
    $current = $sub->status ?? 'draft';
    $keys = array_keys($steps);
    $pos  = array_search($current, $keys, true);
    if ($pos === false) { $pos = array_search('hod_done', $keys, true); }
@endphp

<div class="wf-card mb-4">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
        <div class="text-xs text-gray-600">
            <span class="font-bold text-gray-700">Period of report:</span>
            @if($aperForm->review_period_start && $aperForm->review_period_end)
                {{ \Carbon\Carbon::parse($aperForm->review_period_start)->format('j F, Y') }}
                to {{ \Carbon\Carbon::parse($aperForm->review_period_end)->format('j F, Y') }}
            @else
                <span class="text-gray-400">not set by Registry</span>
            @endif
            <span class="mx-2 text-gray-300">|</span>
            <span class="font-bold text-gray-700">Submission window:</span>
            {{ \Carbon\Carbon::parse($aperForm->start_date)->format('d M Y') }} – {{ \Carbon\Carbon::parse($aperForm->end_date)->format('d M Y') }}
            <span class="ml-1 badge {{ $open ? 'badge-active' : 'badge-inactive' }}">{{ $open ? 'Open' : 'Closed' }}</span>
        </div>
        <span class="badge {{ $current === 'completed' ? 'badge-active' : 'badge-pending' }}">
            {{ \App\Models\AperSubmission::LABELS[$current] ?? $current }}
        </span>
    </div>

    <div class="flex items-center gap-1 overflow-x-auto">
        @foreach($steps as $key => $label)
            @php $i = $loop->index; $done = $i < $pos || $current === 'completed'; $active = $i === $pos && $current !== 'completed'; @endphp
            <div class="flex items-center gap-1.5 flex-shrink-0">
                <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold
                    {{ $done ? 'bg-funaab-600 text-white' : ($active ? 'bg-amber-400 text-white' : 'bg-gray-200 text-gray-400') }}">
                    @if($done)<i class="ti ti-check text-[10px]"></i>@else{{ $i + 1 }}@endif
                </div>
                <span class="text-[11px] {{ $active ? 'font-bold text-gray-700' : 'text-gray-400' }}">{{ $label }}</span>
            </div>
            @if(!$loop->last)<div class="w-6 h-px bg-gray-200 flex-shrink-0"></div>@endif
        @endforeach
    </div>

    @if($sub && ($sub->reached('supervisor_done') || $sub->reached('hod_done')))
        <div class="mt-3 flex flex-wrap gap-2">
            @if($isJunior && $sub->reached('supervisor_done'))
                <a href="{{ route('staff.aper.junior.supervisor_evaluation', $aperForm->uuid) }}" class="btn-wf-ghost btn-wf-sm">
                    <i class="ti ti-user-check text-xs"></i> Supervisor's particulars (items 15–16)
                </a>
            @endif
            @if($sub->reached('hod_done'))
                <a href="{{ route($isJunior ? 'staff.aper.junior.part_c' : 'staff.aper.senior.part_c', $aperForm->uuid) }}" class="btn-wf-ghost btn-wf-sm">
                    <i class="ti ti-message-2 text-xs"></i> HOD's assessment &amp; your comment
                </a>
            @endif
        </div>
    @endif

    @if(!$open && (!$sub || $sub->isDraft()))
        <div class="mt-3 flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-semibold px-3 py-2 rounded-lg">
            <i class="ti ti-lock text-base"></i> The submission window is closed. Your entries are shown read-only.
        </div>
    @elseif($sub && !$sub->isDraft())
        <div class="mt-3 flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold px-3 py-2 rounded-lg">
            <i class="ti ti-send text-base"></i>
            You submitted this form on {{ optional($sub->staff_submitted_at)->format('d M Y, H:i') }}. Your parts are now locked.
        </div>
    @endif

    @if(!empty($canSubmit) && $open && $sub && $sub->isDraft())
        <form action="{{ route('staff.aper.submit', $aperForm->uuid) }}" method="POST"
              onsubmit="return confirm('Submit your APER form? You will not be able to change your entries after submitting.')"
              class="mt-3 flex flex-wrap items-center justify-between gap-3 bg-green-50 border border-green-200 rounded-lg px-3 py-2">
            @csrf
            <span class="text-xs text-green-800">
                <i class="ti ti-info-circle"></i>
                When you have checked all your entries, submit the form to your {{ $isJunior ? 'Supervisor' : 'Head of Department' }}.
                By submitting you sign the form.
            </span>
            <button type="submit" class="btn-wf-primary btn-wf-sm"><i class="ti ti-send text-xs"></i> Submit APER Form</button>
        </form>
    @endif
</div>
