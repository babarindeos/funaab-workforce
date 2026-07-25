<x-admin-layout title="Add Staff">

    <div class="flex items-center gap-2 mb-5">
        <a href="{{ route('admin.staff.index') }}" class="text-gray-400 hover:text-funaab-700 transition-colors">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="pg-title">Add New Staff</h1>
            <p class="pg-sub">Fill in the staff personal and employment details</p>
        </div>
    </div>

    <form action="{{ route('admin.staff.store') }}" method="POST" x-data="staffForm()">
        @csrf

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
            <button type="button" @click="tab='account'"
                    :class="tab==='account' ? 'border-funaab-600 text-funaab-700 font-bold' : 'border-transparent text-gray-400'"
                    class="px-5 py-2.5 text-xs font-bold border-b-2 transition-colors">
                <i class="ti ti-mail mr-1"></i> Portal Access
            </button>
        </div>

        {{-- ── PERSONAL INFO TAB ── --}}
        <div x-show="tab==='personal'" class="max-w-3xl">
            <div class="wf-card mb-4">
                <div class="wf-card-title"><i class="ti ti-user text-gray-400"></i> Personal Information</div>
                <div class="grid grid-cols-2 gap-4">

                    @php
                    $input = 'w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50';
                    $label = 'block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5';
                    @endphp

                    {{-- Title --}}
                    <div>
                        <label class="{{ $label }}">TITLE <span class="text-red-500">*</span></label>
                        <select name="staff_title_id" class="{{ $input }}" required>
                            <option value="">— Select Title —</option>
                            @foreach($titles as $t)
                                <option value="{{ $t->id }}" {{ old('staff_title_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('staff_title_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Staff No --}}
                    <div>
                        <label class="{{ $label }}">STAFF NUMBER <span class="text-red-500">*</span></label>
                        <input type="text" name="staff_no" value="{{ old('staff_no') }}"
                               placeholder="e.g. FUNAAB/001234" class="{{ $input }}" required />
                        @error('staff_no')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Surname --}}
                    <div>
                        <label class="{{ $label }}">SURNAME <span class="text-red-500">*</span></label>
                        <input type="text" name="surname" value="{{ old('surname') }}"
                               class="{{ $input }}" required />
                        @error('surname')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Firstname --}}
                    <div>
                        <label class="{{ $label }}">FIRST NAME <span class="text-red-500">*</span></label>
                        <input type="text" name="firstname" value="{{ old('firstname') }}"
                               class="{{ $input }}" required />
                        @error('firstname')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Othernames --}}
                    <div>
                        <label class="{{ $label }}">OTHER NAMES</label>
                        <input type="text" name="othernames" value="{{ old('othernames') }}"
                               class="{{ $input }}" />
                    </div>

                    {{-- DOB --}}
                    <div>
                        <label class="{{ $label }}">DATE OF BIRTH <span class="text-red-500">*</span></label>
                        <input type="date" name="dob" value="{{ old('dob') }}"
                               class="{{ $input }}" required />
                        @error('dob')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Gender --}}
                    <div>
                        <label class="{{ $label }}">GENDER <span class="text-red-500">*</span></label>
                        <select name="gender_id" class="{{ $input }}" required>
                            <option value="">— Select Gender —</option>
                            @foreach($genders as $g)
                                <option value="{{ $g->id }}" {{ old('gender_id') == $g->id ? 'selected' : '' }}>
                                    {{ $g->gender_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('gender_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Marital Status --}}
                    <div>
                        <label class="{{ $label }}">MARITAL STATUS <span class="text-red-500">*</span></label>
                        <select name="marital_status_id" class="{{ $input }}" required>
                            <option value="">— Select —</option>
                            @foreach($maritalStatuses as $ms)
                                <option value="{{ $ms->id }}" {{ old('marital_status_id') == $ms->id ? 'selected' : '' }}>
                                    {{ $ms->status }}
                                </option>
                            @endforeach
                        </select>
                        @error('marital_status_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- State --}}
                    <div>
                        <label class="{{ $label }}">STATE <span class="text-red-500">*</span></label>
                        <select name="state_id" id="state_id" class="{{ $input }}" required>
                            <option value="">— Select —</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>
                                    {{ $state->state_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('state_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- LGA --}}
                    <div>
                        <label class="{{ $label }}">LGA OF ORIGIN <span class="text-red-500">*</span></label>
                        <select name="lga_id" id="lga_id" class="{{ $input }}" required>
                            <option value="">— Select State first —</option>
                        </select>
                        @error('lga_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>


                    {{-- SENATORIAL DISTRICT --}}
                    <div>
                        <label class="{{ $label }}">SENATORIAL DISTRICT <span class="text-red-500">*</span></label>
                        <select name="senatorial_district_id" id="senatorial_district_id" class="{{ $input }}" required>
                            <option value="">— Select LGA first —</option>
                        </select>
                        @error('senatorial_district_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>


                    {{-- GEO ZONE --}}
                    <div>
                        <label class="{{ $label }}">GEO ZONE <span class="text-red-500">*</span></label>
                        <select name="geo_zone_id" id="geo_zone_id" class="{{ $input }}" required>
                            <option value="">— Select Geo Zone —</option>
                            @foreach($geoZones as $zone)
                                <option value="{{ $zone->id }}" {{ old('geo_zone_id') == $zone->id ? 'selected' : '' }}>
                                    {{ $zone->zone }}
                                </option>
                            @endforeach
                        </select>
                        @error('geo_zone_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                       
                    {{-- Nationality --}}
                    <div>
                        <label class="{{ $label }}">NATIONALITY</label>
                        <input type="text" name="nationality" value="{{ old('nationality') }}"
                               class="{{ $input }}" />
                    </div>


                    {{-- Staff Status --}}
                    <div>
                        <label class="{{ $label }}">STAFF STATUS <span class="text-red-500">*</span></label>
                        <select name="staff_status_id" class="{{ $input }}" required>
                            <option value="">— Select Status —</option>
                            @foreach($statuses as $st)
                                <option value="{{ $st->id }}" {{ old('staff_status_id') == $st->id ? 'selected' : '' }}>
                                    {{ $st->status }}
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
                                <option value="{{ $c->id }}" {{ old('staff_category_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->category }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    

                    <div>
                        <label class="{{ $label }}">EMPLOYMENT TYPE</label>
                        <select name="employment_type_id" class="{{ $input }}">
                            <option value="">— Select Type —</option>
                            @foreach($employmentTypes as $et)
                                <option value="{{ $et->id }}" {{ old('employment_type_id') == $et->id ? 'selected' : '' }}>
                                    {{ $et->employment_type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="{{ $label }}">IPPIS NUMBER</label>
                        <input type="text" name="ippis_no" value="{{ old('ippis_no') }}"
                               placeholder="IPPIS No." class="{{ $input }}" />
                    </div>

                    <div>
                        <label class="{{ $label }}">DIVISION</label>
                        <select name="division_id" class="{{ $input }}">
                            <option value="">— Select Division —</option>
                            @foreach($divisions as $div)
                                <option value="{{ $div->id }}" {{ old('division_id') == $div->id ? 'selected' : '' }}>
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
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->department_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <div>
                        <label class="{{ $label }}">HIGHEST DEGREE</label>
                        <select name="highest_degree" class="{{ $input }}">
                            <option value="">— Select Degree —</option>
                            @foreach($degrees as $degree)
                                <option value="{{ $degree->id }}" {{ old('degree_id') == $degree->id ? 'selected' : '' }}>
                                    {{ $degree->degree_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="{{ $label }}">DESIGNATION</label>
                        <select name="designation_id" class="{{ $input }}">
                            <option value="">— Select Designation —</option>
                            @foreach($designations as $des)
                                <option value="{{ $des->id }}" {{ old('designation_id') == $des->id ? 'selected' : '' }}>
                                    {{ $des->cadreRank?->cadre->cadre_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="{{ $label }}">SALARY STRUCTURE</label>
                        <select name="salary_structure_id" class="{{ $input }}">
                            <option value="">— Select Structure —</option>
                            @foreach($salaryStructures as $ss)
                                <option value="{{ $ss->id }}" {{ old('salary_structure_id') == $ss->id ? 'selected' : '' }}>
                                    {{ $ss->salary_structure_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="{{ $label }}">SALARY STEP (GRADE LEVEL / STEP)</label>
                        <select name="salary_step_id" class="{{ $input }}">
                            <option value="">— Select Step —</option>
                            @foreach($salarySteps as $step)
                                <option value="{{ $step->id }}" {{ old('salary_step_id') == $step->id ? 'selected' : '' }}>
                                    GL {{ $step->salaryLevel?->level ?? '?' }} / Step {{ $step->step ?? '?' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="{{ $label }}">DATE OF FIRST PUBLIC SERVICE APPOINTMENT</label>
                        <input type="date" name="date_public_first_appointment"
                               value="{{ old('date_public_first_appointment') }}" class="{{ $input }}" />
                    </div>

                    <div>
                        <label class="{{ $label }}">DATE OF FIRST FUNAAB APPOINTMENT</label>
                        <input type="date" name="date_funaab_first_appointment"
                               value="{{ old('date_funaab_first_appointment') }}" class="{{ $input }}" />
                    </div>

                    <div>
                        <label class="{{ $label }}">POST OF FIRST FUNAAB APPOINTMENT</label>
                        <input type="text" name="first_appointment_post"
                               value="{{ old('first_appointment_post') }}" class="{{ $input }}" />
                    </div>

                    <div>
                        <label class="{{ $label }}">DATE OF CONFIRMATION</label>
                        <input type="date" name="date_confirmation"
                               value="{{ old('date_confirmation') }}" class="{{ $input }}" />
                    </div>

                    <div>
                        <label class="{{ $label }}">DATE OF PRESENT APPOINTMENT</label>
                        <input type="date" name="date_present_appointment"
                               value="{{ old('date_present_appointment') }}" class="{{ $input }}" />
                    </div>

                </div>
            </div>
            <div class="flex justify-between gap-2">
                <button type="button" @click="tab='personal'" class="btn-wf-ghost">
                    <i class="ti ti-arrow-left text-sm"></i> Back
                </button>
                <button type="button" @click="tab='account'" class="btn-wf-primary">
                    Next: Portal Access <i class="ti ti-arrow-right text-sm"></i>
                </button>
            </div>
        </div>

        {{-- ── ACCOUNT / PORTAL ACCESS TAB ── --}}
        <div x-show="tab==='account'" class="max-w-3xl">
            <div class="wf-card mb-4">
                <div class="wf-card-title"><i class="ti ti-mail text-gray-400"></i> Portal Access</div>
                <p class="text-xs text-gray-500 mb-4 -mt-1">
                    A staff portal account will be created with this email.
                    A temporary password will be generated and sent to the staff member.
                </p>
                <div>
                    <label class="{{ $label }}">EMAIL ADDRESS <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="staff@funaab.edu.ng" class="{{ $input }}" required />
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="mt-3 p-3 bg-funaab-50 rounded-lg border border-funaab-100 flex items-start gap-2">
                    <i class="ti ti-info-circle text-funaab-600 flex-shrink-0 mt-0.5"></i>
                    <p class="text-xs text-funaab-700">
                        A random 8-character password will be auto-generated and emailed to the staff member
                        upon successful creation. They can change it on first login.
                    </p>
                </div>
            </div>
            <div class="flex justify-between gap-2">
                <button type="button" @click="tab='employment'" class="btn-wf-ghost">
                    <i class="ti ti-arrow-left text-sm"></i> Back
                </button>
                <button type="submit" class="btn-wf-primary">
                    <i class="ti ti-device-floppy text-sm"></i> Create Staff
                </button>
            </div>
        </div>

    </form>

    <script>
    function staffForm() {
        return {
            tab: '{{ $errors->any() ? "personal" : "personal" }}',
        }
    }
    </script>

    <script>
        // ── Cascading dropdowns: State → LGA → Senatorial District ──
        const stateSelect      = document.getElementById('state_id');
        const lgaSelect        = document.getElementById('lga_id');
        const senatorialSelect = document.getElementById('senatorial_district_id');

        // State → fetch LGAs
        stateSelect.addEventListener('change', function () {
            const stateId = this.value;

            // Reset downstream selects
            lgaSelect.innerHTML        = '<option value="">— Loading… —</option>';
            senatorialSelect.innerHTML = '<option value="">— Select LGA first —</option>';

            if (!stateId) {
                lgaSelect.innerHTML = '<option value="">— Select State first —</option>';
                return;
            }

            fetch(`/api/lgas/${stateId}`)
                .then(res => res.json())
                .then(data => {
                    lgaSelect.innerHTML = '<option value="">— Select LGA —</option>';
                    data.forEach(lga => {
                        const option = document.createElement('option');
                        option.value       = lga.id;
                        option.textContent = lga.lga_name;
                        lgaSelect.appendChild(option);
                    });
                })
                .catch(() => {
                    lgaSelect.innerHTML = '<option value="">— Failed to load LGAs —</option>';
                });
        });

        // LGA → fetch Senatorial Districts
        lgaSelect.addEventListener('change', function () {
            const lgaId = this.value;

            

            senatorialSelect.innerHTML = '<option value="">— Loading… —</option>';

            if (!lgaId) {
                senatorialSelect.innerHTML = '<option value="">— Select LGA first —</option>';
                return;
            }

            fetch(`/api/senatorial-districts/${lgaId}`)
                .then(res => res.json())
                .then(data => {
                    senatorialSelect.innerHTML = '<option value="">— Select Senatorial District —</option>';
                    data.forEach(district => {
                        const option = document.createElement('option');
                        option.value       = district.id;
                        option.textContent = district.senatorial_district_name;
                        senatorialSelect.appendChild(option);
                    });
                })
                .catch(() => {
                    senatorialSelect.innerHTML = '<option value="">— Failed to load districts —</option>';
                });
        });

        // Re-populate on validation error (if old values exist)
        @if(old('state_id'))
            stateSelect.value = '{{ old('state_id') }}';
            stateSelect.dispatchEvent(new Event('change'));

            // After LGAs load, set the old lga_id and trigger senatorial fetch
            stateSelect.addEventListener('change', function () {
                setTimeout(() => {
                    lgaSelect.value = '{{ old('lga_id') }}';
                    if (lgaSelect.value) {
                        lgaSelect.dispatchEvent(new Event('change'));
                        setTimeout(() => {
                            senatorialSelect.value = '{{ old('senatorial_district_id') }}';
                        }, 500);
                    }
                }, 500);
            }, { once: true });
        @endif
    </script>

</x-admin-layout>