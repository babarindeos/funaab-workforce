<x-staff-layout title="Staff Submission — Senior Non-Teaching">

    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">Staff Submission</h1>
            <p class="pg-sub">{{ $aperForm->name }} — {{ $staff->surname }}, {{ $staff->firstname }} ({{ $staff->staff_no }})</p>
        </div>
        <a href="{{ $backRoute }}" class="btn-wf-ghost btn-wf-sm">
            <i class="ti ti-arrow-left text-sm"></i> Back to Staff List
        </a>
    </div>

    {{-- Tab Navigation --}}
    <div class="flex gap-1 mb-4 bg-gray-100 rounded-lg p-1 w-fit">
        <button onclick="showTab('personal')" id="tab-personal"
                class="px-4 py-1.5 text-xs font-bold rounded-md bg-white text-funaab-700 shadow-sm">
            <i class="ti ti-user text-xs"></i> Personal
        </button>
        <button onclick="showTab('education')" id="tab-education"
                class="px-4 py-1.5 text-xs font-semibold text-gray-500 rounded-md hover:bg-white/60">
            <i class="ti ti-school text-xs"></i> Education
        </button>
        <button onclick="showTab('university')" id="tab-university"
                class="px-4 py-1.5 text-xs font-semibold text-gray-500 rounded-md hover:bg-white/60">
            <i class="ti ti-building-bank text-xs"></i> University Experience
        </button>
    </div>

    {{-- ═══ PERSONAL TAB ═══ --}}
    <div id="section-personal">
        @if($personal)
            <div class="wf-card mb-4">
                <div class="wf-card-title"><i class="ti ti-user text-gray-400"></i> Personal Details</div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">SURNAME</p><p class="text-sm font-semibold text-gray-700">{{ $personal->surname }}</p></div>
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">FIRST NAME</p><p class="text-sm font-semibold text-gray-700">{{ $personal->firstname }}</p></div>
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">MIDDLE NAME</p><p class="text-sm font-semibold text-gray-700">{{ $personal->middlename ?? '—' }}</p></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">DATE OF BIRTH</p><p class="text-sm font-semibold text-gray-700">{{ $personal->dob?->format('d M Y') ?? '—' }}</p></div>
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">DATE OF MANDATORY RETIREMENT</p><p class="text-sm font-semibold text-gray-700">{{ $personal->retirement_date?->format('d M Y') ?? '—' }}</p></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">MARITAL STATUS</p><p class="text-sm font-semibold text-gray-700">{{ $personal->maritalStatus?->status ?? '—' }}</p></div>
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">NO. & AGES OF CHILDREN</p><p class="text-sm font-semibold text-gray-700">{{ $personal->children ?? '—' }}</p></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">TELEPHONE</p><p class="text-sm font-semibold text-gray-700">{{ $personal->telephone_no ?? '—' }}</p></div>
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">EMAIL ADDRESS</p><p class="text-sm font-semibold text-gray-700">{{ $personal->email_address ?? '—' }}</p></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">COLLEGE / DIVISION</p><p class="text-sm font-semibold text-gray-700">{{ $personal->division?->division_name ?? '—' }}</p></div>
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">DEPARTMENT</p><p class="text-sm font-semibold text-gray-700">{{ $personal->department?->department_name ?? '—' }}</p></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">FIRST APPOINTMENT DATE</p><p class="text-sm font-semibold text-gray-700">{{ $personal->date_first_appointment?->format('d M Y') ?? '—' }}</p></div>
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">FIRST APPOINTMENT POST</p><p class="text-sm font-semibold text-gray-700">{{ $personal->post_first_appointment ?? '—' }}</p></div>
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">APPOINTMENT CONFIRMED</p><p class="text-sm font-semibold text-gray-700">{{ $personal->appointment_confirmed ? 'Yes — '.$personal->confirmation_date?->format('d M Y') : 'No' }}</p></div>
                </div>
                <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">PRESENT GRADE LEVEL & SALARY</p><p class="text-sm font-semibold text-gray-700">{{ $personal->present_grade_level_salary ?? '—' }}</p></div>
            </div>

            @if($conferences->isNotEmpty())
                <div class="wf-card mb-4">
                    <div class="wf-card-title"><i class="ti ti-presentation text-gray-400"></i> 11. Courses / Conferences / Workshops</div>
                    <div class="space-y-2">
                        @foreach($conferences as $i => $conf)
                            <div class="flex items-start gap-3">
                                <span class="text-[11px] font-bold text-gray-400 flex-shrink-0 mt-0.5">{{ $i + 1 }}.</span>
                                <p class="text-sm text-gray-700">{{ $conf->conference }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <div class="wf-card flex flex-col items-center justify-center py-10 text-center text-gray-400">
                <i class="ti ti-file-off text-3xl mb-2"></i>
                <p class="text-sm font-semibold">Part A (Personal) not submitted yet.</p>
            </div>
        @endif
    </div>

    {{-- ═══ EDUCATION TAB ═══ --}}
    <div id="section-education" class="hidden">

        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-certificate text-gray-400"></i> 12(a). Academic Qualifications</div>
            @if($qualifications->isNotEmpty())
                <table class="wf-table">
                    <thead><tr><th>DEGREE / DIPLOMA</th><th>CLASS</th><th>INSTITUTION</th><th>DATE OF AWARD</th></tr></thead>
                    <tbody>
                        @foreach($qualifications as $q)
                            <tr><td>{{ $q->degree ?? '—' }}</td><td>{{ $q->class ?? '—' }}</td><td>{{ $q->institution ?? '—' }}</td><td>{{ $q->date_obtained ?? '—' }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-sm text-gray-400 italic">No academic qualifications recorded.</p>
            @endif
        </div>

        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-rosette text-gray-400"></i> 12(b). Professional Qualifications</div>
            @if($professionals->isNotEmpty())
                <table class="wf-table">
                    <thead><tr><th>QUALIFICATION</th><th>AWARDING BODY</th><th>DATE OF AWARD</th></tr></thead>
                    <tbody>
                        @foreach($professionals as $p)
                            <tr><td>{{ $p->qualification ?? '—' }}</td><td>{{ $p->award_body ?? '—' }}</td><td>{{ $p->date_obtained ?? '—' }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-sm text-gray-400 italic">No professional qualifications recorded.</p>
            @endif
        </div>

        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-users-group text-gray-400"></i> 12(c). Membership of Professional Associations</div>
            @if($associations->isNotEmpty())
                <div class="space-y-2">
                    @foreach($associations as $i => $assoc)
                        <div class="flex items-start gap-3">
                            <span class="text-[11px] font-bold text-gray-400 flex-shrink-0 mt-0.5">{{ $i + 1 }}.</span>
                            <p class="text-sm text-gray-700">{{ $assoc->membership }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 italic">No associations recorded.</p>
            @endif
        </div>

    </div>

    {{-- ═══ UNIVERSITY EXPERIENCE TAB ═══ --}}
    <div id="section-university" class="hidden">

        @php
            $uniSections = [
                ['title'=>'13a(i). Positions Held & Duties','icon'=>'ti-briefcase','items'=>$positions,'type'=>'positions'],
                ['title'=>'13a(ii)(a). Present Job Duties','icon'=>'ti-list-check','items'=>$presentJobs,'field'=>'official_duty','type'=>'simple'],
                ['title'=>'13(b). Practicals','icon'=>'ti-tools','items'=>$practicals,'field'=>'practical','type'=>'simple'],
                ['title'=>'14. Recognized Publications','icon'=>'ti-book','items'=>$publications,'field'=>'publication','type'=>'simple'],
                ['title'=>'15a. Other Activities','icon'=>'ti-activity','items'=>$activities,'field'=>'activity','type'=>'simple'],
                ['title'=>'Community Service','icon'=>'ti-heart-handshake','items'=>$communityServices,'field'=>'service','type'=>'simple'],
                ['title'=>'16. Challenges','icon'=>'ti-alert-triangle','items'=>$challenges,'field'=>'challenge','type'=>'simple'],
            ];
        @endphp

        @foreach($uniSections as $section)
            <div class="wf-card mb-4">
                <div class="wf-card-title"><i class="ti {{ $section['icon'] }} text-gray-400"></i> {{ $section['title'] }}</div>
                @if($section['items']->isNotEmpty())
                    @if($section['type'] === 'positions')
                        <div class="space-y-3">
                            @foreach($section['items'] as $i => $pos)
                                <div class="border border-gray-100 rounded-lg p-3 bg-gray-50/50">
                                    <p class="text-[10.5px] font-bold text-gray-400 tracking-wider mb-2">ENTRY {{ $i + 1 }}</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">POSITION HELD</p><p class="text-sm text-gray-700">{{ $pos->position_held }}</p></div>
                                        <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">DUTIES</p><p class="text-sm text-gray-700">{{ $pos->duty }}</p></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach($section['items'] as $i => $item)
                                <div class="flex items-start gap-3">
                                    <span class="text-[11px] font-bold text-gray-400 flex-shrink-0 mt-0.5">{{ $i + 1 }}.</span>
                                    <p class="text-sm text-gray-700">{{ $item->{$section['field']} }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <p class="text-sm text-gray-400 italic">No records submitted.</p>
                @endif
            </div>
        @endforeach

    </div>

    <script>
        function showTab(tab) {
            ['personal','education','university'].forEach(t => {
                document.getElementById('section-' + t).classList.add('hidden');
                document.getElementById('tab-' + t).className = 'px-4 py-1.5 text-xs font-semibold text-gray-500 rounded-md hover:bg-white/60';
            });
            document.getElementById('section-' + tab).classList.remove('hidden');
            document.getElementById('tab-' + tab).className = 'px-4 py-1.5 text-xs font-bold rounded-md bg-white text-funaab-700 shadow-sm';
        }
    </script>

</x-staff-layout>