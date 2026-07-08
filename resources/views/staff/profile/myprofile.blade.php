<x-staff-layout title="My Profile">

    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">My Profile</h1>
            <p class="pg-sub">Personal and employment information</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('staff.profile.myprofile.edit') }}" class="btn-wf-primary">
                <i class="ti ti-edit text-sm"></i> Edit Profile
            </a>
            <a href="{{ route('staff.profile.change_password') }}" class="btn-wf-ghost">
                <i class="ti ti-lock text-sm"></i> Change Password
            </a>
        </div>
    </div>

    {{-- Hero banner --}}
    <div class="rounded-xl bg-gradient-to-r from-funaab-800 to-funaab-600 p-5 mb-4 flex items-center gap-5">
        @if(Auth::user()->profile?->avatar)
            <img src="{{ asset('storage/avatars/'.Auth::user()->profile->avatar) }}"
                 class="w-16 h-16 rounded-full object-cover border-2 border-white/25 flex-shrink-0" />
        @else
            <div class="w-16 h-16 rounded-full bg-gold-500 flex items-center justify-center
                        text-funaab-900 font-bold text-2xl border-2 border-white/20 flex-shrink-0">
                {{ strtoupper(substr(Auth::user()->surname ?? 'S', 0, 1)) }}{{ strtoupper(substr(Auth::user()->firstname ?? 'S', 0, 1)) }}
            </div>
        @endif
        <div class="flex-1 text-white min-w-0">
            <div class="text-lg font-bold truncate">
                {{ $profile->title ?? '' }}
                {{ Auth::user()->surname }}, {{ Auth::user()->firstname }}
                {{ Auth::user()->middlename }}
            </div>
            <div class="text-white/70 text-xs mt-0.5">{{ $profile->designation ?? '—' }}</div>
            <div class="flex flex-wrap gap-4 mt-2">
                <span class="flex items-center gap-1.5 text-white/70 text-xs">
                    <i class="ti ti-id-badge text-sm"></i>
                    <span class="font-mono">{{ Auth::user()->fileno ?? '—' }}</span>
                </span>
                <span class="flex items-center gap-1.5 text-white/70 text-xs">
                    <i class="ti ti-chart-bar text-sm"></i>
                    GL {{ $profile->user->staff->employment->designation->salaryLevel->level ?? '—' }} / Step {{ $profile->user->staff->employment->salaryStep->step ?? '—' }}
                </span>
                <span class="flex items-center gap-1.5 text-white/70 text-xs">
                    <i class="ti ti-receipt text-sm"></i>
                    {{ $profile->user->staff->employment->salaryStructure->salary_structure_name ?? '—' }}
                </span>
            </div>
        </div>
        <div class="flex-shrink-0">
            <span class="badge badge-active">{{ $profile->user->staff->staffStatus->status ?? 'Active' }}</span>
        </div>
    </div>

    @php
        $cell = 'bg-gray-50 rounded-lg p-3';
        $ck   = 'text-[10px] text-gray-400 font-bold tracking-wider mb-1';
        $cv   = 'text-sm font-bold text-gray-800';
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Personal Info --}}
        <div class="wf-card">
            <div class="wf-card-title"><i class="ti ti-user text-gray-400"></i> Personal Information</div>
            <div class="grid grid-cols-2 gap-2">
                <div class="{{ $cell }}"><div class="{{ $ck }}">TITLE</div><div class="{{ $cv }}">{{ $profile->user->staff->title->title ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">DATE OF BIRTH</div><div class="{{ $cv }}">{{ $profile->user->staff->dob->format('d-M-Y') ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">GENDER</div><div class="{{ $cv }}">{{ $profile->user->staff->gender->gender_name ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">STATE OF ORIGIN</div><div class="{{ $cv }}">{{ $profile->user->staff->state->state_name ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">LGA OF ORIGIN</div><div class="{{ $cv }}">{{ $profile->user->staff->lga->lga_name ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">SENATORIAL DISTRICT</div><div class="{{ $cv }}">{{ $profile->user->staff->senatorialDistrict->senatorial_district_name ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">GEO-POL ZONE</div><div class="{{ $cv }}">{{ $profile->user->staff->state->geoPolZone->zone ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">NATIONALITY</div><div class="{{ $cv }}">{{ $profile->user->staff->nationality ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">BIRTH PLACE</div><div class="{{ $cv }}">{{ $profile->user->staff->birth_place ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">MARITAL STATUS</div><div class="{{ $cv }}">{{ $profile->user->staff->maritalStatus->status ?? '—' }}</div></div>
                <div class="{{ $cell }} col-span-2"><div class="{{ $ck }}">PERMANENT HOME ADDRESS</div><div class="{{ $cv }}">{{ $profile->home_address ?? '—' }}</div></div>
            </div>
        </div>

        {{-- Employment Details --}}
        <div class="wf-card">
            <div class="wf-card-title"><i class="ti ti-briefcase text-gray-400"></i> Employment Details</div>
            <div class="grid grid-cols-2 gap-2">
                <div class="{{ $cell }}"><div class="{{ $ck }}">IPPIS NO.</div><div class="{{ $cv }} font-mono text-sm">{{ $profile->user->staff->employment->ippis_no ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">DESIGNATION</div><div class="{{ $cv }}">{{ $profile->user->staff->employment->designation->cadreRank->cadre_rank_name ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">CADRE</div><div class="{{ $cv }}">{{ $profile->user->staff->employment->designation->cadreRank->cadre->cadre_name ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">SALARY STRUCTURE</div><div class="{{ $cv }}">{{ $profile->user->staff->employment->salaryStructure->salary_structure_name ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">GRADE LEVEL</div><div class="{{ $cv }}">{{ $profile->user->staff->employment->designation->salaryLevel->level ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">STEP</div><div class="{{ $cv }}">{{ $profile->user->staff->employment->salaryStep->step ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">DATE FIRST APPT (SERVICE)</div><div class="{{ $cv }}">{{ $profile->user->staff->employment->date_public_first_appointment->format('d-M-Y') ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">DATE FIRST APPT (FUNAAB)</div><div class="{{ $cv }}">{{ $profile->user->staff->employment->date_funaab_first_appointment->format('d-M-Y') ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">DATE CONFIRMATION</div><div class="{{ $cv }}">{{ $profile->user->staff->employment->date_confirmation->format('d-M-Y') ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">DATE PRESENT APPT.</div><div class="{{ $cv }}">{{ $profile->user->staff->employment->date_present_appointment->format('d-M-Y') ?? '—' }}</div></div>
            </div>
        </div>

        {{-- Next of Kin --}}
        <div class="wf-card">
            <div class="wf-card-title"><i class="ti ti-users text-gray-400"></i> Next of Kin</div>
            <div class="grid grid-cols-2 gap-2">
                <div class="{{ $cell }} col-span-2"><div class="{{ $ck }}">FULL NAMES</div><div class="{{ $cv }}">{{ $profile->user->staff->nextOfKin?->surname.' '.$profile->user->staff->nextOfKin?->firstname.' '.$profile->user->staff->nextOfKin?->othernames ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">PHONE</div><div class="{{ $cv }}">{{ $profile->user->staff->nextOfKin?->phone ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">EMAIL</div><div class="{{ $cv }}">{{ $profile->user->staff->nextOfKin?->email ?? '—' }}</div></div>
                <div class="{{ $cell }} col-span-2"><div class="{{ $ck }}">ADDRESS</div><div class="{{ $cv }}">{{ $profile->user->staff->nextOfKin?->address ?? '—' }}</div></div>
            </div>
        </div>

        {{-- Avatar update --}}
        <div class="wf-card">
            <div class="wf-card-title"><i class="ti ti-photo text-gray-400"></i> Profile Photo</div>
            <div class="flex items-center gap-4 mb-4">
                @if($profile->avatar)
                    <img src="{{ asset('storage/avatars/'.$profile->avatar) }}"
                         class="w-20 h-20 rounded-full object-cover border-2 border-funaab-100" />
                @else
                    <div class="w-20 h-20 rounded-full bg-funaab-50 border-2 border-funaab-100
                                flex items-center justify-center text-funaab-700 font-bold text-2xl">
                        {{ strtoupper(substr(Auth::user()->surname ?? 'S', 0, 1)) }}{{ strtoupper(substr(Auth::user()->firstname ?? 'S', 0, 1)) }}
                    </div>
                @endif
                <div class="text-xs text-gray-500">
                    <p class="font-bold text-gray-700 mb-1">Upload a profile photo</p>
                    <p>PNG, JPG or JPEG. Max 1MB.</p>
                </div>
            </div>
            <form action="{{ route('staff.profile.myprofile.update_avatar') }}" method="POST"
                  enctype="multipart/form-data">
                @csrf
                <div class="flex items-center gap-2">
                    <input type="file" name="photo" accept="image/*"
                           class="flex-1 text-xs text-gray-500 border border-gray-200 rounded-lg
                                  px-3 py-2 bg-gray-50 file:mr-3 file:py-1 file:px-3
                                  file:rounded-md file:border-0 file:text-xs file:font-bold
                                  file:bg-funaab-50 file:text-funaab-700 hover:file:bg-funaab-100" />
                    <button type="submit" class="btn-wf-primary btn-wf-sm flex-shrink-0">
                        <i class="ti ti-upload text-xs"></i> Upload
                    </button>
                </div>
                @error('photo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </form>
        </div>

    </div>

</x-staff-layout>
