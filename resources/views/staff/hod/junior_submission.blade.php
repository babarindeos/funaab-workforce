<x-staff-layout title="Staff Submission — Junior Non-Teaching">

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
        <button onclick="showTab('part-a')" id="tab-part-a"
                class="px-4 py-1.5 text-xs font-bold rounded-md bg-white text-funaab-700 shadow-sm">
            <i class="ti ti-user text-xs"></i> Part A
        </button>
        <button onclick="showTab('part-b')" id="tab-part-b"
                class="px-4 py-1.5 text-xs font-semibold text-gray-500 rounded-md hover:bg-white/60">
            <i class="ti ti-list-check text-xs"></i> Part B
        </button>
    </div>

    {{-- ═══ PART A ═══ --}}
    <div id="section-part-a">
        @if($personal)
            <div class="wf-card mb-4">
                <div class="wf-card-title"><i class="ti ti-user text-gray-400"></i> Personal Details</div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">SURNAME</p><p class="text-sm font-semibold text-gray-700">{{ $personal->surname }}</p></div>
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">FIRST NAME</p><p class="text-sm font-semibold text-gray-700">{{ $personal->firstname }}</p></div>
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">MIDDLE NAME</p><p class="text-sm font-semibold text-gray-700">{{ $personal->middlename ?? '—' }}</p></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">DATE OF BIRTH</p><p class="text-sm font-semibold text-gray-700">{{ $personal->dob?->format('d M Y') ?? '—' }}</p></div>
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">DATE OF MANDATORY RETIREMENT</p><p class="text-sm font-semibold text-gray-700">{{ $personal->retirement_date?->format('d M Y') ?? '—' }}</p></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">MARITAL STATUS</p><p class="text-sm font-semibold text-gray-700">{{ $personal->maritalStatus?->status ?? '—' }}</p></div>
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">NO. & AGES OF CHILDREN</p><p class="text-sm font-semibold text-gray-700">{{ $personal->children ?? '—' }}</p></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">TELEPHONE</p><p class="text-sm font-semibold text-gray-700">{{ $personal->telephone_no ?? '—' }}</p></div>
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">EMAIL ADDRESS</p><p class="text-sm font-semibold text-gray-700">{{ $personal->email_address ?? '—' }}</p></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">COLLEGE / DIVISION</p><p class="text-sm font-semibold text-gray-700">{{ $personal->division?->division_name ?? '—' }}</p></div>
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">DEPARTMENT</p><p class="text-sm font-semibold text-gray-700">{{ $personal->department?->department_name ?? '—' }}</p></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">FIRST APPOINTMENT DATE</p><p class="text-sm font-semibold text-gray-700">{{ $personal->date_first_appointment?->format('d M Y') ?? '—' }}</p></div>
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">FIRST APPOINTMENT POST</p><p class="text-sm font-semibold text-gray-700">{{ $personal->post_first_appointment ?? '—' }}</p></div>
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">APPOINTMENT CONFIRMED</p><p class="text-sm font-semibold text-gray-700">{{ $personal->appointment_confirmed ? 'Yes — '.$personal->confirmation_date?->format('d M Y') : 'No' }}</p></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">LAST PROMOTION DATE</p><p class="text-sm font-semibold text-gray-700">{{ $personal->date_last_appointment?->format('d M Y') ?? '—' }}</p></div>
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">LAST PROMOTION POST</p><p class="text-sm font-semibold text-gray-700">{{ $personal->post_last_appointment ?? '—' }}</p></div>
                    <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">PRESENT GRADE LEVEL & SALARY</p><p class="text-sm font-semibold text-gray-700">{{ $personal->present_grade_level_salary ?? '—' }}</p></div>
                </div>
            </div>

            @if($qualifications->isNotEmpty())
                <div class="wf-card mb-4">
                    <div class="wf-card-title"><i class="ti ti-school text-gray-400"></i> 11. Qualifications</div>
                    <table class="wf-table">
                        <thead><tr><th>INSTITUTION</th><th>FROM</th><th>TO</th><th>QUALIFICATION OBTAINED</th></tr></thead>
                        <tbody>
                            @foreach($qualifications as $q)
                                <tr><td>{{ $q->institution ?? '—' }}</td><td>{{ $q->from_date ?? '—' }}</td><td>{{ $q->to_date ?? '—' }}</td><td>{{ $q->qualification_obtained ?? '—' }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @else
            <div class="wf-card flex flex-col items-center justify-center py-10 text-center text-gray-400">
                <i class="ti ti-file-off text-3xl mb-2"></i>
                <p class="text-sm font-semibold">Part A not submitted yet.</p>
            </div>
        @endif
    </div>

    {{-- ═══ PART B ═══ --}}
    <div id="section-part-b" class="hidden">

        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-list-check text-gray-400"></i> 12. Main Duties</div>
            @if($duties->isNotEmpty())
                <div class="space-y-2">
                    @foreach($duties as $i => $duty)
                        <div class="flex items-start gap-3">
                            <span class="text-[11px] font-bold text-gray-400 flex-shrink-0 mt-0.5">{{ $i + 1 }}.</span>
                            <p class="text-sm text-gray-700">{{ $duty->duty }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 italic">No duties recorded.</p>
            @endif
        </div>

        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-alert-triangle text-gray-400"></i> 13. Difficulties & Suggested Solutions</div>
            @if($difficulties->isNotEmpty())
                <div class="space-y-3">
                    @foreach($difficulties as $i => $diff)
                        <div class="border border-gray-100 rounded-lg p-3 bg-gray-50/50">
                            <p class="text-[10.5px] font-bold text-gray-400 tracking-wider mb-2">ENTRY {{ $i + 1 }}</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">DIFFICULTY</p><p class="text-sm text-gray-700">{{ $diff->difficulty }}</p></div>
                                <div><p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">SUGGESTED SOLUTION</p><p class="text-sm text-gray-700">{{ $diff->solution }}</p></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 italic">No difficulties recorded.</p>
            @endif
        </div>

        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-info-circle text-gray-400"></i> 14. Other Useful Information</div>
            @if($peculiarDuties->isNotEmpty())
                <div class="space-y-2">
                    @foreach($peculiarDuties as $i => $pd)
                        <div class="flex items-start gap-3">
                            <span class="text-[11px] font-bold text-gray-400 flex-shrink-0 mt-0.5">{{ $i + 1 }}.</span>
                            <p class="text-sm text-gray-700">{{ $pd->peculiar_duty }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 italic">No other information recorded.</p>
            @endif
        </div>

    </div>

    <script>
        function showTab(tab) {
            ['part-a','part-b'].forEach(t => {
                document.getElementById('section-' + t).classList.add('hidden');
                document.getElementById('tab-' + t).className = 'px-4 py-1.5 text-xs font-semibold text-gray-500 rounded-md hover:bg-white/60';
            });
            document.getElementById('section-' + tab).classList.remove('hidden');
            document.getElementById('tab-' + tab).className = 'px-4 py-1.5 text-xs font-bold rounded-md bg-white text-funaab-700 shadow-sm';
        }
    </script>

</x-staff-layout>
