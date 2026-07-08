<x-staff-layout title="Chairman DAPC Assessment — Senior Non-Teaching">

    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">Chairman DAPC Assessment</h1>
            <p class="pg-sub">{{ $aperForm->name }} — {{ $staff->surname }}, {{ $staff->firstname }}</p>
        </div>
        <a href="{{ route('staff.chairman.senior.staff_list', $aperForm->uuid) }}" class="btn-wf-ghost btn-wf-sm">
            <i class="ti ti-arrow-left text-sm"></i> Back to Staff List
        </a>
    </div>

    {{-- Staff Info --}}
    <div class="wf-card mb-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">STAFF NAME</p>
                <p class="text-sm font-bold text-gray-700">{{ $staff->surname }}, {{ $staff->firstname }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">FILE NO.</p>
                <p class="text-sm font-bold text-gray-700 font-mono">{{ $staff->staff_no }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">GRADE LEVEL</p>
                <p class="text-sm font-bold text-gray-700">
                    @if($staff->employment?->designation?->salaryLevel?->level)
                        CONTISS - {{ $staff->employment->designation->salaryLevel->level }}
                    @else
                        {{ $personalInfo?->present_grade_level_salary ?? '—' }}
                    @endif
                </p>
            </div>
            <div>
                <p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">MIN. PROMOTION SCORE</p>
                <p class="text-sm font-bold {{ $minReq ? 'text-gray-700' : 'text-gray-400' }}">
                    {{ $minReq ? $minReq->percentage.'%' : '—' }}
                </p>
            </div>
        </div>
    </div>

    @if(!$isOpen && !$hasExisting)
        <div class="wf-card flex flex-col items-center justify-center py-16 text-center">
            <i class="ti ti-lock text-3xl text-gray-300 mb-3"></i>
            <p class="text-sm font-bold text-gray-500">This APER form is closed and no assessment was submitted.</p>
        </div>
    @else

    <form action="{{ route('staff.chairman.senior.store_assessment', [$aperForm->uuid, $staff->uuid]) }}"
          method="POST">
        @csrf
        @if($hasExisting) @method('PUT') @endif

        {{-- ── SCORING (item 22 equivalent) ───────────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title">
                <i class="ti ti-chart-bar text-gray-400"></i>
                Detailed Scoring — Departmental Assessment Panel
            </div>
            <p class="text-[11px] text-gray-400 mb-4">Score each metric up to the maximum allowed. Total score out of 100.</p>

            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-gray-50 text-[10.5px] font-bold tracking-wider text-gray-500">
                            <th class="px-4 py-3 text-left border-b border-gray-200">METRIC</th>
                            <th class="px-4 py-3 text-center border-b border-gray-200 w-24">MAX SCORE</th>
                            <th class="px-4 py-3 text-center border-b border-gray-200 w-36">SCORE GIVEN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scoringGuides as $guide)
                            @php $existing = $existingScores->get($guide->id); @endphp
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                                <td class="px-4 py-3 text-sm font-semibold text-gray-700">{{ $guide->metric }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-xs font-bold text-gray-500">{{ $guide->max_score }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <input type="number"
                                           name="scores[{{ $guide->id }}]"
                                           value="{{ old("scores.{$guide->id}", $existing?->score) }}"
                                           min="0" max="{{ $guide->max_score }}" step="0.5"
                                           class="wf-input w-24 text-center @error("scores.{$guide->id}") border-red-400 @enderror"
                                           {{ !$isOpen ? 'readonly' : '' }} required />
                                    @error("scores.{$guide->id}")
                                        <p class="text-red-500 text-[10px] mt-1">Max: {{ $guide->max_score }}</p>
                                    @enderror
                                </td>
                            </tr>
                        @endforeach
                        <tr class="bg-funaab-50">
                            <td class="px-4 py-3 text-sm font-bold text-funaab-800">Total</td>
                            <td class="px-4 py-3 text-center text-sm font-bold text-funaab-800">100</td>
                            <td class="px-4 py-3 text-center">
                                @if($hasExisting)
                                    <div class="text-sm font-bold {{ $meetsMinReq === true ? 'text-green-600' : ($meetsMinReq === false ? 'text-red-600' : 'text-gray-700') }}">
                                        {{ $totalScore }} / 100
                                        <span class="text-[10px] ml-1">({{ $percentage }}%)</span>
                                    </div>
                                    @if($meetsMinReq === true)
                                        <span class="badge badge-active text-[10px]">Meets Requirement</span>
                                    @elseif($meetsMinReq === false)
                                        <span class="badge badge-inactive text-[10px]">Below Requirement</span>
                                    @endif
                                @else
                                    <span class="text-[11px] text-gray-400" id="live-total">0 / 100</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($minReq)
                <div class="mt-3 flex items-center gap-2 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">
                    <i class="ti ti-info-circle text-amber-500 text-sm"></i>
                    <p class="text-[11px] text-amber-700 font-semibold">
                        Minimum score for promotion at CONTISS - {{ $minReq->salaryLevel?->level }}:
                        <strong>{{ $minReq->percentage }}%</strong>
                    </p>
                </div>
            @endif
        </div>

        {{-- ── RECOMMENDATION (item 23 equivalent) ────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title">
                <i class="ti ti-gavel text-gray-400"></i>
                Recommendation of the Appropriate Department Appointments and Promotions Committee
            </div>

            @php
                $currentRec = old('recommendation',
                    $recommendation ? ($recommendation->endorse ? 'endorse' : 'reject') : null
                );
            @endphp

            <div class="space-y-3 mb-4">
                <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border
                    {{ $currentRec === 'endorse' ? 'border-funaab-300 bg-funaab-50' : 'border-gray-200 hover:bg-gray-50' }}">
                    <input type="radio" name="recommendation" value="endorse"
                           {{ $currentRec === 'endorse' ? 'checked' : '' }}
                           {{ !$isOpen ? 'disabled' : '' }}
                           onchange="toggleRecommendation(this.value)"
                           class="mt-0.5 text-funaab-600" />
                    <p class="text-sm font-bold text-gray-700">(a) We endorse the comments and recommendations of the Head of Department</p>
                </label>
                <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border
                    {{ $currentRec === 'reject' ? 'border-red-300 bg-red-50' : 'border-gray-200 hover:bg-gray-50' }}">
                    <input type="radio" name="recommendation" value="reject"
                           {{ $currentRec === 'reject' ? 'checked' : '' }}
                           {{ !$isOpen ? 'disabled' : '' }}
                           onchange="toggleRecommendation(this.value)"
                           class="mt-0.5 text-red-500" />
                    <p class="text-sm font-bold text-gray-700">(b) We do not agree with the comments and recommendations of the Head of Department for the following reasons:</p>
                </label>
            </div>

            @error('recommendation')
                <p class="text-red-500 text-[11px] mb-3">{{ $message }}</p>
            @enderror

            <div id="endorse-section" class="{{ $currentRec === 'endorse' ? '' : 'hidden' }} mb-3">
                <label class="wf-label">Endorsement Comments</label>
                <textarea name="endorse" rows="4" class="wf-input w-full resize-none"
                          {{ !$isOpen ? 'readonly' : '' }}
                          placeholder="Enter endorsement comments…">{{ old('endorse', $recommendation?->endorse) }}</textarea>
                @error('endorse') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div id="reject-section" class="{{ $currentRec === 'reject' ? '' : 'hidden' }}">
                <label class="wf-label">Reasons for Disagreement</label>
                <textarea name="reject" rows="4" class="wf-input w-full resize-none"
                          {{ !$isOpen ? 'readonly' : '' }}
                          placeholder="State reasons for disagreement with HOD's recommendation…">{{ old('reject', $recommendation?->reject) }}</textarea>
                @error('reject') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        @if($isOpen)
            <div class="flex items-center justify-end gap-3 mb-6">
                <a href="{{ route('staff.chairman.senior.staff_list', $aperForm->uuid) }}" class="btn-wf-ghost">Cancel</a>
                <button type="submit" class="btn-wf-primary">
                    <i class="ti ti-device-floppy text-sm"></i>
                    {{ $hasExisting ? 'Update Assessment' : 'Submit Assessment' }}
                </button>
            </div>
        @endif

    </form>
    @endif

    <script>
        function toggleRecommendation(value) {
            document.getElementById('endorse-section').classList.toggle('hidden', value !== 'endorse');
            document.getElementById('reject-section').classList.toggle('hidden', value !== 'reject');
        }

        const scoreInputs = document.querySelectorAll('input[name^="scores["]');
        const liveTotal   = document.getElementById('live-total');
        if (liveTotal) {
            scoreInputs.forEach(input => input.addEventListener('input', updateTotal));
            function updateTotal() {
                let total = 0;
                scoreInputs.forEach(i => total += parseFloat(i.value) || 0);
                liveTotal.textContent = total.toFixed(1) + ' / 100';
                liveTotal.className = total >= 50
                    ? 'text-[11px] text-green-600 font-bold'
                    : 'text-[11px] text-red-500 font-bold';
            }
        }
    </script>

</x-staff-layout>
