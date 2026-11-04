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
        <a href="{{ route('admin.staff_documents.staff', $staff) }}" class="btn-wf-ghost">
            <i class="ti ti-folders text-sm"></i> Documents
        </a>
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
                {{ $staff->employment?->staffCategory?->category ?? '—' }}
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
            @php $s = $staff->staffStatus?->status; @endphp
            @if(str_contains(strtolower($s ?? ''), 'active'))
                <span class="badge badge-active">{{ $s }}</span>
            @else
                <span class="badge badge-inactive">{{ $s ?? '—' }}</span>
            @endif
        </div>
    </div>

    {{-- ── Section Navigation Tabs ── --}}
    <div class="wf-card mb-5 !py-0 !px-0 overflow-hidden" id="staff-tabs">
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
                <button type="button" data-tab="{{ $tab['key'] }}"
                        class="staff-tab text-gray-400 hover:text-gray-700 hover:bg-white flex items-center gap-1.5 px-4 py-3 text-xs font-bold whitespace-nowrap
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
        <div data-panel="personal" class="staff-panel p-5" style="display:none">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div class="{{ $cell }}"><div class="{{ $cellKey }}">DATE OF BIRTH</div><div class="{{ $cellVal }}">{{ $staff->dob?->format('d M Y') ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $cellKey }}">GENDER</div><div class="{{ $cellVal }}">{{ $staff->gender?->gender_name ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $cellKey }}">MARITAL STATUS</div><div class="{{ $cellVal }}">{{ $staff->maritalStatus?->status ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $cellKey }}">LGA OF ORIGIN</div><div class="{{ $cellVal }}">{{ $staff->lga?->lga_name ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $cellKey }}">STATE OF ORIGIN</div><div class="{{ $cellVal }}">{{ ($staff->state?->state_name ?? $staff->lga?->state?->state_name) ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $cellKey }}">STAFF STATUS</div><div class="{{ $cellVal }}">{{ $staff->staffStatus?->status ?? '—' }}</div></div>
            </div>
        </div>

        {{-- ── EMPLOYMENT ── --}}
        <div data-panel="employment" class="staff-panel p-5" style="display:none">
            @if($staff->employment)
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">EMPLOYMENT TYPE</div><div class="{{ $cellVal }}">{{ $staff->employment->employmentType?->employment_type ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">STAFF CATEGORY</div><div class="{{ $cellVal }}">{{ $staff->employment->staffCategory?->category ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">DESIGNATION</div><div class="{{ $cellVal }}">{{ $staff->employment->designation?->cadreRank?->cadre_rank_name ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">IPPIS NO.</div><div class="{{ $cellVal }} font-mono text-sm">{{ $staff->employment->ippis_no ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">DIVISION</div><div class="{{ $cellVal }}">{{ $staff->employment->division?->division_name ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">DEPARTMENT</div><div class="{{ $cellVal }}">{{ $staff->employment->department?->department_name ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">SALARY STRUCTURE</div><div class="{{ $cellVal }}">{{ ($staff->employment->salaryStructure?->salary_structure_name ?? $staff->employment->salaryStep?->salaryLevel?->salaryStructure?->salary_structure_name) ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">GRADE LEVEL / STEP</div><div class="{{ $cellVal }}">{{ $staff->employment->salaryStep ? 'GL ' . $staff->employment->salaryStep->salaryLevel?->level . ' / Step ' . $staff->employment->salaryStep->step : '—' }}</div></div>
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
        <div data-panel="qualifications" class="staff-panel p-5" style="display:none">
            @php $qErr = $errors->getBag('qualification'); @endphp
            <div class="flex justify-end mb-3">
                <button type="button" class="btn-wf-primary btn-wf-sm" onclick="openQualForm()">
                    <i class="ti ti-plus text-xs"></i> Add Qualification
                </button>
            </div>

            {{-- Add / edit form --}}
            <form id="qual-form" method="POST" action="{{ $qErr->any() ? old('_form_action', route('admin.staff.qualifications.store', $staff)) : route('admin.staff.qualifications.store', $staff) }}"
                  data-store="{{ route('admin.staff.qualifications.store', $staff) }}"
                  class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4 {{ $qErr->any() ? '' : 'hidden' }}">
                @csrf
                <input type="hidden" name="_method" id="qual-method" value="{{ $qErr->any() ? old('_method', 'POST') : 'POST' }}">
                <input type="hidden" name="_form_action" id="qual-form-action" value="{{ $qErr->any() ? old('_form_action', route('admin.staff.qualifications.store', $staff)) : route('admin.staff.qualifications.store', $staff) }}">
                <p class="text-sm font-bold text-gray-700 mb-3" id="qual-form-title">{{ old('_method') === 'PUT' ? 'Edit Qualification' : 'Add Qualification' }}</p>
                @if($qErr->any())
                    <div class="mb-3 text-xs text-red-600">{{ $qErr->first() }}</div>
                @endif
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="wf-label">Degree / certificate <span class="text-red-500">*</span></label>
                        <select name="degree_id" id="q-degree" class="{{ $input }}" required>
                            <option value="">— Select —</option>
                            @foreach($degrees as $d)
                                <option value="{{ $d->id }}" {{ old('degree_id') == $d->id ? 'selected' : '' }}>{{ $d->degree_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="wf-label">Institution <span class="text-red-500">*</span></label>
                        <input type="text" name="institution" id="q-institution" value="{{ old('institution') }}" class="{{ $input }}" required placeholder="e.g. University of Ibadan">
                    </div>
                    <div>
                        <label class="wf-label">Start date</label>
                        <input type="date" name="start_date" id="q-start" value="{{ old('start_date') }}" class="{{ $input }}">
                    </div>
                    <div>
                        <label class="wf-label">End date</label>
                        <input type="date" name="end_date" id="q-end" value="{{ old('end_date') }}" class="{{ $input }}">
                    </div>
                    <div>
                        <label class="wf-label">Class of degree</label>
                        <select name="degree_class_id" id="q-class" class="{{ $input }}">
                            <option value="">— None / not applicable —</option>
                            @foreach($degreeClasses as $c)
                                <option value="{{ $c->id }}" {{ old('degree_class_id') == $c->id ? 'selected' : '' }}>{{ $c->degree_class_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <label class="wf-label">Remark</label>
                        <input type="text" name="remark" id="q-remark" value="{{ old('remark') }}" class="{{ $input }}">
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-3">
                    <button type="button" class="btn-wf-ghost btn-wf-sm" onclick="document.getElementById('qual-form').classList.add('hidden')">Cancel</button>
                    <button type="submit" class="btn-wf-primary btn-wf-sm"><i class="ti ti-device-floppy text-xs"></i> Save</button>
                </div>
            </form>

            @if($staff->qualifications->count())
                <table class="wf-table">
                    <thead>
                        <tr><th>DEGREE</th><th>INSTITUTION</th><th>START</th><th>END</th><th>CLASS</th><th>REMARK</th><th class="text-right">ACTIONS</th></tr>
                    </thead>
                    <tbody>
                        @foreach($staff->qualifications->sortByDesc(fn ($q) => $q->end_date ?? $q->start_date ?? $q->created_at) as $qual)
                        <tr>
                            <td class="col-name">{{ $qual->degree?->degree_name ?? '—' }}</td>
                            <td>{{ $qual->institution }}</td>
                            <td>{{ $qual->start_date?->format('M Y') ?? '—' }}</td>
                            <td>{{ $qual->end_date?->format('M Y') ?? '—' }}</td>
                            <td>{{ $qual->degreeClass?->degree_class_name ?? '—' }}</td>
                            <td class="text-gray-400 text-xs">{{ $qual->remark ?? '—' }}</td>
                            <td>
                                <div class="flex justify-end gap-1">
                                    <button type="button" class="btn-wf-ghost btn-wf-sm" onclick="openQualForm(this.dataset)"
                                            data-action="{{ route('admin.staff.qualifications.update', [$staff, $qual]) }}"
                                            data-degree_id="{{ $qual->degree_id }}" data-institution="{{ $qual->institution }}"
                                            data-start_date="{{ $qual->start_date?->format('Y-m-d') }}" data-end_date="{{ $qual->end_date?->format('Y-m-d') }}"
                                            data-degree_class_id="{{ $qual->degree_class_id }}" data-remark="{{ $qual->remark }}">
                                        <i class="ti ti-edit text-xs"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.staff.qualifications.destroy', [$staff, $qual]) }}" method="POST"
                                          onsubmit="return confirm('Delete this qualification?')">
                                        @csrf @method('DELETE')
                                        <button class="btn-wf-danger btn-wf-sm"><i class="ti ti-trash text-xs"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-center text-gray-400 py-8 text-sm">No qualifications on record.</p>
            @endif
        </div>

        {{-- ── NEXT OF KIN ── --}}
        <div data-panel="nextofkin" class="staff-panel p-5" style="display:none">
            @include('admin.staff.tabs._kin')
        </div>

        {{-- ── CONTACT ── --}}
        <div data-panel="contact" class="staff-panel p-5" style="display:none">
            @include('admin.staff.tabs._contact')
        </div>

        {{-- ── DEPLOYMENTS ── --}}
        <div data-panel="deployments" class="staff-panel p-5" style="display:none">
            @include('admin.staff.tabs._deployments')
        </div>

        {{-- ── OFF EMPLOYMENTS ── --}}
        <div data-panel="offemployments" class="staff-panel p-5" style="display:none">
            @include('admin.staff.tabs._off')
        </div>

        {{-- ── PROFILE ── --}}
        <div data-panel="profile" class="staff-panel p-5" style="display:none">
            @php $portalProfile = $staff->user?->profile; @endphp
            @if($portalProfile)
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">DESIGNATION (PORTAL)</div><div class="{{ $cellVal }}">{{ $portalProfile->designation ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">PHONE</div><div class="{{ $cellVal }}">{{ $portalProfile->phone ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">PORTAL E-MAIL</div><div class="{{ $cellVal }} break-all">{{ $staff->user?->email ?? '—' }}</div></div>
                    <div class="{{ $cell }}"><div class="{{ $cellKey }}">HOME ADDRESS</div><div class="{{ $cellVal }}">{{ $portalProfile->home_address ?? '—' }}</div></div>
                    @if($portalProfile->avatar)
                        <div class="{{ $cell }}">
                            <div class="{{ $cellKey }}">AVATAR</div>
                            <img src="{{ asset('storage/avatars/'.$portalProfile->avatar) }}"
                                 class="w-12 h-12 rounded-full object-cover mt-1" alt="Avatar" />
                        </div>
                    @endif
                    @if($portalProfile->bio)
                        <div class="{{ $cell }} col-span-3">
                            <div class="{{ $cellKey }}">BIO</div>
                            <div class="{{ $cellVal }} font-normal text-xs leading-relaxed">{{ $portalProfile->bio }}</div>
                        </div>
                    @endif
                </div>
            @else
                <p class="text-center text-gray-400 py-8 text-sm">This staff member has not created a portal profile yet.</p>
            @endif
        </div>

    </div>{{-- end wf-card tabs --}}


    <script>
        // Tabs work with plain JavaScript (no dependency on Vite/Alpine).
        (function () {
            const tabs = document.querySelectorAll('#staff-tabs .staff-tab');
            const panels = document.querySelectorAll('#staff-tabs .staff-panel');
            const active = ['border-funaab-600', 'text-funaab-700', 'bg-white'];
            function show(key) {
                if (![...panels].some(p => p.dataset.panel === key)) key = 'personal';
                panels.forEach(p => p.style.display = p.dataset.panel === key ? '' : 'none');
                tabs.forEach(t => {
                    const on = t.dataset.tab === key;
                    t.classList.toggle('text-gray-400', !on);
                    active.forEach(c => t.classList.toggle(c, on));
                });
                history.replaceState(null, '', '#' + key);
            }
            tabs.forEach(t => t.addEventListener('click', () => show(t.dataset.tab)));
            window.addEventListener('hashchange', () => show(location.hash.slice(1)));
            @php
                $errorTab = collect(['qualification' => 'qualifications', 'kin' => 'nextofkin', 'contact' => 'contact',
                                     'deployment' => 'deployments', 'offemp' => 'offemployments'])
                    ->first(fn ($tab, $bag) => $errors->getBag($bag)->any());
            @endphp
            show(@json($errorTab) || location.hash.slice(1));
        })();

        // Add / edit forms on the Next of Kin, Contact, Deployments and Off-Employment tabs.
        // Edit buttons carry data-action plus one data-<field> per form field.
        function openRecordForm(formId, btn) {
            const f = document.getElementById(formId);
            const editing = !!(btn && btn.dataset.action);
            f.reset();
            f.querySelectorAll('input:not([type=hidden]), select, textarea').forEach(el => {
                el.value = editing ? (btn.dataset[el.name] ?? '') : (el.dataset.default ?? '');
            });
            const url = editing ? btn.dataset.action : f.dataset.store;
            f.action = url;
            f.querySelector('input[name=_method]').value = editing ? 'PUT' : 'POST';
            f.querySelector('input[name=_form_action]').value = url;
            f.querySelector('.record-form-title').textContent = editing ? f.dataset.titleEdit : f.dataset.titleAdd;
            f.querySelectorAll('.add-only').forEach(el => el.style.display = editing ? 'none' : '');
            f.classList.remove('hidden');
            f.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function openQualForm(q) {
            const f = document.getElementById('qual-form');
            const set = (id, v) => document.getElementById(id).value = v ?? '';
            if (q) {
                f.action = q.action; set('qual-method', 'PUT'); set('qual-form-action', q.action);
                document.getElementById('qual-form-title').textContent = 'Edit Qualification';
                set('q-degree', q.degree_id); set('q-institution', q.institution); set('q-start', q.start_date);
                set('q-end', q.end_date); set('q-class', q.degree_class_id); set('q-remark', q.remark);
            } else {
                f.action = f.dataset.store; set('qual-method', 'POST'); set('qual-form-action', f.dataset.store);
                document.getElementById('qual-form-title').textContent = 'Add Qualification';
                ['q-degree','q-institution','q-start','q-end','q-class','q-remark'].forEach(id => set(id, ''));
            }
            f.classList.remove('hidden');
            f.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    </script>

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