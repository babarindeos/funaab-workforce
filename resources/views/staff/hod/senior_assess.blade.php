<x-staff-layout title="HOD Assessment — Senior Non-Teaching">

    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">HOD Assessment</h1>
            <p class="pg-sub">{{ $aperForm->name }} — {{ $staff->surname }}, {{ $staff->firstname }}</p>
        </div>
        <a href="{{ route('staff.hod.staff_list', $aperForm->uuid) }}" class="btn-wf-ghost btn-wf-sm">
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
                <p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">DEPARTMENT</p>
                <p class="text-sm font-bold text-gray-700">{{ $staff->employment?->department?->department_name }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">GRADE LEVEL</p>
                <p class="text-sm font-bold text-gray-700">{{ $personalInfo?->present_grade_level_salary ?? '—' }}</p>
            </div>
        </div>
    </div>

    @if(!$isOpen && !$hasExisting)
        <div class="wf-card flex flex-col items-center justify-center py-16 text-center">
            <i class="ti ti-lock text-3xl text-gray-300 mb-3"></i>
            <p class="text-sm font-bold text-gray-500">This APER form is closed and no assessment was submitted.</p>
        </div>
    @else

    <form action="{{ route('staff.hod.senior.store_assessment', [$aperForm->uuid, $staff->uuid]) }}"
          method="POST">
        @csrf
        @if($hasExisting) @method('PUT') @endif

        {{-- ── ITEM 17: REPORT AGREEMENT ───────────────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title">
                <i class="ti ti-file-check text-gray-400"></i>
                17. Job Description Agreement
            </div>
            <p class="text-[11px] text-gray-500 mb-4">
                Do you and the person reported upon agree on the job description and the order of importance?
                (If not, please discuss the changes with him/her and record any unresolved difference here)
            </p>

            <div class="flex items-center gap-6 mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="report_agreement" value="1"
                           {{ old('report_agreement', $performance?->report_agreement) == 1 ? 'checked' : '' }}
                           {{ !$isOpen ? 'disabled' : '' }}
                           onchange="toggleAgreementComment(this.value)"
                           class="text-funaab-600" />
                    <span class="text-sm font-bold text-gray-700">Yes</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="report_agreement" value="0"
                           {{ old('report_agreement', $performance?->report_agreement) == 0 ? 'checked' : '' }}
                           {{ !$isOpen ? 'disabled' : '' }}
                           onchange="toggleAgreementComment(this.value)"
                           class="text-funaab-600" />
                    <span class="text-sm font-bold text-gray-700">No</span>
                </label>
            </div>

            <div id="agreement-comment-row">
                <label class="wf-label">Unresolved Differences / Comments</label>
                <textarea name="report_agreement_comment" rows="3"
                          class="wf-input w-full resize-none"
                          {{ !$isOpen ? 'readonly' : '' }}
                          placeholder="Record any unresolved differences here…">{{ old('report_agreement_comment', $performance?->report_agreement_comment) }}</textarea>
                @error('report_agreement_comment')
                    <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- ── ITEM 18: ASSESSMENT ─────────────────────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title">
                <i class="ti ti-chart-bar text-gray-400"></i>
                18. Assessment by the Head of Department
            </div>
            <p class="text-[11px] text-gray-400 mb-4">
                Grade on a scale of 5 to 1 in descending order (5 = Excellent, 4 = Very Good, 3 = Good, 2 = Fair, 1 = Poor).
            </p>

            @php
                $criteria = [
                    'punctuality'            => 'a. Punctuality',
                    'initiative'             => 'b. Initiative and Foresight',
                    'expression'             => 'c. Oral / Written Expression',
                    'interpersonal'          => 'd. Interpersonal & Public Relations',
                    'responsibility'         => 'e. Responsibility / Reliability',
                    'competence'             => 'f. Professional / Technical Competence',
                    'organisational_ability' => 'g. Organizational Ability',
                    'work_quality'           => 'h. Workload and Quality of Work',
                    'special_responsibility' => 'i. Special Responsibilities',
                ];
            @endphp

            <div class="space-y-3">
                @foreach($criteria as $field => $label)
                    <div class="flex items-center justify-between gap-4 py-2 border-b border-gray-50">
                        <label class="text-sm text-gray-700 font-semibold flex-1">{{ $label }}</label>
                        <select name="{{ $field }}"
                                class="wf-input w-44 flex-shrink-0 @error($field) border-red-400 @enderror"
                                {{ !$isOpen ? 'disabled' : '' }} required>
                            <option value="">— Grade —</option>
                            @foreach($grades as $val => $gradeLabel)
                                <option value="{{ $val }}"
                                    {{ old($field, $performance?->$field) == $val ? 'selected' : '' }}>
                                    {{ $gradeLabel }}
                                </option>
                            @endforeach
                        </select>
                        @error($field)
                            <p class="text-red-500 text-[11px]">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>

            {{-- General Remarks --}}
            <div class="mt-4">
                <label class="wf-label">General Remarks <span class="text-red-500">*</span></label>
                <textarea name="general_remarks" rows="4"
                          class="wf-input w-full resize-none @error('general_remarks') border-red-400 @enderror"
                          {{ !$isOpen ? 'readonly' : '' }}
                          placeholder="General remarks on the staff member's performance…"
                          required>{{ old('general_remarks', $performance?->general_remarks) }}</textarea>
                @error('general_remarks')
                    <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- ── ITEM 19: TRAINING NEEDS ─────────────────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title">
                <i class="ti ti-school text-gray-400"></i>
                19. Training Needs
            </div>
            <p class="text-[11px] text-gray-400 mb-4">
                In completing this section you should take account of any view expressed by the person reported on.
            </p>
            <div class="mb-4">
                <label class="wf-label">19(a). If performance or potential could be improved by training, please specify the needs</label>
                <textarea name="need_training" rows="4"
                          class="wf-input w-full resize-none"
                          {{ !$isOpen ? 'readonly' : '' }}
                          placeholder="Specify training needs…">{{ old('need_training', $trainingNeed?->need_training) }}</textarea>
            </div>
            <div>
                <label class="wf-label">19(b). If they cannot be met by training on the job, please suggest in which way they might be met</label>
                <textarea name="other_needs" rows="4"
                          class="wf-input w-full resize-none"
                          {{ !$isOpen ? 'readonly' : '' }}
                          placeholder="Suggest alternative ways to meet training needs…">{{ old('other_needs', $trainingNeed?->other_needs) }}</textarea>
            </div>
        </div>

        {{-- ── ITEM 21: HOD RECOMMENDATION (not shown to staff) --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title">
                <i class="ti ti-award text-gray-400"></i>
                21. Recommendation by Head of Department
                <span class="ml-auto text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                    <i class="ti ti-eye-off text-xs"></i> Not visible to staff
                </span>
            </div>
            <p class="text-[11px] text-gray-400 mb-4">I recommend:</p>
            <div class="mb-4 text-[11px] bg-amber-50 border border-amber-200 text-amber-800 rounded-lg px-3 py-2">
                <i class="ti ti-alert-triangle"></i>
                A staff member who did not obtain at least {{ $punctualityMin }}% in punctuality (a grade of 4 or 5 in item "Punctuality")
                is not eligible for promotion or the annual salary increment — use option (iii).
                @if($errors->has('promotion_level_id') || $errors->has('promotion_salary_level_id'))
                    <p class="text-red-600 font-semibold mt-1">{{ $errors->first('promotion_level_id') ?: $errors->first('promotion_salary_level_id') }}</p>
                @endif
                @error('no_salary_increment') <p class="text-red-600 font-semibold mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-4">
                <div>
                    <label class="wf-label">(i). Promotion to the grade of</label>
                    <select name="promotion_level_id" class="wf-input w-full max-w-sm" {{ !$isOpen ? 'disabled' : '' }}>
                        <option value="">— Select Grade Level —</option>
                        @foreach($salaryLevels as $level)
                            <option value="{{ $level->id }}"
                                {{ old('promotion_level_id', $recommendation?->promotion_level_id) == $level->id ? 'selected' : '' }}>
                                {{ $level->salaryStructure?->salary_structure_name }} {{ $level->level }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="wf-label">(ii). Salary increment by ___ step(s) within the present scale</label>
                    <select name="increment_steps" class="wf-input w-full max-w-sm" {{ !$isOpen ? 'disabled' : '' }}>
                        <option value="">— No increment recommended —</option>
                        @foreach([1,2,3,4,5] as $n)
                            <option value="{{ $n }}" {{ old('increment_steps', $recommendation?->increment_steps) == $n ? 'selected' : '' }}>{{ $n }} step{{ $n > 1 ? 's' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="wf-label">(iii). No Salary Increment — Give detailed reasons</label>
                    <textarea name="no_salary_increment" rows="3"
                              class="wf-input w-full resize-none"
                              {{ !$isOpen ? 'readonly' : '' }}
                              placeholder="Give detailed reasons for no salary increment…">{{ old('no_salary_increment', $recommendation?->no_salary_increment) }}</textarea>
                </div>
            </div>
        </div>

        @if($isOpen)
            <div class="flex items-center justify-end gap-3 mb-6">
                <a href="{{ route('staff.hod.staff_list', $aperForm->uuid) }}" class="btn-wf-ghost">Cancel</a>
                <button type="submit" class="btn-wf-primary">
                    <i class="ti ti-device-floppy text-sm"></i>
                    {{ $hasExisting ? 'Update Assessment' : 'Submit Assessment' }}
                </button>
            </div>
        @endif

    </form>
    @endif

    <script>
        function toggleAgreementComment(value) {
            // Always show the comment field — just highlight when No is selected
            const row = document.getElementById('agreement-comment-row');
            row.classList.toggle('border-l-2', value === '0');
            row.classList.toggle('border-amber-400', value === '0');
            row.classList.toggle('pl-3', value === '0');
        }

        // Init on page load
        const checked = document.querySelector('input[name="report_agreement"]:checked');
        if (checked) toggleAgreementComment(checked.value);
    </script>

</x-staff-layout>
