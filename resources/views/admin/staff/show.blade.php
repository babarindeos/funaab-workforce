<x-admin-layout title="Staff Profile">

    {{-- Back + Actions --}}
    <div class="flex items-center gap-2 mb-4">
        <a href="{{ route('admin.staff.index') }}" class="text-gray-400 hover:text-funaab-700 transition-colors">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div class="flex-1">
            <h1 class="pg-title">Staff Profile</h1>
            <p class="pg-sub">{{ $staff->staff_no }}</p>
        </div>
        <a href="{{ route('admin.staff.edit', $staff) }}" class="btn-wf-ghost">
            <i class="ti ti-edit text-sm"></i> Edit
        </a>
        <form action="{{ route('admin.staff.destroy', $staff) }}" method="POST"
              onsubmit="return confirm('Delete this staff record?')">
            @csrf @method('DELETE')
            <button class="btn-wf-danger"><i class="ti ti-trash text-sm"></i></button>
        </form>
    </div>

    {{-- Hero Banner --}}
    <div class="rounded-xl bg-gradient-to-r from-funaab-800 to-funaab-600 p-5 mb-4 flex items-center gap-5">
        <div class="w-16 h-16 rounded-full bg-gold-500 flex items-center justify-center
                    text-funaab-900 font-bold text-2xl flex-shrink-0 border-2 border-white/20">
            {{ strtoupper(substr($staff->surname,0,1)) }}{{ strtoupper(substr($staff->firstname,0,1)) }}
        </div>
        <div class="text-white flex-1 min-w-0">
            <div class="text-lg font-bold truncate">
                {{ $staff->title?->title }} {{ $staff->surname }}, {{ $staff->firstname }} {{ $staff->othernames }}
            </div>
            <div class="text-white/70 text-xs mt-0.5">
                {{ $staff->employment?->staffCategory?->staff_category ?? '—' }}
                @if($staff->employment?->department)· {{ $staff->employment->department->department_name }}@endif
                @if($staff->employment?->employmentType)· {{ $staff->employment->employmentType->employment_type }}@endif
            </div>
            <div class="flex flex-wrap gap-4 mt-2.5">
                <span class="flex items-center gap-1.5 text-white/75 text-xs">
                    <i class="ti ti-id-badge text-sm"></i> {{ $staff->staff_no }}
                </span>
                @if($staff->employment?->salaryStep)
                    <span class="flex items-center gap-1.5 text-white/75 text-xs">
                        <i class="ti ti-chart-bar text-sm"></i>
                        GL {{ $staff->employment->salaryStep->salaryLevel?->level ?? '?' }}
                        / Step {{ $staff->employment->salaryStep->step ?? '?' }}
                    </span>
                @endif
                @if($staff->employment?->salaryStructure)
                    <span class="flex items-center gap-1.5 text-white/75 text-xs">
                        <i class="ti ti-receipt text-sm"></i>
                        {{ $staff->employment->salaryStructure->salary_structure }}
                    </span>
                @endif
            </div>
        </div>
        <div>
            @php $s = $staff->staffStatus?->staff_status; @endphp
            @if(str_contains(strtolower($s ?? ''), 'active'))
                <span class="badge badge-active">{{ $s }}</span>
            @else
                <span class="badge badge-inactive">{{ $s ?? '—' }}</span>
            @endif
        </div>
    </div>

    {{-- ── Section Navigation Tabs ── --}}
    <div class="wf-card mb-5 !py-0 !px-0 overflow-hidden" x-data="{ section: 'personal' }">
        {{-- Tab bar --}}
        <div class="flex overflow-x-auto border-b border-gray-100 bg-gray-50 scrollbar-thin">
            @php
                $tabs = [
                    ['key' => 'personal',       'label' => 'Personal Info',  'icon' => 'ti-user'],
                    ['key' => 'employment',      'label' => 'Employment',     'icon' => 'ti-briefcase'],
                    ['key' => 'qualifications',  'label' => 'Qualifications', 'icon' => 'ti-school'],
                    ['key' => 'nextofkin',       'label' => 'Next of Kin',    'icon' => 'ti-users'],
                    ['key' => 'contact',         'label' => 'Contact',        'icon' => 'ti-phone'],
                    ['key' => 'deployments',     'label' => 'Deployments',    'icon' => 'ti-arrows-exchange'],
                    ['key' => 'offemployments',  'label' => 'Off Employments','icon' => 'ti-user-off'],
                    ['key' => 'profile',         'label' => 'Profile',        'icon' => 'ti-id-badge'],
                ];
            @endphp
            @foreach($tabs as $tab)
                <button type="button"
                        @click="section='{{ $tab['key'] }}'"
                        :class="section==='{{ $tab['key'] }}'
                            ? 'border-b-2 border-funaab-600 text-funaab-700 bg-white font-bold'
                            : 'text-gray-400 hover:text-gray-700 hover:bg-white'"
                        class="flex items-center gap-1.5 px-4 py-3 text-xs font-bold whitespace-nowrap
                               transition-colors border-b-2 border-transparent flex-shrink-0">
                    <i class="ti {{ $tab['icon'] }} text-sm"></i>
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </div>

        @php
            $cell    = 'bg-gray-50 rounded-lg p-3';
            $cellKey = 'text-[10px] text-gray-400 font-bold tracking-wider mb-1';
            $cellVal = 'text-sm font-bold text-gray-800';
            $input   = 'w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500';
        @endphp

        {{-- ── PERSONAL INFO ── --}}
        <div x-show="section==='personal'" class="p-5">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div class="{{ $cell }}"><div class="{{ $cellKey }}">DATE OF BIRTH</div><div class="{{ $cellVal }}">{{ $staff->dob?->format('d M Y') ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $cellKey }}">GENDER</div><div class="{{ $cellVal }}">{{ $staff->gender?->gender ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $cellKey }}">MARITAL STATUS</div><div class="{{ $cellVal }}">{{ $staff->maritalStatus?->marital_status ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $cellKey }}">LGA OF ORIGIN</div><div class="{{ $cellVal }}">{{ $staff->lga?->lga_name ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $cellKey }}">STATE OF ORIGIN</div><div class="{{ $cellVal }}">{{ $staff->lga?->state?->state_name ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $cellKey }}">STAFF STATUS</div><div class="{{ $cellVal }}">{{ $staff->staffStatus?->staff_status ?? '—' }}</div></div>
            </div>
        </div>

        {{-- ── EMPLOYMENT ── --}}
        <div x-show="section==='employment'" class="p-5">
            @if($staff->employment)
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">EMPLOYMENT TYPE</div><div class="{{ $cellVal }}">{{ $staff->employment->employmentType?->employment_type ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">STAFF CATEGORY</div><div class="{{ $cellVal }}">{{ $staff->employment->staffCategory?->staff_category ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">IPPIS NO.</div><div class="{{ $cellVal }} font-mono text-sm">{{ $staff->employment->ippis_no ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">DIVISION</div><div class="{{ $cellVal }}">{{ $staff->employment->division?->division_name ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">DEPARTMENT</div><div class="{{ $cellVal }}">{{ $staff->employment->department?->department_name ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">SALARY STRUCTURE</div><div class="{{ $cellVal }}">{{ $staff->employment->salaryStructure?->salary_structure ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">GRADE LEVEL / STEP</div><div class="{{ $cellVal }}">GL {{ $staff->employment->salaryStep?->salaryLevel?->level ?? '?' }} / Step {{ $staff->employment->salaryStep?->step ?? '?' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">DATE PUBLIC FIRST APPT.</div><div class="{{ $cellVal }}">{{ $staff->employment->date_public_first_appointment?->format('d M Y') ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">DATE FUNAAB FIRST APPT.</div><div class="{{ $cellVal }}">{{ $staff->employment->date_funaab_first_appointment?->format('d M Y') ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">DATE OF CONFIRMATION</div><div class="{{ $cellVal }}">{{ $staff->employment->date_confirmation?->format('d M Y') ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">DATE PRESENT APPT.</div><div class="{{ $cellVal }}">{{ $staff->employment->date_present_appointment?->format('d M Y') ?? '—' }}</div></div>
                </div>
            @else
                <p class="text-center text-gray-400 py-8 text-sm">No employment record on file.</p>
            @endif
        </div>

        {{-- ── QUALIFICATIONS ── --}}
        <div x-show="section==='qualifications'" class="p-5">
            <div class="flex justify-end mb-3">
                <a href="#" class="btn-wf-primary btn-wf-sm">
                    <i class="ti ti-plus text-xs"></i> Add Qualification
                </a>
            </div>
            @if($staff->qualifications->count())
                <table class="wf-table">
                    <thead>
                        <tr><th>DEGREE</th><th>INSTITUTION</th><th>START</th><th>END</th><th>CLASS</th><th>REMARK</th></tr>
                    </thead>
                    <tbody>
                        @foreach($staff->qualifications as $qual)
                        <tr>
                            <td class="col-name">{{ $qual->degree?->degree_name ?? '—' }}</td>
                            <td>{{ $qual->institution }}</td>
                            <td>{{ $qual->start_date?->format('Y') ?? '—' }}</td>
                            <td>{{ $qual->end_date?->format('Y') ?? '—' }}</td>
                            <td>{{ $qual->degreeClass?->degree_class_name ?? '—' }}</td>
                            <td class="text-gray-400 text-xs">{{ $qual->remark ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-center text-gray-400 py-8 text-sm">No qualifications on record.</p>
            @endif
        </div>

        {{-- ── NEXT OF KIN ── --}}
        <div x-show="section==='nextofkin'" class="p-5">
            <div class="flex justify-end mb-3">
                <a href="#" class="btn-wf-primary btn-wf-sm">
                    <i class="ti ti-plus text-xs"></i> {{ $staff->nextOfKin ? 'Update' : 'Add' }} Next of Kin
                </a>
            </div>
            @if($staff->nextOfKin)
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">SURNAME</div><div class="{{ $cellVal }}">{{ $staff->nextOfKin->surname }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">FIRST NAME</div><div class="{{ $cellVal }}">{{ $staff->nextOfKin->firstname }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">OTHER NAMES</div><div class="{{ $cellVal }}">{{ $staff->nextOfKin->othernames ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">PHONE</div><div class="{{ $cellVal }}">{{ $staff->nextOfKin->phone ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">EMAIL</div><div class="{{ $cellVal }}">{{ $staff->nextOfKin->email ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">STATE</div><div class="{{ $cellVal }}">{{ $staff->nextOfKin->state?->state_name ?? '—' }}</div></div>
                    <div class="{{ $cell }} col-span-2"><div class="{{ $cellKey }}">ADDRESS</div><div class="{{ $cellVal }}">{{ $staff->nextOfKin->address ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">CITY</div><div class="{{ $cellVal }}">{{ $staff->nextOfKin->city ?? '—' }}</div></div>
                </div>
            @else
                <p class="text-center text-gray-400 py-8 text-sm">No next of kin on record.</p>
            @endif
        </div>

        {{-- ── CONTACT ── --}}
        <div x-show="section==='contact'" class="p-5">
            <div class="flex justify-end mb-3">
                <a href="#" class="btn-wf-primary btn-wf-sm">
                    <i class="ti ti-plus text-xs"></i> Add Contact
                </a>
            </div>
            @if($staff->contacts->count())
                <table class="wf-table">
                    <thead>
                        <tr><th>CONTACT TYPE</th><th>VALUE</th><th>ADDED</th></tr>
                    </thead>
                    <tbody>
                        @foreach($staff->contacts as $contact)
                        <tr>
                            <td class="col-name">{{ $contact->contactType?->contact_type ?? '—' }}</td>
                            <td>{{ $contact->contact_value }}</td>
                            <td class="text-gray-400 text-xs">{{ $contact->created_at->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-center text-gray-400 py-8 text-sm">No contact information on record.</p>
            @endif
        </div>

        {{-- ── DEPLOYMENTS ── --}}
        <div x-show="section==='deployments'" class="p-5">
            <div class="flex justify-end mb-3">
                <a href="#" class="btn-wf-primary btn-wf-sm">
                    <i class="ti ti-plus text-xs"></i> Add Deployment
                </a>
            </div>
            @if($staff->deployments->count())
                <table class="wf-table">
                    <thead>
                        <tr><th>DEPARTMENT</th><th>POST</th><th>START DATE</th><th>EXPECTED END</th><th>ACTUAL END</th><th>REMARK</th></tr>
                    </thead>
                    <tbody>
                        @foreach($staff->deployments as $dep)
                        <tr>
                            <td class="col-name">{{ $dep->department?->department_name ?? '—' }}</td>
                            <td>{{ $dep->deploymentPost?->deployment_post ?? '—' }}</td>
                            <td>{{ $dep->start_date?->format('d M Y') ?? '—' }}</td>
                            <td>{{ $dep->expected_end_date?->format('d M Y') ?? '—' }}</td>
                            <td>{{ $dep->end_date?->format('d M Y') ?? 'Ongoing' }}</td>
                            <td class="text-gray-400 text-xs">{{ $dep->remark ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-center text-gray-400 py-8 text-sm">No deployment records.</p>
            @endif
        </div>

        {{-- ── OFF EMPLOYMENTS ── --}}
        <div x-show="section==='offemployments'" class="p-5">
            <div class="flex justify-end mb-3">
                <a href="#" class="btn-wf-danger btn-wf-sm">
                    <i class="ti ti-plus text-xs"></i> Record Off Employment
                </a>
            </div>
            @if($staff->offEmployments->count())
                <table class="wf-table">
                    <thead>
                        <tr><th>STATUS</th><th>EFFECTIVE DATE</th><th>REASON</th><th>DOC REFERENCE</th><th>RECORDED</th></tr>
                    </thead>
                    <tbody>
                        @foreach($staff->offEmployments as $off)
                        <tr>
                            <td>
                                <span class="badge badge-inactive">{{ $off->staffStatus?->staff_status ?? '—' }}</span>
                            </td>
                            <td>{{ $off->effective_date?->format('d M Y') ?? '—' }}</td>
                            <td>{{ $off->reason }}</td>
                            <td class="col-mono">{{ $off->document_reference ?? '—' }}</td>
                            <td class="text-gray-400 text-xs">{{ $off->created_at->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-center text-gray-400 py-8 text-sm">No off-employment records.</p>
            @endif
        </div>

        {{-- ── PROFILE ── --}}
        <div x-show="section==='profile'" class="p-5">
            <div class="flex justify-end mb-3">
                <a href="#" class="btn-wf-primary btn-wf-sm">
                    <i class="ti ti-edit text-xs"></i> Edit Profile
                </a>
            </div>
            @if($staff->profile)
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">DESIGNATION (PORTAL)</div><div class="{{ $cellVal }}">{{ $staff->profile->designation ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">PHONE</div><div class="{{ $cellVal }}">{{ $staff->profile->phone ?? '—' }}</div></div>
                    @if($staff->profile->avatar)
                        <div class="{{ $cell }}">
                            <div class="{{ $cellKey }}">AVATAR</div>
                            <img src="{{ asset('storage/'.$staff->profile->avatar) }}"
                                 class="w-12 h-12 rounded-full object-cover mt-1" alt="Avatar" />
                        </div>
                    @endif
                    @if($staff->profile->bio)
                        <div class="{{ $cell }} col-span-3">
                            <div class="{{ $cellKey }}">BIO</div>
                            <div class="{{ $cellVal }} font-normal text-xs leading-relaxed">{{ $staff->profile->bio }}</div>
                        </div>
                    @endif
                </div>
            @else
                <p class="text-center text-gray-400 py-8 text-sm">No portal profile created yet.</p>
            @endif
        </div>

    </div>{{-- end wf-card tabs --}}

    {{-- Leave History card below --}}
    <div class="wf-card">
        <div class="flex items-center justify-between mb-3">
            <div class="wf-card-title mb-0"><i class="ti ti-calendar-off text-gray-400"></i> Leave History</div>
            <a href="{{ route('admin.leaves.index', ['search' => $staff->staff_no]) }}"
               class="text-xs text-funaab-700 font-bold hover:underline">View all →</a>
        </div>
        @if($staff->leaves->count())
            <table class="wf-table">
                <thead>
                    <tr><th>TYPE</th><th>FROM</th><th>TO</th><th>DAYS</th><th>STATUS</th></tr>
                </thead>
                <tbody>
                    @foreach($staff->leaves->take(5) as $leave)
                    <tr>
                        <td>{{ $leave->leaveType?->leave_type ?? '—' }}</td>
                        <td>{{ $leave->start_date?->format('d M Y') }}</td>
                        <td>{{ $leave->end_date?->format('d M Y') }}</td>
                        <td class="font-bold">{{ $leave->total_days }}</td>
                        <td>
                            @if($leave->approval_status === 'approved') <span class="badge badge-approved">Approved</span>
                            @elseif($leave->approval_status === 'rejected') <span class="badge badge-rejected">Rejected</span>
                            @else <span class="badge badge-pending">Pending</span> @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-xs text-gray-400 py-4 text-center">No leave records found.</p>
        @endif
    </div>

</x-admin-layout>