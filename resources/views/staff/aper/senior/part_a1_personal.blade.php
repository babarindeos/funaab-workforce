<x-staff-layout title="APER Form — Part A: Personal">

    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">{{ $aperForm->name }}</h1>
            <p class="pg-sub">Senior Non-Teaching Staff — Part A: Personal Information</p>
        </div>
        <a href="{{ route('staff.aper.index') }}" class="btn-wf-ghost btn-wf-sm">
            <i class="ti ti-arrow-left text-sm"></i> Back to APER Forms
        </a>
    </div>

    {{-- Part Progress --}}
    <div class="wf-card mb-4">
        <div class="flex items-center gap-0">
            <div class="flex items-center gap-2 flex-1">
                <div class="w-7 h-7 rounded-full bg-funaab-600 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">A</div>
                <span class="text-xs font-bold text-funaab-700">Part A</span>
                <span class="text-[10px] text-funaab-500">Personal Info</span>
            </div>
            <div class="flex-1 h-px bg-gray-200 mx-2"></div>
            <div class="flex items-center gap-2 flex-1">
                <div class="w-7 h-7 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-xs font-bold flex-shrink-0">B</div>
                <span class="text-xs font-semibold text-gray-400">Part B</span>
            </div>
            <div class="flex-1 h-px bg-gray-200 mx-2"></div>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-xs font-bold flex-shrink-0">C</div>
                <span class="text-xs font-semibold text-gray-400">Part C</span>
            </div>
        </div>
    </div>

    {{-- Step Tabs --}}
    <div class="flex gap-1 mb-4 bg-gray-100 rounded-lg p-1 w-fit">
        <div class="px-4 py-1.5 text-xs font-bold rounded-md bg-white text-funaab-700 shadow-sm">
            <i class="ti ti-user text-xs"></i> Personal
        </div>
        <div class="px-4 py-1.5 text-xs font-semibold text-gray-400 rounded-md">
            Education
        </div>
        <div class="px-4 py-1.5 text-xs font-semibold text-gray-400 rounded-md">
            University Experience 
        </div>
    </div>

    @if($readonly)
        <div class="flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-semibold px-4 py-3 rounded-lg mb-4">
            <i class="ti ti-lock text-base"></i> This APER form is closed. Displayed in read-only mode.
        </div>
    @endif

    <form action="{{ $readonly ? '#' : route('staff.aper.senior.part_a1.store', $aperForm->uuid) }}" method="POST">
        @csrf
        @if($existing) @method('PUT') @endif

        {{-- ── SECTION 1–3: NAME / DOB / PERSONAL ─────────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-user text-gray-400"></i> Personal Details</div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="wf-label">1. Surname <span class="text-red-500">*</span></label>
                    <input type="text" name="surname" value="{{ old('surname', $existing->surname ?? $staff->surname) }}"
                           class="wf-input w-full bg-gray-50" disabled required />
                    @error('surname') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="wf-label">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="firstname" value="{{ old('firstname', $existing->firstname ?? $staff->firstname) }}"
                           class="wf-input w-full bg-gray-50" disabled required />
                    @error('firstname') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="wf-label">Middle Name</label>
                    <input type="text" name="middlename" value="{{ old('middlename', $existing->middlename ?? $staff->othernames) }}"
                           class="wf-input w-full bg-gray-50" disabled />
                </div>
            </div>

            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="wf-label">2(a). Date of Birth <span class="text-red-500">*</span></label>
                    
                    <input type="date" name="dob" value="{{ old('dob', optional($existing)->dob?->format('Y-m-d') ?? optional($staff->dob)->format('Y-m-d')) }}"
                            class="wf-input w-full bg-gray-50" disabled required />
                    @error('dob') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="wf-label">2(b). Date of Mandatory Retirement <span class="text-red-500">*</span></label>
                    <input type="date" name="retirement_date" value="{{ $retirementDate }}"
                           class="wf-input w-full bg-gray-50" disabled required />
                    @error('retirement_date') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="wf-label">3(a). Marital Status <span class="text-red-500">*</span></label>
                    <select name="marital_status_id" class="wf-input w-full bg-gray-50" disabled required>
                        <option value="">— Select —</option>
                        @foreach($maritalStatuses as $ms)
                            <option value="{{ $ms->id }}" {{ old('marital_status_id', $existing->marital_status_id ?? $staff->marital_status_id) == $ms->id ? 'selected' : '' }}>
                                {{ $ms->status }}
                            </option>
                        @endforeach
                    </select>
                    @error('marital_status_id') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="wf-label">3(b). No. and Ages of Children <span class="text-red-500">*</span></label>
                    <input type="text" name="children" value="{{ old('children', $existing->children ?? '') }}"
                           placeholder="e.g. 2 children: 5, 8 years"
                           class="wf-input w-full" {{ $readonly ? 'readonly' : '' }} required />
                    @error('children') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="wf-label">3(c). Telephone No. <span class="text-red-500">*</span></label>
                    <input type="text" name="telephone_no" value="{{ old('telephone_no', $existing->telephone_no ?? $profile->phone ?? '') }}"
                           class="wf-input w-full" {{ $readonly ? 'readonly' : '' }} required />
                    @error('telephone_no') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="wf-label">3(d). E-mail Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email_address" value="{{ old('email_address', $existing->email_address ?? Auth::user()->email) }}"
                           class="wf-input w-full bg-gray-50" disabled required />
                    @error('email_address') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ── SECTION 4–5: COLLEGE / DEPARTMENT ──────────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-building text-gray-400"></i> College &amp; Department</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="wf-label">4. College / Division <span class="text-red-500">*</span></label>
                    <select name="division_id" class="wf-input w-full" disabled required>
                        <option value="">— Select College / Division —</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ old('division_id', $existing->division_id ?? $staff->employment?->division_id) == $div->id ? 'selected' : '' }}>
                                {{ $div->division_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('division_id') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="wf-label">5. Department <span class="text-red-500">*</span></label>
                    <select name="department_id" class="wf-input w-full" disabled required>
                        <option value="">— Select Department —</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $existing->department_id ?? $staff->employment?->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->department_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ── SECTION 6–10: APPOINTMENT HISTORY ──────────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-calendar-event text-gray-400"></i> Appointment History</div>

            <div class="mb-4">
                <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-2">6. Date &amp; Post on First Appointment</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="wf-label">Date</label>
                        <input type="date" name="date_first_appointment"
                               value="{{ old('date_first_appointment', optional($existing)->date_first_appointment?->format('Y-m-d') ?? optional($employment->date_funaab_first_appointment)->format('Y-m-d')) }}"
                               class="wf-input w-full bg-gray-50" disabled />
                    </div>
                    <div>
                        <label class="wf-label">Post</label>
                        <input type="text" name="post_first_appointment"
                               value="{{ old('post_first_appointment', $existing->post_first_appointment ?? $staff->employment?->first_appointment_post) }}"
                               class="wf-input w-full bg-gray-50" disabled  placeholder="e.g. Administrative Officer II" />
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-2">7. Date &amp; Post of Last Promotion</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="wf-label">Date</label>
                        <input type="date" name="date_last_appointment"
                               value="{{ old('date_last_appointment', optional($existing)->date_last_appointment?->format('Y-m-d') ?? optional($employment)->date_present_appointment?->format('Y-m-d')) }}"
                               class="wf-input w-full bg-gray-50" disabled />
                    </div>
                    <div>
                        <label class="wf-label">Post</label>
                        <input type="text" name="post_last_appointment"
                               value="{{ old('post_last_appointment', $existing->post_last_appointment ?? optional($employment)->designation->cadreRank->cadre_rank_name) }}"
                               class="wf-input w-full bg-gray-50" disabled placeholder="e.g. Senior Administrative Officer" />
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-2">8. Date &amp; Post of Current Appointment (if different from 6 above)</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="wf-label">Date</label>
                        <input type="date" name="date_current_appointment"
                               value="{{ old('date_current_appointment', $existing->date_current_appointment ?? optional($employment)->date_current_appointment?->format('Y-m-d')) }}"
                               class="wf-input w-full" {{ $readonly ? 'readonly' : '' }} />
                    </div>
                    <div>
                        <label class="wf-label">Post</label>
                        <input type="text" name="post_current_appointment"
                               value="{{ old('post_current_appointment', $existing->post_current_appointment ?? '') }}"
                               class="wf-input w-full" {{ $readonly ? 'readonly' : '' }} placeholder="e.g. Principal Administrative Officer" />
                    </div>
                </div>
            </div>

            
            <div class="mb-4">
                <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-2">9. Has Appointment been confirmed? </label>
                <div class="flex items-center gap-6 mb-3">
                    <label class="flex items-center gap-2 cursor-pointer" disabled>
                        <input type="radio" name="appointment_confirmed" value="1"
                               {{ old('appointment_confirmed', $employment?->date_confirmation ? 'checked' : '') }}
                               disabled class="text-funaab-600" />
                        <span class="text-sm font-semibold text-gray-700">Yes</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer" disabled>
                        <input type="radio" name="appointment_confirmed" value="0"
                               {{ old('appointment_confirmed', $employment?->date_confirmation == null ? 'checked' : '') }}
                               disabled class="text-funaab-600" />
                        <span class="text-sm font-semibold text-gray-700">No</span>
                    </label>
                    @error('appointment_confirmed') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div id="confirmation-date-row" class="{{ $employment->date_confirmation ? 'block' : 'hidden' }}">
                    <label class="wf-label">Date of Confirmation</label>
                    <input type="date" name="confirmation_date" disabled
                           value="{{ $employment->date_confirmation?->format('Y-m-d') }}"
                           class="wf-input w-full max-w-xs bg-gray-50" />
                </div>
            </div>

            <div>
                <label class="wf-label">10. Present Grade Level and Salary <span class="text-red-500">*</span></label>
                <input type="text" name="present_grade_level_salary"
                       value="{{ old('present_grade_level_salary', $existing->present_grade_level_salary ?? ($staff->employment->salaryStep ? $staff->employment->salaryStep->salaryLevel->salaryStructure->salary_structure_name.' - '.$staff->employment->salaryStep->salaryLevel->level.' / Step '.$staff->employment->salaryStep->step : '')) }}"
                       placeholder="e.g. GL 09 / Step 4"
                       class="wf-input w-full max-w-sm bg-gray-50" disabled required />
                @error('present_grade_level_salary') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- ── SECTION 11: CONFERENCES ─────────────────────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-presentation text-gray-400"></i> 11. Courses / Conferences / Workshops Attended During the Period</div>

            <div id="conf-tbody" class="space-y-2">
                @php
                    $confRows = old('conferences',
                        $conferences->isNotEmpty()
                            ? $conferences->pluck('conference')->toArray()
                            : ['', '']
                    );
                @endphp
                @foreach($confRows as $i => $conf)
                    <div class="conf-row flex items-start gap-2">
                        <span class="text-[11px] text-gray-400 font-semibold mt-2.5 w-5 flex-shrink-0">{{ $i + 1 }}.</span>
                        <textarea name="conferences[{{ $i }}]" rows="2"
                                  class="wf-input w-full resize-none"
                                  {{ $readonly ? 'readonly' : '' }}
                                  placeholder="Conference or workshop name, venue, date…">{{ $conf }}</textarea>
                        @if(!$readonly)
                            <button type="button" onclick="removeRow(this, '.conf-row')"
                                    class="mt-2 text-red-400 hover:text-red-600 flex-shrink-0">
                                <i class="ti ti-trash text-sm"></i>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
            @if(!$readonly)
                <button type="button" onclick="addTextRow('conf-tbody', 'conferences', confIndex++)"
                        class="mt-3 btn-wf-ghost btn-wf-sm">
                    <i class="ti ti-plus text-xs"></i> Add Entry
                </button>
            @endif
        </div>

        @if(!$readonly)
            <div class="flex items-center justify-end gap-3 mb-6">
                <a href="{{ route('staff.aper.index') }}" class="btn-wf-ghost">Cancel</a>
                <button type="submit" class="btn-wf-primary">
                    <i class="ti ti-arrow-right text-sm"></i> Save &amp; Continue to Education
                </button>
            </div>
        @endif
    </form>

    <script>
        /*
        document.querySelectorAll('input[name="appointment_confirmed"]').forEach(r => {
            r.addEventListener('change', function () {
                document.getElementById('confirmation-date-row').classList.toggle('hidden', this.value !== '1');
            });
        });
        */

        let confIndex = {{ count($confRows) }};

        function addTextRow(tbodyId, fieldName, idx) {
            const container = document.getElementById(tbodyId);
            const rowClass  = tbodyId === 'conf-tbody' ? 'conf-row' : 'text-row';
            const num       = container.querySelectorAll('div').length + 1;
            const div       = document.createElement('div');
            div.className   = `${rowClass} flex items-start gap-2`;
            div.innerHTML   = `
                <span class="text-[11px] text-gray-400 font-semibold mt-2.5 w-5 flex-shrink-0">${num}.</span>
                <textarea name="${fieldName}[${idx}]" rows="2" class="wf-input w-full resize-none" placeholder="Enter details…"></textarea>
                <button type="button" onclick="removeRow(this, '.${rowClass}')" class="mt-2 text-red-400 hover:text-red-600 flex-shrink-0">
                    <i class="ti ti-trash text-sm"></i>
                </button>`;
            container.appendChild(div);
        }

        function removeRow(btn, selector) {
            const rows = document.querySelectorAll(selector);
            if (rows.length > 1) btn.closest('div').remove();
        }
    </script>

</x-staff-layout>
