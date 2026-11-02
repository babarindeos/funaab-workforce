<x-staff-layout title="APER Form — Part B">
    @php
        $supervisorHasEvaluated = $submission?->reached('supervisor_done') ?? false;
    @endphp

    {{-- Page Header --}}
    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">{{ $aperForm->name }}</h1>
            <p class="pg-sub">Junior Non-Teaching Staff — Part B: Nature of Assignment</p>
        </div>
        <a href="{{ route('staff.aper.index') }}" class="btn-wf-ghost btn-wf-sm">
            <i class="ti ti-arrow-left text-sm"></i> Back to APER Forms
        </a>
    </div>

    {{-- Progress Indicator --}}
    <div class="wf-card mb-4">
        <div class="flex items-center gap-0">
            {{-- Part A — Completed --}}
            <div class="flex items-center gap-2 flex-1">
                <a href="{{ route('staff.aper.open', $aperForm->uuid) }}"
                   class="w-7 h-7 rounded-full bg-green-500 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                    <i class="ti ti-check text-xs"></i>
                </a>
                <a href="{{ route('staff.aper.open', $aperForm->uuid) }}"
                   class="text-xs font-bold text-green-600 hover:underline">Part A</a>
                <span class="text-[10px] text-green-400">Personal Info</span>
            </div>
            <div class="flex-1 h-px bg-funaab-200 mx-2"></div>
            {{-- Part B — Active --}}
            <div class="flex items-center gap-2 flex-1">
                <div class="w-7 h-7 rounded-full bg-funaab-600 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">B</div>
                <span class="text-xs font-bold text-funaab-700">Part B</span>
                <span class="text-[10px] text-funaab-500">Assignment</span>
            </div>
            <div class="flex-1 h-px bg-gray-200 mx-2"></div>
            {{-- Part C — Pending --}}
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-xs font-bold flex-shrink-0">C</div>
                <span class="text-xs font-semibold text-gray-400">Part C</span>
            </div>
        </div>
    </div>


    {{-- Part B Tabs --}}
    <div class="flex gap-1 mb-4 bg-gray-100 rounded-lg p-1 w-fit">
        <div class="px-4 py-1.5 text-xs font-bold rounded-md bg-white text-funaab-700 shadow-sm">
            <i class="ti ti-list-check text-xs"></i> Your Submission
        </div>
        @if($supervisorHasEvaluated)
            <a href="{{ route('staff.aper.junior.supervisor_evaluation', $aperForm->uuid) }}"
            class="px-4 py-1.5 text-xs font-semibold text-gray-500 rounded-md hover:bg-white/60 flex items-center gap-1">
                <i class="ti ti-user-check text-xs"></i> Supervisor's Evaluation
                <span class="w-2 h-2 rounded-full bg-green-500 ml-1"></span>
            </a>
        @endif
    </div>
    @include('staff.aper._status_banner')

    <form action="{{ $readonly ? '#' : route('staff.aper.junior.part_b.store', $aperForm->uuid) }}"
          method="POST">
        @csrf
        @if($hasDuties || $hasDifficulties || $hasOther) @method('PUT') @endif

        {{-- ═══════════════════════════════════════
             ITEM 12 — MAIN DUTIES
        ════════════════════════════════════════ --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title">
                <i class="ti ti-list-check text-gray-400"></i>
                12. State your main duties during the period covered by this Report
            </div>

            <div id="duties-tbody" class="space-y-2">
                @php
                    $dutyRows = old('duties',
                        $duties->isNotEmpty()
                            ? $duties->pluck('duty')->toArray()
                            : ['', '', '']
                    );
                @endphp

                @foreach($dutyRows as $i => $duty)
                    <div class="duty-row flex items-start gap-2">
                        <span class="text-[11px] text-gray-400 font-semibold mt-2.5 w-5 flex-shrink-0">{{ $i + 1 }}.</span>
                        <textarea name="duties[{{ $i }}]" rows="2"
                                  class="wf-input w-full resize-none"
                                  {{ $readonly ? 'readonly' : '' }}
                                  placeholder="Describe a main duty…">{{ $duty }}</textarea>
                        @if(!$readonly)
                            <button type="button" onclick="removeDutyRow(this)"
                                    class="mt-2 text-red-400 hover:text-red-600 flex-shrink-0">
                                <i class="ti ti-trash text-sm"></i>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>

            @if(!$readonly)
                <button type="button" onclick="addDutyRow()"
                        class="mt-3 btn-wf-ghost btn-wf-sm">
                    <i class="ti ti-plus text-xs"></i> Add Duty
                </button>
            @endif
        </div>

        {{-- ═══════════════════════════════════════
             ITEM 13 — DIFFICULTIES & SOLUTIONS
        ════════════════════════════════════════ --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title">
                <i class="ti ti-alert-triangle text-gray-400"></i>
                13. What major difficulties did you encounter in the performance of your duties? Offer suggestions for their solution
            </div>

            <div id="difficulties-tbody" class="space-y-3">
                @php
                    $diffRows = old('difficulties',
                        $difficulties->isNotEmpty()
                            ? $difficulties->map(fn($d) => ['difficulty' => $d->difficulty, 'solution' => $d->solution])->toArray()
                            : [['difficulty' => '', 'solution' => '']]
                    );
                @endphp

                @foreach($diffRows as $i => $row)
                    <div class="difficulty-row border border-gray-100 rounded-lg p-3 bg-gray-50/50">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10.5px] font-bold text-gray-400 tracking-wider">ENTRY {{ $i + 1 }}</span>
                            @if(!$readonly)
                                <button type="button" onclick="removeDiffRow(this)"
                                        class="text-red-400 hover:text-red-600">
                                    <i class="ti ti-trash text-sm"></i>
                                </button>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="wf-label">Difficulty</label>
                                <textarea name="difficulties[{{ $i }}][difficulty]" rows="3"
                                          class="wf-input w-full resize-none"
                                          {{ $readonly ? 'readonly' : '' }}
                                          placeholder="Describe the difficulty encountered…">{{ $row['difficulty'] }}</textarea>
                            </div>
                            <div>
                                <label class="wf-label">Suggested Solution</label>
                                <textarea name="difficulties[{{ $i }}][solution]" rows="3"
                                          class="wf-input w-full resize-none"
                                          {{ $readonly ? 'readonly' : '' }}
                                          placeholder="Suggest a solution…">{{ $row['solution'] }}</textarea>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if(!$readonly)
                <button type="button" onclick="addDiffRow()"
                        class="mt-3 btn-wf-ghost btn-wf-sm">
                    <i class="ti ti-plus text-xs"></i> Add Difficulty
                </button>
            @endif
        </div>

        {{-- ═══════════════════════════════════════
             ITEM 14 — OTHER USEFUL INFORMATION
        ════════════════════════════════════════ --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title">
                <i class="ti ti-info-circle text-gray-400"></i>
                14. Any other useful information peculiar to your duty during the period covered by this Report
            </div>

            <div id="other-tbody" class="space-y-2">
                @php
                    $otherRows = old('peculiar_duties',
                        $peculiarDuties->isNotEmpty()
                            ? $peculiarDuties->pluck('peculiar_duty')->toArray()
                            : ['', '']
                    );
                @endphp

                @foreach($otherRows as $i => $other)
                    <div class="other-row flex items-start gap-2">
                        <span class="text-[11px] text-gray-400 font-semibold mt-2.5 w-5 flex-shrink-0">{{ $i + 1 }}.</span>
                        <textarea name="peculiar_duties[{{ $i }}]" rows="2"
                                  class="wf-input w-full resize-none"
                                  {{ $readonly ? 'readonly' : '' }}
                                  placeholder="Any other useful information…">{{ $other }}</textarea>
                        @if(!$readonly)
                            <button type="button" onclick="removeOtherRow(this)"
                                    class="mt-2 text-red-400 hover:text-red-600 flex-shrink-0">
                                <i class="ti ti-trash text-sm"></i>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>

            @if(!$readonly)
                <button type="button" onclick="addOtherRow()"
                        class="mt-3 btn-wf-ghost btn-wf-sm">
                    <i class="ti ti-plus text-xs"></i> Add Entry
                </button>
            @endif
        </div>

        {{-- Form Actions --}}
        @if(!$readonly)
            <div class="flex items-center justify-between mb-6">
                <a href="{{ route('staff.aper.open', $aperForm->uuid) }}"
                   class="btn-wf-ghost">
                    <i class="ti ti-arrow-left text-sm"></i> Back to Part A
                </a>
                <button type="submit" class="btn-wf-primary">
                    <i class="ti ti-device-floppy text-sm"></i> Save Part B
                </button>
            </div>
        @endif

    </form>

    <script>
        // ── Duties (item 12) ──────────────────────────────
        let dutyIndex = {{ count($dutyRows) }};

        function addDutyRow() {
            const container = document.getElementById('duties-tbody');
            const idx       = dutyIndex++;
            const div       = document.createElement('div');
            div.className   = 'duty-row flex items-start gap-2';
            div.innerHTML   = `
                <span class="text-[11px] text-gray-400 font-semibold mt-2.5 w-5 flex-shrink-0">${document.querySelectorAll('.duty-row').length + 1}.</span>
                <textarea name="duties[${idx}]" rows="2" class="wf-input w-full resize-none" placeholder="Describe a main duty…"></textarea>
                <button type="button" onclick="removeDutyRow(this)" class="mt-2 text-red-400 hover:text-red-600 flex-shrink-0">
                    <i class="ti ti-trash text-sm"></i>
                </button>`;
            container.appendChild(div);
        }

        function removeDutyRow(btn) {
            if (document.querySelectorAll('.duty-row').length > 1) {
                btn.closest('.duty-row').remove();
            }
        }

        // ── Difficulties (item 13) ────────────────────────
        let diffIndex = {{ count($diffRows) }};

        function addDiffRow() {
            const container = document.getElementById('difficulties-tbody');
            const idx       = diffIndex++;
            const count     = document.querySelectorAll('.difficulty-row').length + 1;
            const div       = document.createElement('div');
            div.className   = 'difficulty-row border border-gray-100 rounded-lg p-3 bg-gray-50/50';
            div.innerHTML   = `
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10.5px] font-bold text-gray-400 tracking-wider">ENTRY ${count}</span>
                    <button type="button" onclick="removeDiffRow(this)" class="text-red-400 hover:text-red-600">
                        <i class="ti ti-trash text-sm"></i>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="wf-label">Difficulty</label>
                        <textarea name="difficulties[${idx}][difficulty]" rows="3" class="wf-input w-full resize-none" placeholder="Describe the difficulty encountered…"></textarea>
                    </div>
                    <div>
                        <label class="wf-label">Suggested Solution</label>
                        <textarea name="difficulties[${idx}][solution]" rows="3" class="wf-input w-full resize-none" placeholder="Suggest a solution…"></textarea>
                    </div>
                </div>`;
            container.appendChild(div);
        }

        function removeDiffRow(btn) {
            if (document.querySelectorAll('.difficulty-row').length > 1) {
                btn.closest('.difficulty-row').remove();
            }
        }

        // ── Other useful info (item 14) ───────────────────
        let otherIndex = {{ count($otherRows) }};

        function addOtherRow() {
            const container = document.getElementById('other-tbody');
            const idx       = otherIndex++;
            const div       = document.createElement('div');
            div.className   = 'other-row flex items-start gap-2';
            div.innerHTML   = `
                <span class="text-[11px] text-gray-400 font-semibold mt-2.5 w-5 flex-shrink-0">${document.querySelectorAll('.other-row').length + 1}.</span>
                <textarea name="peculiar_duties[${idx}]" rows="2" class="wf-input w-full resize-none" placeholder="Any other useful information…"></textarea>
                <button type="button" onclick="removeOtherRow(this)" class="mt-2 text-red-400 hover:text-red-600 flex-shrink-0">
                    <i class="ti ti-trash text-sm"></i>
                </button>`;
            container.appendChild(div);
        }

        function removeOtherRow(btn) {
            if (document.querySelectorAll('.other-row').length > 1) {
                btn.closest('.other-row').remove();
            }
        }
    </script>

</x-staff-layout>
