<x-admin-layout title="Edit Staff">

    <div class="flex items-center gap-2 mb-5">
        <a href="{{ route('admin.staff.show', $staff) }}" class="text-gray-400 hover:text-funaab-700 transition-colors">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="pg-title">Edit Staff Record</h1>
            <p class="pg-sub">{{ $staff->staff_no }} — {{ $staff->surname }}, {{ $staff->firstname }}</p>
        </div>
    </div>

    <form action="{{ route('admin.staff.update', $staff) }}" method="POST" x-data="{ tab: 'personal' }">
        @csrf @method('PUT')

        {{-- Tab Navigation --}}
        <div class="flex gap-0 border-b border-gray-200 mb-5">
            <button type="button" @click="tab='personal'"
                    :class="tab==='personal' ? 'border-funaab-600 text-funaab-700 font-bold' : 'border-transparent text-gray-400'"
                    class="px-5 py-2.5 text-xs font-bold border-b-2 transition-colors">
                <i class="ti ti-user mr-1"></i> Personal Info
            </button>
            <button type="button" @click="tab='employment'"
                    :class="tab==='employment' ? 'border-funaab-600 text-funaab-700 font-bold' : 'border-transparent text-gray-400'"
                    class="px-5 py-2.5 text-xs font-bold border-b-2 transition-colors">
                <i class="ti ti-briefcase mr-1"></i> Employment
            </button>
        </div>

        @php
        $input = 'w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50';
        $label = 'block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5';
        @endphp

        {{-- ── PERSONAL INFO TAB ── --}}
        <div x-show="tab==='personal'" class="max-w-3xl">
            <div class="wf-card mb-4">
                <div class="wf-card-title"><i class="ti ti-user text-gray-400"></i> Personal Information</div>
                <div class="grid grid-cols-2 gap-4">

                    <div>
                        <label class="{{ $label }}">TITLE <span class="text-red-500">*</span></label>
                        <select name="staff_title_id" class="{{ $input }}" required>
                            <option value="">— Select Title —</option>
                            @foreach($titles as $t)
                                <option value="{{ $t->id }}"
                                    {{ old('staff_title_id', $staff->staff_title_id) == $t->id ? 'selected' : '' }}>
                                    {{ $t->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('staff_title_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">STAFF NUMBER <span class="text-red-500">*</span></label>
                        <input type="text" name="staff_no"
                               value="{{ old('staff_no', $staff->staff_no) }}"
                               class="{{ $input }}" required />
                        @error('staff_no')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">SURNAME <span class="text-red-500">*</span></label>
                        <input type="text" name="surname"
                               value="{{ old('surname', $staff->surname) }}"
                               class="{{ $input }}" required />
                        @error('surname')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">FIRST NAME <span class="text-red-500">*</span></label>
                        <input type="text" name="firstname"
                               value="{{ old('firstname', $staff->firstname) }}"
                               class="{{ $input }}" required />
                        @error('firstname')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">OTHER NAMES</label>
                        <input type="text" name="othernames"
                               value="{{ old('othernames', $staff->othernames) }}"
                               class="{{ $input }}" />
                    </div>

                    <div>
                        <label class="{{ $label }}">DATE OF BIRTH <span class="text-red-500">*</span></label>
                        <input type="date" name="dob"
                               value="{{ old('dob', $staff->dob?->format('Y-m-d')) }}"
                               class="{{ $input }}" required />
                        @error('dob')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">GENDER <span class="text-red-500">*</span></label>
                        <select name="gender_id" class="{{ $input }}" required>
                            <option value="">— Select Gender —</option>
                            @foreach($genders as $g)
                                <option value="{{ $g->id }}"
                                    {{ old('gender_id', $staff->gender_id) == $g->id ? 'selected' : '' }}>
                                    {{ $g->gender }}
                                </option>
                            @endforeach
                        </select>
                        @error('gender_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">MARITAL STATUS <span class="text-red-500">*</span></label>
                        <select name="marital_status_id" class="{{ $input }}" required>
                            <option value="">— Select —</option>
                            @foreach($maritalStatuses as $ms)
                                <option value="{{ $ms->id }}"
                                    {{ old('marital_status_id', $staff->marital_status_id) == $ms->id ? 'selected' : '' }}>
                                    {{ $ms->marital_status }}
                                </option>
                            @endforeach
                        </select>
                        @error('marital_status_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">LGA OF ORIGIN <span class="text-red-500">*</span></label>
                        <select name="lga_id" class="{{ $input }}" required>
                            <option value="">— Select LGA —</option>
                            @foreach($lgas as $lga)
                                <option value="{{ $lga->id }}"
                                    {{ old('lga_id', $staff->lga_id) == $lga->id ? 'selected' : '' }}>
                                    {{ $lga->lga_name }} ({{ $lga->state?->state_name }})
                                </option>
                            @endforeach
                        </select>
                        @error('lga_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">STAFF STATUS <span class="text-red-500">*</span></label>
                        <select name="staff_status_id" class="{{ $input }}" required>
                            <option value="">— Select Status —</option>
                            @foreach($statuses as $st)
                                <option value="{{ $st->id }}"
                                    {{ old('staff_status_id', $staff->staff_status_id) == $st->id ? 'selected' : '' }}>
                                    {{ $st->staff_status }}
                                </option>
                            @endforeach
                        </select>
                        @error('staff_status_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" @click="tab='employment'" class="btn-wf-primary">
                    Next: Employment <i class="ti ti-arrow-right text-sm"></i>
                </button>
            </div>
        </div>

        {{-- ── EMPLOYMENT TAB ── --}}
        <div x-show="tab==='employment'" class="max-w-3xl">
            <div class="wf-card mb-4">
                <div class="wf-card-title"><i class="ti ti-briefcase text-gray-400"></i> Employment Details</div>
                <div class="grid grid-cols-2 gap-4">

                    <div>
                        <label class="{{ $label }}">STAFF CATEGORY</label>
                        <select name="staff_category_id" class="{{ $input }}">
                            <option value="">— Select Category —</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}"
                                    {{ old('staff_category_id', $staff->employment?->staff_category_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->staff_category }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="{{ $label }}">EMPLOYMENT TYPE</label>
                        <select name="employment_type_id" class="{{ $input }}">
                            <option value="">— Select Type —</option>
                            @foreach($employmentTypes as $et)
                                <option value="{{ $et->id }}"
                                    {{ old('employment_type_id', $staff->employment?->employment_type_id) == $et->id ? 'selected' : '' }}>
                                    {{ $et->employment_type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="{{ $label }}">IPPIS NUMBER</label>
                        <input type="text" name="ippis_no"
                               value="{{ old('ippis_no', $staff->employment?->ippis_no) }}"
                               class="{{ $input }}" />
                    </div>

                    <div>
                        <label class="{{ $label }}">DIVISION</label>
                        <select name="division_id" class="{{ $input }}">
                            <option value="">— Select Division —</option>
                            @foreach($divisions as $div)
                                <option value="{{ $div->id }}"
                                    {{ old('division_id', $staff->employment?->division_id) == $div->id ? 'selected' : '' }}>
                                    {{ $div->division_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="{{ $label }}">DEPARTMENT</label>
                        <select name="department_id" class="{{ $input }}">
                            <option value="">— Select Department —</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}"
                                    {{ old('department_id', $staff->employment?->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->department_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="{{ $label }}">DESIGNATION</label>
                        <select name="designation_id" class="{{ $input }}">
                            <option value="">— Select Designation —</option>
                            @foreach($designations as $des)
                                <option value="{{ $des->id }}"
                                    {{ old('designation_id', $staff->employment?->designation_id) == $des->id ? 'selected' : '' }}>
                                    {{ $des->cadreRank?->rank_name ?? 'Designation #'.$des->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="{{ $label }}">SALARY STRUCTURE</label>
                        <select name="salary_structure_id" class="{{ $input }}">
                            <option value="">— Select Structure —</option>
                            @foreach($salaryStructures as $ss)
                                <option value="{{ $ss->id }}"
                                    {{ old('salary_structure_id', $staff->employment?->salary_structure_id) == $ss->id ? 'selected' : '' }}>
                                    {{ $ss->salary_structure }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="{{ $label }}">SALARY STEP (GRADE LEVEL / STEP)</label>
                        <select name="salary_step_id" class="{{ $input }}">
                            <option value="">— Select Step —</option>
                            @foreach($salarySteps as $step)
                                <option value="{{ $step->id }}"
                                    {{ old('salary_step_id', $staff->employment?->salary_step_id) == $step->id ? 'selected' : '' }}>
                                    GL {{ $step->salaryLevel?->level ?? '?' }} / Step {{ $step->step ?? '?' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="{{ $label }}">DATE OF FIRST PUBLIC SERVICE APPOINTMENT</label>
                        <input type="date" name="date_public_first_appointment"
                               value="{{ old('date_public_first_appointment', $staff->employment?->date_public_first_appointment?->format('Y-m-d')) }}"
                               class="{{ $input }}" />
                    </div>

                    <div>
                        <label class="{{ $label }}">DATE OF FIRST FUNAAB APPOINTMENT</label>
                        <input type="date" name="date_funaab_first_appointment"
                               value="{{ old('date_funaab_first_appointment', $staff->employment?->date_funaab_first_appointment?->format('Y-m-d')) }}"
                               class="{{ $input }}" />
                    </div>

                    <div>
                        <label class="{{ $label }}">DATE OF CONFIRMATION</label>
                        <input type="date" name="date_confirmation"
                               value="{{ old('date_confirmation', $staff->employment?->date_confirmation?->format('Y-m-d')) }}"
                               class="{{ $input }}" />
                    </div>

                    <div>
                        <label class="{{ $label }}">DATE OF PRESENT APPOINTMENT</label>
                        <input type="date" name="date_present_appointment"
                               value="{{ old('date_present_appointment', $staff->employment?->date_present_appointment?->format('Y-m-d')) }}"
                               class="{{ $input }}" />
                    </div>

                </div>
            </div>

            <div class="flex justify-between gap-2">
                <button type="button" @click="tab='personal'" class="btn-wf-ghost">
                    <i class="ti ti-arrow-left text-sm"></i> Back
                </button>
                <div class="flex gap-2">
                    <a href="{{ route('admin.staff.show', $staff) }}" class="btn-wf-ghost">Cancel</a>
                    <button type="submit" class="btn-wf-primary">
                        <i class="ti ti-device-floppy text-sm"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>

    </form>

</x-admin-layout>