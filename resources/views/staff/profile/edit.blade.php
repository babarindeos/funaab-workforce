<x-staff-layout title="Edit Profile">

    <div class="flex items-center gap-2 mb-5">
        <a href="{{ route('staff.profile.myprofile') }}" class="text-gray-400 hover:text-funaab-700 transition-colors">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="pg-title">Edit Profile</h1>
            <p class="pg-sub">Update your personal and employment information</p>
        </div>
    </div>

    <form action="{{ route('staff.profile.myprofile.update') }}" method="POST"
          x-data="{ tab: 'personal' }">
        @csrf @method('PUT')

        {{-- Tab nav --}}
        <div class="flex border-b border-gray-200 mb-5">
            <button type="button" @click="tab='personal'"
                    :class="tab==='personal' ? 'border-funaab-600 text-funaab-700 font-bold' : 'border-transparent text-gray-400'"
                    class="px-5 py-2.5 text-xs font-bold border-b-2 transition-colors">
                <i class="ti ti-user mr-1"></i> Personal
            </button>
            <button type="button" @click="tab='nok'"
                    :class="tab==='nok' ? 'border-funaab-600 text-funaab-700 font-bold' : 'border-transparent text-gray-400'"
                    class="px-5 py-2.5 text-xs font-bold border-b-2 transition-colors">
                <i class="ti ti-users mr-1"></i> Next of Kin
            </button>
            <button type="button" @click="tab='name_change'"
                    :class="tab==='name_change' ? 'border-funaab-600 text-funaab-700 font-bold' : 'border-transparent text-gray-400'"
                    class="px-5 py-2.5 text-xs font-bold border-b-2 transition-colors hidden">
                <i class="ti ti-pencil mr-1"></i> Name Change
            </button>
        </div>

        @php
            $inp = 'w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50 bg-white';
            $lbl = 'block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5';
        @endphp

        {{-- Personal tab --}}
        <div x-show="tab==='personal'" class="max-w-3xl">
            <div class="wf-card mb-4">
                <div class="wf-card-title"><i class="ti ti-user text-gray-400"></i> Personal Information (Read-only)</div>
                <div class="p-3 bg-funaab-50 border border-funaab-100 rounded-lg text-xs text-funaab-700 flex items-center gap-2 mb-3">
                    <i class="ti ti-info-circle flex-shrink-0"></i>
                    Personal Imformation can only be updated by HR / Admin. Contact the Registry for changes.
                </div>
                <div class="grid grid-cols-2 gap-4">
                    @php $ro = 'w-full px-3 py-2 border border-gray-100 rounded-lg text-sm bg-gray-50 text-gray-500 cursor-not-allowed'; @endphp
                    <div>
                        <label class="{{ $lbl }}">TITLE</label>
                        <input type="text" name="title" value="{{ old('title', $profile->user->staff->title->title) }}"  class="{{ $ro }}" readonly />
                    </div>
                    <div>
                        <label class="{{ $lbl }}">PHONE <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone', $profile->phone) }}" class="{{ $ro }}" required readonly/>
                        @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="{{ $lbl }}">DATE OF BIRTH</label>
                        <input type="text" name="dob" value="{{ old('dob', $profile->user->staff->dob->format('d-M-Y')) }}" class="{{ $ro }}" readonly placeholder="dd/mm/yyyy" />
                    </div>
                    <div>
                        <label class="{{ $lbl }}">GENDER</label>
                        <input type="text" name="gender" value="{{ old('gender', $profile->user->staff->gender->gender_name) }}" class="{{ $ro }}" readonly />
                    </div>
                    <div>
                        <label class="{{ $lbl }}">STATE OF ORIGIN</label>
                        <input type="text" name="state" value="{{ old('state', $profile->user->staff->state->state_name) }}" class="{{ $ro }}" readonly />
                    </div>
                    <div>
                        <label class="{{ $lbl }}">LGA OF ORIGIN</label>
                        <input type="text" name="lga" value="{{ old('lga', $profile->user->staff->lga->lga_name ) }}" class="{{ $ro }}" readonly/>
                    </div>
                    <div>
                        <label class="{{ $lbl }}">GEO-POLITICAL ZONE</label>
                        <input type="text" name="geopolitical_zone" value="{{ old('geopolitical_zone', $profile->user->staff->state->geoPolZone->zone) }}" class="{{ $ro }}" readonly />
                    </div>
                    <div>
                        <label class="{{ $lbl }}">SENATORIAL DISTRICT</label>
                        <input type="text" name="senatorial_district" value="{{ old('senatorial_district', $profile->user->staff->senatorialDistrict->senatorial_district_name) }}" class="{{ $ro }}" readonly />
                    </div>
                    <div>
                        <label class="{{ $lbl }}">NATIONALITY</label>
                        <input type="text" name="nationality" value="{{ old('nationality', $profile->user->staff->nationality) }}" class="{{ $ro }}" readonly />
                    </div>
                    <div>
                        <label class="{{ $lbl }}">BIRTH PLACE</label>
                        <input type="text" name="birth_place" value="{{ old('birth_place', $profile->user->staff->birth_place) }}" class="{{ $ro }}" readonly />
                    </div>
                    <div class="col-span-2">
                        <label class="{{ $lbl }}">PERMANENT HOME ADDRESS</label>
                        <textarea name="permanent_home_address" rows="2"
                                  class="{{ $ro }} resize-none" readonly>{{ old('permanent_home_address', $profile->home_address) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="wf-card mb-4">
                <div class="wf-card-title"><i class="ti ti-briefcase text-gray-400"></i> Employment (Read-only)</div>
                <div class="p-3 bg-funaab-50 border border-funaab-100 rounded-lg text-xs text-funaab-700 flex items-center gap-2 mb-3">
                    <i class="ti ti-info-circle flex-shrink-0"></i>
                    Employment details can only be updated by HR / Admin. Contact the Registry for changes.
                </div>
                <div class="grid grid-cols-2 gap-3">
                    
                    <div><label class="{{ $lbl }}">DESIGNATION</label><input type="text" value="{{ $profile->designation }}" class="{{ $ro }}" readonly /></div>
                    <div><label class="{{ $lbl }}">CADRE</label><input type="text" value="{{ $profile->user->staff->employment->designation->cadreRank->cadre->cadre_name }}" class="{{ $ro }}" readonly /></div>
                    <div><label class="{{ $lbl }}">SALARY STRUCTURE</label><input type="text" value="{{ $profile->user->staff->employment->salaryStructure->salary_structure_name }}" class="{{ $ro }}" readonly /></div>
                    <div><label class="{{ $lbl }}">GRADE LEVEL / STEP</label><input type="text" value="GL {{ $profile->user->staff->employment->designation->salaryLevel->level }} / Step {{ $profile->user->staff->employment->salaryStep->step }}" class="{{ $ro }}" readonly /></div>
                    <div><label class="{{ $lbl }}">STAFF STATUS</label><input type="text" value="{{ $profile->user->staff->staffStatus->status }}" class="{{ $ro }}" readonly /></div>
                    <div><label class="{{ $lbl }}">IPPIS NO.</label><input type="text" value="{{ $profile->user->staff->employment->ippis_no }}" class="{{ $ro }}" readonly /></div>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('staff.profile.myprofile') }}" class="btn-wf-ghost">Cancel</a>
                <button type="button" @click="tab='nok'" class="btn-wf-ghost">
                    Next <i class="ti ti-arrow-right text-sm"></i>
                </button>
                <button type="submit" class="btn-wf-primary hidden">
                    <i class="ti ti-device-floppy text-sm"></i> Save Changes
                </button>
            </div>
        </div>

        {{-- Next of Kin tab --}}
        <div x-show="tab==='nok'" class="max-w-3xl">
            <div class="wf-card mb-4">
                <div class="wf-card-title"><i class="ti ti-users text-gray-400"></i> Next of Kin</div>
                <div class="p-3 bg-funaab-50 border border-funaab-100 rounded-lg text-xs text-funaab-700 flex items-center gap-2 mb-3">
                    <i class="ti ti-info-circle flex-shrink-0"></i>
                    Next of Kin can only be updated by HR / Admin. Contact the Registry for changes.
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="{{ $lbl }}">FULL NAMES <span class="text-red-500">*</span></label>
                        <input type="text" name="nok_fullnames" value="{{ old('nok_fullnames', $profile->user->staff->nextOfKin?->surname ) }}" class="{{ $ro }}" readonly />
                    </div>
                    <div>
                        <label class="{{ $lbl }}">PHONE</label>
                        <input type="text" name="nok_phone" value="{{ old('nok_phone', $profile->user->staff->nextOfKin?->phone) }}" class="{{ $ro }}" readonly />
                    </div>
                    <div>
                        <label class="{{ $lbl }}">EMAIL</label>
                        <input type="email" name="nok_email" value="{{ old('nok_email', $profile->user->staff->nextOfKin?->email) }}" class="{{ $ro }}" readonly />
                    </div>
                    <div class="col-span-2">
                        <label class="{{ $lbl }}">ADDRESS</label>
                        <textarea name="nok_address" rows="2" class="{{ $ro }} resize-none" readonly>{{ old('nok_address', $profile->user->staff->nextOfKin?->address) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="flex justify-between gap-2">
                <button type="button" @click="tab='personal'" class="btn-wf-ghost">
                    <i class="ti ti-arrow-left text-sm"></i> Back
                </button>
                <div class="flex gap-2">
                    <button type="button" @click="tab='name_change'" class="btn-wf-ghost">
                        Next <i class="ti ti-arrow-right text-sm"></i>
                    </button>
                    <button type="submit" class="btn-wf-primary hidden">
                        <i class="ti ti-device-floppy text-sm"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>

        {{-- Name Change tab --}}
        <div x-show="tab==='name_change'" class="max-w-3xl hidden">
            <div class="wf-card mb-4">
                <div class="wf-card-title"><i class="ti ti-pencil text-gray-400"></i> Name Change Record</div>
                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-xs text-yellow-700 flex items-center gap-2 mb-4">
                    <i class="ti ti-alert-triangle flex-shrink-0"></i>
                    Only complete this section if you have legally changed your name. Supporting documents will be required.
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="{{ $lbl }}">ORIGINAL NAMES</label>
                        <input type="text" name="original_names" value="{{ old('original_names', $profile->original_names) }}" class="{{ $inp }}" />
                    </div>
                    <div class="col-span-2">
                        <label class="{{ $lbl }}">CHANGED NAME</label>
                        <input type="text" name="change_name" value="{{ old('change_name', $profile->change_name) }}" class="{{ $inp }}" />
                    </div>
                    <div>
                        <label class="{{ $lbl }}">DATE OF NAME CHANGE</label>
                        <input type="date" name="date_name_change" value="{{ old('date_name_change', $profile->date_name_change) }}" class="{{ $inp }}" />
                    </div>
                    <div>
                        <label class="{{ $lbl }}">REASON FOR NAME CHANGE</label>
                        <input type="text" name="reason_name_change" value="{{ old('reason_name_change', $profile->reason_name_change) }}" class="{{ $inp }}" />
                    </div>
                </div>
            </div>
            <div class="flex justify-between gap-2">
                <button type="button" @click="tab='nok'" class="btn-wf-ghost">
                    <i class="ti ti-arrow-left text-sm"></i> Back
                </button>
                <button type="submit" class="btn-wf-primary">
                    <i class="ti ti-device-floppy text-sm"></i> Save All Changes
                </button>
            </div>
        </div>

    </form>

</x-staff-layout>
