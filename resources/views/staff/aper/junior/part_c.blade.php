<x-staff-layout title="APER Form — Part C">

    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">{{ $aperForm->name }}</h1>
            <p class="pg-sub">Junior Non-Teaching Staff — Part C</p>
        </div>
        <a href="{{ route('staff.aper.index') }}" class="btn-wf-ghost btn-wf-sm">
            <i class="ti ti-arrow-left text-sm"></i> Back to APER Forms
        </a>
    </div>

    @include('staff.aper._status_banner')

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
                <span class="text-[10px] text-green-400">Personal Info</span>
            </div>
            <div class="flex-1 h-px bg-green-200 mx-2"></div>
            <div class="flex items-center gap-2 flex-1">
                <a href="{{ route('staff.aper.junior.part_b', $aperForm->uuid) }}"
                   class="w-7 h-7 rounded-full bg-green-500 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                    <i class="ti ti-check text-xs"></i>
                </a>
                <a href="{{ route('staff.aper.junior.part_b', $aperForm->uuid) }}"
                   class="text-xs font-bold text-green-600 hover:underline">Part B</a>
                <span class="text-[10px] text-green-400">Assignment</span>
            </div>
            <div class="flex-1 h-px bg-funaab-200 mx-2"></div>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-funaab-600 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">C</div>
                <span class="text-xs font-bold text-funaab-700">Part C</span>
                <span class="text-[10px] text-funaab-500">HOD Evaluation</span>
            </div>
        </div>
    </div>

    @if(!$hodEvaluated)
        {{-- ── WAITING STATE ─────────────────────────────────── --}}
        <div class="wf-card">
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-16 h-16 rounded-full bg-amber-50 flex items-center justify-center mb-4">
                    <i class="ti ti-clock-hour-4 text-3xl text-amber-400"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-700 mb-2">Awaiting HOD Evaluation</h2>
                <p class="text-xs text-gray-400 max-w-md">
                    You have successfully completed Parts A and B of your APER form.
                    Your Head of Department (HOD) will now evaluate your submission.
                    This page will update once the HOD has completed their assessment.
                </p>
                <div class="mt-6 flex items-center gap-2 bg-funaab-50 border border-funaab-100 rounded-lg px-4 py-3">
                    <i class="ti ti-info-circle text-funaab-600 text-base"></i>
                    <p class="text-[11px] text-funaab-700 font-semibold">
                        No action required from you at this time.
                    </p>
                </div>
            </div>
        </div>

    @else
        {{-- ── HOD EVALUATION VISIBLE — Staff can now comment ── --}}

        @if(session('success'))
            <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-xs font-semibold px-4 py-3 rounded-lg mb-4">
                <i class="ti ti-circle-check text-base"></i> {{ session('success') }}
            </div>
        @endif

        {{-- HOD Assessment Display --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title">
                <i class="ti ti-user-star text-gray-400"></i> HOD Evaluation
                <span class="ml-auto text-[10px] font-bold text-funaab-600 bg-funaab-50 px-2 py-0.5 rounded-full">
                    Submitted by HOD
                </span>
            </div>

            {{-- HOD assessment content will be rendered here --}}
            <div class="space-y-4">
                @foreach($hodAssessment as $section)
                    <div class="border border-gray-100 rounded-lg p-4">
                        <p class="text-[10.5px] font-bold tracking-wider text-gray-400 mb-2">{{ $section['label'] }}</p>
                        <p class="text-sm text-gray-700">{{ $section['value'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Staff Comment Form --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title">
                <i class="ti ti-message-circle text-gray-400"></i> Your Comment on HOD Evaluation
            </div>

            @if($staffComment)
                {{-- Already commented — show read-only --}}
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-[10px] font-bold tracking-wider text-gray-400 mb-2">YOUR COMMENT</p>
                    <p class="text-sm text-gray-700">{{ $staffComment->comment }}</p>
                    <p class="text-[10.5px] text-gray-400 mt-2">
                        Submitted {{ \Carbon\Carbon::parse($staffComment->created_at)->diffForHumans() }}
                    </p>
                </div>
            @elseif(!$canComment)
                <p class="text-xs text-gray-500">The submission window is closed, so a comment can no longer be added.</p>
            @else
                <form action="{{ route('staff.aper.junior.part_c.comment', $aperForm->uuid) }}" method="POST"
                      onsubmit="return confirm('Submit your comment? This signs Item 20 and cannot be changed afterwards.')">
                    @csrf
                    <p class="text-xs text-gray-600 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 mb-3">
                        <strong>Item 20.</strong> I certify that I have seen the contents of this report and that my
                        Head of Department has discussed them with me. I have the following comments to add:
                    </p>
                    <div class="mb-4">
                        <label class="wf-label">Your Response / Comment <span class="text-red-500">*</span></label>
                        <textarea name="comment" rows="5"
                                  class="wf-input w-full resize-none @error('comment') border-red-400 @enderror"
                                  placeholder="Enter your response to the HOD's evaluation…"
                                  required>{{ old('comment') }}</textarea>
                        @error('comment')
                            <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn-wf-primary">
                            <i class="ti ti-send text-sm"></i> Submit Comment
                        </button>
                    </div>
                </form>
            @endif
        </div>

    @endif

</x-staff-layout>
