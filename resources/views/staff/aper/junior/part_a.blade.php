<x-staff-layout title="APER Form — Part A">

    {{-- Page Header --}}
    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">{{ $aperForm->name }}</h1>
            <p class="pg-sub">Junior Non-Teaching Staff — Part A: Personal Information</p>
        </div>
        <a href="{{ route('staff.aper.index') }}" class="btn-wf-ghost btn-wf-sm">
            <i class="ti ti-arrow-left text-sm"></i> Back to APER Forms
        </a>
    </div>

    {{-- Progress Indicator --}}
    <div class="wf-card mb-4">
        <div class="flex items-center gap-0">
            {{-- Part A - Active --}}
            <div class="flex items-center gap-2 flex-1">
                <div class="w-7 h-7 rounded-full bg-funaab-600 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">A</div>
                <span class="text-xs font-bold text-funaab-700">Part A</span>
                <span class="text-[10px] text-funaab-500">Personal Info</span>
            </div>
            <div class="flex-1 h-px bg-gray-200 mx-2"></div>
            {{-- Part B - Pending --}}
            <div class="flex items-center gap-2 flex-1">
                <div class="w-7 h-7 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-xs font-bold flex-shrink-0">B</div>
                <span class="text-xs font-semibold text-gray-400">Part B</span>
            </div>
            <div class="flex-1 h-px bg-gray-200 mx-2"></div>
            {{-- Part C - Pending --}}
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-xs font-bold flex-shrink-0">C</div>
                <span class="text-xs font-semibold text-gray-400">Part C</span>
            </div>
        </div>
    </div>
    @include('staff.aper._status_banner')

    <form action="{{ $readonly ? '#' : route('staff.aper.junior.part_a.store', $aperForm->uuid) }}"
          method="POST">
        @csrf
        @if($existing) @method('PUT') @endif

        {{-- ═══════════════════════════════════════
             SECTION 1 — PERSONAL DETAILS
        ════════════════════════════════════════ --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title">
                <i class="ti ti-user text-gray-400"></i> Personal Details
            </div>

            {{-- 1. Name --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="wf-label">1. Surname <span class="text-red-500">*</span></label>
                    <input type="text" name="surname"
                           value="{{ old('surname', $existing->surname ?? $staff->surname) }}"
                           class="wf-input w-full @error('surname') border-red-400 @enderror bg-gray-50"
                           disabled required />
                    @error('surname') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="wf-label">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="firstname"
                           value="{{ old('firstname', $existing->firstname ?? $staff->firstname) }}"
                           class="wf-input w-full @error('firstname') border-red-400 @enderror bg-gray-50"
                           disabled required />
                    @error('firstname') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="wf-label">Middle Name</label>
                    <input type="text" name="middlename"
                           value="{{ old('middlename', $existing->middlename ?? $staff->othernames) }}"
                           class="wf-input w-full bg-gray-50"
                           disabled />
                </div>
            </div>

            {{-- 2. Date of Birth & Retirement --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="wf-label">2(a). Date of Birth <span class="text-red-500">*</span></label>                    
                    <input type="date" name="dob" 
                            value="{{ old('dob', optional($existing)->dob?->format('Y-m-d') ?? optional($staff->dob)->format('Y-m-d')) }}"
                            class="wf-input w-full  @error('dob') border-red-400 @enderror bg-gray-50" disabled required />                           
                    @error('dob') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="wf-label">2(b). Date of Mandatory Retirement <span class="text-red-500">*</span></label>
                    <input type="date" name="retirement_date"
                           value="{{ $retirementDate }}"
                           class="wf-input w-full @error('retirement_date') border-red-400 @enderror bg-gray-50"
                           disabled required />
                    @error('retirement_date') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- 3. Marital Status, Children, Telephone, Email --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="wf-label">3(a). Marital Status <span class="text-red-500">*</span></label>
                    <select name="marital_status_id"
                            class="wf-input w-full @error('marital_status_id') border-red-400 @enderror bg-gray-50"
                            disabled required>
                        <option value="">— Select —</option>
                        @foreach($maritalStatuses as $ms)
                            <option value="{{ $ms->id }}"
                                {{ old('marital_status_id', $existing->marital_status_id ?? $staff->marital_status_id) == $ms->id ? 'selected' : '' }}>
                                {{ $ms->status }}
                            </option>
                        @endforeach
                    </select>
                    @error('marital_status_id') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="wf-label">3(b). No. and Ages of Children <span class="text-red-500">*</span></label>
                    <input type="text" name="children"
                           value="{{ old('children', $existing->children ?? '') }}"
                           placeholder="e.g. 2 children: 5, 8 years"
                           class="wf-input w-full @error('children') border-red-400 @enderror"
                           {{ $readonly ? 'readonly' : '' }} required />
                    @error('children') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="wf-label">3(c). Telephone No. <span class="text-red-500">*</span></label>
                    <input type="text" name="telephone_no"
                           value="{{ old('telephone_no', $existing->telephone_no ?? $profile->phone ?? '') }}"
                           class="wf-input w-full @error('telephone_no') border-red-400 @enderror"
                           {{ $readonly ? 'readonly' : '' }} required />
                    @error('telephone_no') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="wf-label">3(d). E-mail Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email_address"
                           value="{{ old('email_address', $existing->email_address ?? Auth::user()->email) }}"
                           class="wf-input w-full @error('email_address') border-red-400 @enderror bg-gray-50"
                           disabled required />
                    @error('email_address') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════
             SECTION 2 — DEPARTMENT / DIVISION
        ════════════════════════════════════════ --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title">
                <i class="ti ti-building text-gray-400"></i> College &amp; Department
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- 4. College / Division --}}
                <div>
                    <label class="wf-label">4. College / Division <span class="text-red-500">*</span></label>
                    <select name="division_id" id="division_id"
                            class="wf-input w-full @error('division_id') border-red-400 @enderror bg-gray-50"
                            disabled required>
                        <option value="">— Select College / Division —</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}"
                                {{ old('division_id', $existing->division_id ?? $staff->employment?->division_id) == $division->id ? 'selected' : '' }}>
                                {{ $division->division_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('division_id') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- 5. Department --}}
                <div>
                    <label class="wf-label">5. Department <span class="text-red-500">*</span></label>
                    <select name="department_id" id="department_id"
                            class="wf-input w-full @error('department_id') border-red-400 @enderror bg-gray-50"
                            disabled required>
                        <option value="">— Select Department —</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}"
                                {{ old('department_id', $existing->department_id ?? $staff->employment?->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->department_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════
             SECTION 3 — APPOINTMENT HISTORY
        ════════════════════════════════════════ --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title">
                <i class="ti ti-calendar-event text-gray-400"></i> Appointment History
            </div>

            {{-- 6. First Appointment --}}
            <div class="mb-4">
                <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-2">
                    6. Date &amp; Post on First Appointment
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="wf-label">Date</label>
                        <input type="date" name="date_first_appointment"
                               value="{{ old('date_first_appointment', optional($existing)->date_first_appointment?->format('Y-m-d') ?? optional($staff->employment?->date_funaab_first_appointment)->format('Y-m-d')) }}"
                               class="wf-input w-full bg-gray-50"
                              disabled />
                    </div>
                    <div>
                        <label class="wf-label">Post</label>
                        <input type="text" name="post_first_appointment"
                               value="{{ old('post_first_appointment', $existing->post_first_appointment ?? $staff->employment?->first_appointment_post) }}"
                               placeholder="e.g. Clerical Officer II"
                               class="wf-input w-full bg-gray-50"
                               disabled />
                    </div>
                </div>
            </div>

            {{-- 7. Last Promotion --}}
            <div class="mb-4">
                <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-2">
                    7. Date &amp; Post of Last Promotion
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="wf-label">Date</label>                      
                         <input type="date" name="date_last_appointment"
                               value="{{ old('date_last_appointment', optional($existing)->date_last_appointment?->format('Y-m-d') ?? $lastPromotion['date']) }}"
                               class="wf-input w-full bg-gray-50" disabled />
                    </div>
                    <div>
                        <label class="wf-label">Post</label>
                        <input type="text" name="post_last_appointment"
                               value="{{ old('post_last_appointment', $existing->post_last_appointment ?? $lastPromotion['post']) }}"
                               placeholder="e.g. Senior Clerical Officer"
                               class="wf-input w-full bg-gray-50"
                               disabled />
                    </div>
                </div>
            </div>

            {{-- 8. Current Appointment --}}
            <div class="mb-4">
                <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-2">
                    8. Date &amp; Post of Current Appointment (if different from 6 above)
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="wf-label">Date</label>
                        <input type="date" name="date_current_appointment"
                               value="{{ old('date_current_appointment', optional($existing)->date_current_appointment?->format('Y-m-d') ?? '') }}"
                               class="wf-input w-full"
                               {{ $readonly ? 'readonly' : '' }} />
                    </div>
                    <div>
                        <label class="wf-label">Post</label>
                        <input type="text" name="post_current_appointment"
                               value="{{ old('post_current_appointment', $existing->post_current_appointment ?? '') }}"
                               placeholder="e.g. Principal Clerical Officer"
                               class="wf-input w-full"
                               {{ $readonly ? 'readonly' : '' }} />
                    </div>
                </div>
            </div>

            {{-- 9. Appointment Confirmed --}}
            <div class="mb-4">
                <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-2">
                    9. Has Appointment been confirmed?
                </label>
                <div class="flex items-center gap-6 mb-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="appointment_confirmed" value="1"
                               {{ old('appointment_confirmed', $existing->appointment_confirmed ?? 0) == 1 ? 'checked' : '' }}
                               {{ $readonly ? 'disabled' : '' }}
                               class="text-funaab-600" />
                        <span class="text-sm font-semibold text-gray-700">Yes</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="appointment_confirmed" value="0"
                               {{ old('appointment_confirmed', $existing->appointment_confirmed ?? 1) == 0 ? 'checked' : '' }}
                               {{ $readonly ? 'disabled' : '' }}
                               class="text-funaab-600" />
                        <span class="text-sm font-semibold text-gray-700">No</span>
                    </label>
                    @error('appointment_confirmed') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div id="confirmation-date-row" class="{{ (old('appointment_confirmed', $existing->appointment_confirmed ?? 0) == 1) ? '' : 'hidden' }}">
                    <label class="wf-label">Date of Confirmation</label>
                    <input type="date" name="confirmation_date"
                           value="{{ old('confirmation_date', $existing->confirmation_date ?? optional($profile)->date_confirmation?->format('Y-m-d')) }}"
                           class="wf-input w-full max-w-xs"
                           {{ $readonly ? 'readonly' : '' }} />
                </div>
            </div>

            {{-- 10. Present Grade Level and Salary --}}
            <div>
                <label class="wf-label">10. Present Grade Level and Salary <span class="text-red-500">*</span></label>
                <input type="text" name="present_grade_level_salary"
                       value="{{ old('present_grade_level_salary', $existing->present_grade_level_salary ?? $gradeText) }}"
                       placeholder="e.g. GL 06 / Step 3"
                       class="wf-input w-full max-w-sm @error('present_grade_level_salary') border-red-400 @enderror bg-gray-50"
                       disabled required />
                @error('present_grade_level_salary') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- ═══════════════════════════════════════
             SECTION 4 — QUALIFICATIONS (Item 11)
        ════════════════════════════════════════ --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title">
                <i class="ti ti-school text-gray-400"></i> 11. Qualifications with Dates
            </div>
            <p class="text-[11px] text-gray-400 mb-3">State subjects and grade of passes where appropriate.</p>

            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden" id="qual-table">
                    <thead>
                        <tr class="bg-gray-50 text-[10.5px] font-bold tracking-wider text-gray-500">
                            <th class="px-3 py-2.5 text-left border-b border-gray-200">NAME OF INSTITUTION</th>
                            <th class="px-3 py-2.5 text-left border-b border-gray-200">DURATION FROM</th>
                            <th class="px-3 py-2.5 text-left border-b border-gray-200">DURATION TO</th>
                            <th class="px-3 py-2.5 text-left border-b border-gray-200">QUALIFICATION OBTAINED</th>
                            <th class="px-3 py-2.5 text-left border-b border-gray-200">SUBJECTS &amp; GRADES</th>
                            @if(!$readonly)
                                <th class="px-3 py-2.5 text-center border-b border-gray-200 w-10"></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="qual-tbody">
                        @php
                            $qualRows = old('qualifications',
                                $qualifications->isNotEmpty()
                                    ? $qualifications->map(fn($q) => [
                                        'institution'           => $q->institution,
                                        'from_date'             => $q->from_date,
                                        'to_date'               => $q->to_date,
                                        'qualification_obtained'=> $q->qualification_obtained,
                                        'subjects_grades'       => $q->subjects_grades,
                                    ])->toArray()
                                    : [['institution'=>'','from_date'=>'','to_date'=>'','qualification_obtained'=>'','subjects_grades'=>'']]
                            );
                        @endphp

                        @foreach($qualRows as $i => $row)
                            <tr class="qual-row border-b border-gray-100">
                                <td class="px-3 py-2">
                                    <input type="text" name="qualifications[{{ $i }}][institution]"
                                           value="{{ $row['institution'] }}"
                                           class="wf-input w-full"
                                           {{ $readonly ? 'readonly' : '' }}
                                           placeholder="Institution name" />
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" name="qualifications[{{ $i }}][from_date]"
                                           value="{{ $row['from_date'] }}"
                                           class="wf-input w-full"
                                           {{ $readonly ? 'readonly' : '' }}
                                           placeholder="e.g. 2010" />
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" name="qualifications[{{ $i }}][to_date]"
                                           value="{{ $row['to_date'] }}"
                                           class="wf-input w-full"
                                           {{ $readonly ? 'readonly' : '' }}
                                           placeholder="e.g. 2014" />
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" name="qualifications[{{ $i }}][qualification_obtained]"
                                           value="{{ $row['qualification_obtained'] }}"
                                           class="wf-input w-full"
                                           {{ $readonly ? 'readonly' : '' }}
                                           placeholder="e.g. B.Sc. Computer Science" />
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" name="qualifications[{{ $i }}][subjects_grades]"
                                           value="{{ $row['subjects_grades'] ?? '' }}"
                                           class="wf-input w-full"
                                           {{ $readonly ? 'readonly' : '' }}
                                           placeholder="e.g. English C4, Maths B3" />
                                </td>
                                @if(!$readonly)
                                    <td class="px-3 py-2 text-center">
                                        <button type="button" onclick="removeQualRow(this)"
                                                class="text-red-400 hover:text-red-600">
                                            <i class="ti ti-trash text-sm"></i>
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(!$readonly)
                <button type="button" onclick="addQualRow()"
                        class="mt-3 btn-wf-ghost btn-wf-sm">
                    <i class="ti ti-plus text-xs"></i> Add Row
                </button>
            @endif
        </div>

        {{-- Form Actions --}}
        @if(!$readonly)
            <div class="flex items-center justify-end gap-3 mb-6">
                <a href="{{ route('staff.aper.index') }}" class="btn-wf-ghost">Cancel</a>
                <button type="submit" class="btn-wf-primary">
                    <i class="ti ti-device-floppy text-sm"></i> Save Part A &amp; Continue
                </button>
            </div>
        @endif

    </form>

    <script>
        // Toggle confirmation date visibility
        document.querySelectorAll('input[name="appointment_confirmed"]').forEach(radio => {
            radio.addEventListener('change', function () {
                document.getElementById('confirmation-date-row').classList.toggle('hidden', this.value !== '1');
            });
        });

        // Dynamic qualification rows
        let qualIndex = {{ count($qualRows) }};

        function addQualRow() {
            const tbody = document.getElementById('qual-tbody');
            const tr = document.createElement('tr');
            tr.className = 'qual-row border-b border-gray-100';
            tr.innerHTML = `
                <td class="px-3 py-2"><input type="text" name="qualifications[${qualIndex}][institution]" class="wf-input w-full" placeholder="Institution name" /></td>
                <td class="px-3 py-2"><input type="text" name="qualifications[${qualIndex}][from_date]" class="wf-input w-full" placeholder="e.g. 2010" /></td>
                <td class="px-3 py-2"><input type="text" name="qualifications[${qualIndex}][to_date]" class="wf-input w-full" placeholder="e.g. 2014" /></td>
                <td class="px-3 py-2"><input type="text" name="qualifications[${qualIndex}][qualification_obtained]" class="wf-input w-full" placeholder="e.g. B.Sc." /></td>
                <td class="px-3 py-2"><input type="text" name="qualifications[${qualIndex}][subjects_grades]" class="wf-input w-full" placeholder="e.g. English C4, Maths B3" /></td>
                <td class="px-3 py-2 text-center"><button type="button" onclick="removeQualRow(this)" class="text-red-400 hover:text-red-600"><i class="ti ti-trash text-sm"></i></button></td>
            `;
            tbody.appendChild(tr);
            qualIndex++;
        }

        function removeQualRow(btn) {
            const rows = document.querySelectorAll('.qual-row');
            if (rows.length > 1) {
                btn.closest('tr').remove();
            }
        }
    </script>

</x-staff-layout>
