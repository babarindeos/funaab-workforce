<x-staff-layout title="HOD Assessment Form">

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

    {{-- Supervisor particulars (items 15–16) --}}
    <div class="wf-card mb-4">
        <div class="wf-card-title"><i class="ti ti-user-check text-gray-400"></i> Particulars supplied by the Supervisor</div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">15(a). SICK LEAVE WITH MEDICAL CERT.</p>{{ $sickLeave?->with_medical_certificate ?? '—' }}</div>
            <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">15(b). SICK LEAVE WITHOUT MEDICAL CERT.</p>{{ $sickLeave?->without_medical_certificate ?? '—' }}</div>
            <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">HEALTH CENTRE ATTENDANCE</p>{{ $frequency?->frequency ?? '—' }}</div>
        </div>
        <div class="mt-3 text-sm">
            <p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">16. SANCTIONS INCURRED</p>
            @forelse($sanctions as $sn)<p>• {{ $sn->sanction }}</p>@empty<p class="text-gray-400">None recorded</p>@endforelse
        </div>
    </div>

    <form action="{{ route('staff.hod.store_assessment', [$aperForm->uuid, $staff->uuid]) }}"
          method="POST">
        @csrf
        @if($hasExisting) @method('PUT') @endif

        {{-- ── ITEM 17: EVALUATION OF PERFORMANCE ─────────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title">
                <i class="ti ti-chart-bar text-gray-400"></i>
                17. Evaluation of Performance
            </div>
            <p class="text-[11px] text-gray-400 mb-4">
                Grade on a scale of 5 to 1 in descending order (5 = Excellent, 4 = Very Good, 3 = Good, 2 = Fair, 1 = Poor).
            </p>

            @php
                $criteria = [
                    'punctuality'            => 'a. Punctuality and Devotion to Work',
                    'initiative'             => 'b. Initiative',
                    'interpersonal'          => 'c. Interpersonal and Public Relation',
                    'responsibility'         => 'd. Responsibility / Reliability',
                    'organisational_ability' => 'e. Organizational Ability',
                    'work_quality'           => 'f. Output and Quality of Work',
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

            {{-- Item 18: General Remarks --}}
            <div class="mt-4">
                <label class="wf-label">18. General Remarks <span class="text-red-500">*</span></label>
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

        {{-- ── ITEM 19: TRAINING NEEDS ─────────────────────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title">
                <i class="ti ti-school text-gray-400"></i>
                19. Training Needs
            </div>
            <p class="text-[11px] text-gray-400 mb-4">
                In completing this section you should take account of any view expressed by the person reported on.
            </p>

            <div class="mb-4">
                <label class="wf-label">
                    19(a). If performance or potential could be improved by training, please specify the needs
                </label>
                <textarea name="need_training" rows="4"
                          class="wf-input w-full resize-none"
                          {{ !$isOpen ? 'readonly' : '' }}
                          placeholder="Specify training needs…">{{ old('need_training', $trainingNeed?->need_training) }}</textarea>
            </div>

            <div>
                <label class="wf-label">
                    19(b). If they cannot be met by training on the job, please suggest in which way they might be met
                </label>
                <textarea name="other_needs" rows="4"
                          class="wf-input w-full resize-none"
                          {{ !$isOpen ? 'readonly' : '' }}
                          placeholder="Suggest alternative ways to meet training needs…">{{ old('other_needs', $trainingNeed?->other_needs) }}</textarea>
            </div>
        </div>

        {{-- ── ITEM 21: HOD RECOMMENDATION (not shown to staff) ── --}}
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
                    <select name="promotion_salary_level_id"
                            class="wf-input w-full max-w-sm"
                            {{ !$isOpen ? 'disabled' : '' }}>
                        <option value="">— Select Grade Level —</option>
                        @foreach($salaryLevels as $level)
                            <option value="{{ $level->id }}"
                                {{ old('promotion_salary_level_id', $recommendation?->promotion_salary_level_id) == $level->id ? 'selected' : '' }}>
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

</x-staff-layout>
