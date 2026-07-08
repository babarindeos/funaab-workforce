<x-staff-layout title="APER Form — Part A: University Experience">

    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">{{ $aperForm->name }}</h1>
            <p class="pg-sub">Senior Non-Teaching Staff — Part A: University Experience</p>
        </div>
        <a href="{{ route('staff.aper.index') }}" class="btn-wf-ghost btn-wf-sm">
            <i class="ti ti-arrow-left text-sm"></i> Back to APER Forms
        </a>
    </div>

    {{-- Part Progress --}}
    <div class="wf-card mb-4">
        <div class="flex items-center gap-0">
            <div class="flex items-center gap-2 flex-1">
                <a href="{{ route('staff.aper.open', $aperForm->uuid) }}"
                   class="w-7 h-7 rounded-full bg-green-500 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                    <i class="ti ti-check text-xs"></i>
                </a>
                <a href="{{ route('staff.aper.open', $aperForm->uuid) }}" class="text-xs font-bold text-green-600 hover:underline">Part A</a>
            </div>
            <div class="flex-1 h-px bg-funaab-200 mx-2"></div>
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
        <a href="{{ route('staff.aper.open', $aperForm->uuid) }}"
           class="px-4 py-1.5 text-xs font-semibold text-gray-500 rounded-md hover:bg-white/60">Personal</a>
        <a href="{{ route('staff.aper.senior.part_a2', $aperForm->uuid) }}"
           class="px-4 py-1.5 text-xs font-semibold text-gray-500 rounded-md hover:bg-white/60">Education</a>
        <div class="px-4 py-1.5 text-xs font-bold rounded-md bg-white text-funaab-700 shadow-sm">
            <i class="ti ti-building-bank text-xs"></i> University Experience
        </div>
    </div>

    @if($readonly)
        <div class="flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-semibold px-4 py-3 rounded-lg mb-4">
            <i class="ti ti-lock text-base"></i> This APER form is closed. Displayed in read-only mode.
        </div>
    @endif

    <form action="{{ $readonly ? '#' : route('staff.aper.senior.part_a3.store', $aperForm->uuid) }}" method="POST">
        @csrf
        @if($hasData) @method('PUT') @endif

        @php
            // Reusable helper — textarea list section
            $textSection = function(string $containerId, string $fieldName, array $rows, bool $readonly) {
                return compact('containerId', 'fieldName', 'rows', 'readonly');
            };
        @endphp

        {{-- ── 13a(i): POSITIONS HELD ──────────────────────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-briefcase text-gray-400"></i> 13a(i). Positions Held and Duties Performed During the Period Under Review</div>
            @php $posRows = old('positions', $positions->isNotEmpty() ? $positions->map(fn($p)=>['position_held'=>$p->position_held,'duty'=>$p->duty])->toArray() : [['position_held'=>'','duty'=>'']]) @endphp
            <div id="pos-tbody" class="space-y-3">
                @foreach($posRows as $i => $row)
                    <div class="pos-row border border-gray-100 rounded-lg p-3 bg-gray-50/50">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10.5px] font-bold text-gray-400 tracking-wider">ENTRY {{ $i + 1 }}</span>
                            @if(!$readonly)
                                <button type="button" onclick="removeRow(this, '.pos-row')" class="text-red-400 hover:text-red-600"><i class="ti ti-trash text-sm"></i></button>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="wf-label">Position Held</label>
                                <input type="text" name="positions[{{ $i }}][position_held]" value="{{ $row['position_held'] }}" class="wf-input w-full" {{ $readonly ? 'readonly' : '' }} placeholder="e.g. Ag. Head, Accounts Unit" />
                            </div>
                            <div>
                                <label class="wf-label">Duties</label>
                                <textarea name="positions[{{ $i }}][duty]" rows="3" class="wf-input w-full resize-none" {{ $readonly ? 'readonly' : '' }} placeholder="Describe duties performed…">{{ $row['duty'] }}</textarea>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if(!$readonly)
                <button type="button" onclick="addPosRow()" class="mt-3 btn-wf-ghost btn-wf-sm"><i class="ti ti-plus text-xs"></i> Add Entry</button>
            @endif
        </div>

        {{-- ── 13a(ii)(a): PRESENT JOB DUTIES ─────────────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-list-check text-gray-400"></i> 13a(ii)(a). Present Job — Main Official Duties in Order of Importance</div>
            @php $jobRows = old('present_jobs', $presentJobs->isNotEmpty() ? $presentJobs->pluck('official_duty')->toArray() : ['','']) @endphp
            <div id="job-tbody" class="space-y-2">
                @foreach($jobRows as $i => $job)
                    <div class="job-row flex items-start gap-2">
                        <span class="text-[11px] text-gray-400 font-semibold mt-2.5 w-5 flex-shrink-0">{{ $i + 1 }}.</span>
                        <textarea name="present_jobs[{{ $i }}]" rows="2" class="wf-input w-full resize-none" {{ $readonly ? 'readonly' : '' }} placeholder="Describe duty…">{{ $job }}</textarea>
                        @if(!$readonly)
                            <button type="button" onclick="removeRow(this, '.job-row')" class="mt-2 text-red-400 hover:text-red-600 flex-shrink-0"><i class="ti ti-trash text-sm"></i></button>
                        @endif
                    </div>
                @endforeach
            </div>
            @if(!$readonly)
                <button type="button" onclick="addSimpleRow('job-tbody','present_jobs','job-row', jobIdx++)" class="mt-3 btn-wf-ghost btn-wf-sm"><i class="ti ti-plus text-xs"></i> Add Duty</button>
            @endif
        </div>

        {{-- ── 13b: PRACTICALS ─────────────────────────────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-tools text-gray-400"></i> 13(b). Practicals Involved in During the Period Under Review</div>
            @php $practRows = old('practicals', $practicals->isNotEmpty() ? $practicals->pluck('practical')->toArray() : ['']) @endphp
            <div id="pract-tbody" class="space-y-2">
                @foreach($practRows as $i => $pract)
                    <div class="pract-row flex items-start gap-2">
                        <span class="text-[11px] text-gray-400 font-semibold mt-2.5 w-5 flex-shrink-0">{{ $i + 1 }}.</span>
                        <textarea name="practicals[{{ $i }}]" rows="2" class="wf-input w-full resize-none" {{ $readonly ? 'readonly' : '' }} placeholder="Describe practical…">{{ $pract }}</textarea>
                        @if(!$readonly)
                            <button type="button" onclick="removeRow(this, '.pract-row')" class="mt-2 text-red-400 hover:text-red-600 flex-shrink-0"><i class="ti ti-trash text-sm"></i></button>
                        @endif
                    </div>
                @endforeach
            </div>
            @if(!$readonly)
                <button type="button" onclick="addSimpleRow('pract-tbody','practicals','pract-row', practIdx++)" class="mt-3 btn-wf-ghost btn-wf-sm"><i class="ti ti-plus text-xs"></i> Add Entry</button>
            @endif
        </div>

        {{-- ── 14: PUBLICATIONS ────────────────────────────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-book text-gray-400"></i> 14. Recognized Publications (if any)</div>
            @php $pubRows = old('publications', $publications->isNotEmpty() ? $publications->pluck('publication')->toArray() : ['']) @endphp
            <div id="pub-tbody" class="space-y-2">
                @foreach($pubRows as $i => $pub)
                    <div class="pub-row flex items-start gap-2">
                        <span class="text-[11px] text-gray-400 font-semibold mt-2.5 w-5 flex-shrink-0">{{ $i + 1 }}.</span>
                        <textarea name="publications[{{ $i }}]" rows="2" class="wf-input w-full resize-none" {{ $readonly ? 'readonly' : '' }} placeholder="Full citation of publication…">{{ $pub }}</textarea>
                        @if(!$readonly)
                            <button type="button" onclick="removeRow(this, '.pub-row')" class="mt-2 text-red-400 hover:text-red-600 flex-shrink-0"><i class="ti ti-trash text-sm"></i></button>
                        @endif
                    </div>
                @endforeach
            </div>
            @if(!$readonly)
                <button type="button" onclick="addSimpleRow('pub-tbody','publications','pub-row', pubIdx++)" class="mt-3 btn-wf-ghost btn-wf-sm"><i class="ti ti-plus text-xs"></i> Add Publication</button>
            @endif
        </div>

        {{-- ── 15a: OTHER ACTIVITIES ───────────────────────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-activity text-gray-400"></i> 15a. Other Activities Within the University System</div>
            <p class="text-[11px] text-gray-400 mb-3">e.g. position in Hall Administration, University Sports, University Committees etc.</p>
            @php $actRows = old('activities', $activities->isNotEmpty() ? $activities->pluck('activity')->toArray() : ['']) @endphp
            <div id="act-tbody" class="space-y-2">
                @foreach($actRows as $i => $act)
                    <div class="act-row flex items-start gap-2">
                        <span class="text-[11px] text-gray-400 font-semibold mt-2.5 w-5 flex-shrink-0">{{ $i + 1 }}.</span>
                        <textarea name="activities[{{ $i }}]" rows="2" class="wf-input w-full resize-none" {{ $readonly ? 'readonly' : '' }} placeholder="Describe activity…">{{ $act }}</textarea>
                        @if(!$readonly)
                            <button type="button" onclick="removeRow(this, '.act-row')" class="mt-2 text-red-400 hover:text-red-600 flex-shrink-0"><i class="ti ti-trash text-sm"></i></button>
                        @endif
                    </div>
                @endforeach
            </div>
            @if(!$readonly)
                <button type="button" onclick="addSimpleRow('act-tbody','activities','act-row', actIdx++)" class="mt-3 btn-wf-ghost btn-wf-sm"><i class="ti ti-plus text-xs"></i> Add Activity</button>
            @endif
        </div>

        {{-- ── COMMUNITY SERVICE ───────────────────────────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-heart-handshake text-gray-400"></i> Community Service</div>
            @php $svcRows = old('community_services', $communityServices->isNotEmpty() ? $communityServices->pluck('service')->toArray() : ['']) @endphp
            <div id="svc-tbody" class="space-y-2">
                @foreach($svcRows as $i => $svc)
                    <div class="svc-row flex items-start gap-2">
                        <span class="text-[11px] text-gray-400 font-semibold mt-2.5 w-5 flex-shrink-0">{{ $i + 1 }}.</span>
                        <textarea name="community_services[{{ $i }}]" rows="2" class="wf-input w-full resize-none" {{ $readonly ? 'readonly' : '' }} placeholder="Describe community service…">{{ $svc }}</textarea>
                        @if(!$readonly)
                            <button type="button" onclick="removeRow(this, '.svc-row')" class="mt-2 text-red-400 hover:text-red-600 flex-shrink-0"><i class="ti ti-trash text-sm"></i></button>
                        @endif
                    </div>
                @endforeach
            </div>
            @if(!$readonly)
                <button type="button" onclick="addSimpleRow('svc-tbody','community_services','svc-row', svcIdx++)" class="mt-3 btn-wf-ghost btn-wf-sm"><i class="ti ti-plus text-xs"></i> Add Entry</button>
            @endif
        </div>

        {{-- ── 16: CHALLENGES ──────────────────────────────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-alert-triangle text-gray-400"></i> 16. Challenges Encountered in the Course of Discharging Duties</div>
            @php $chalRows = old('challenges', $challenges->isNotEmpty() ? $challenges->pluck('challenge')->toArray() : ['']) @endphp
            <div id="chal-tbody" class="space-y-2">
                @foreach($chalRows as $i => $chal)
                    <div class="chal-row flex items-start gap-2">
                        <span class="text-[11px] text-gray-400 font-semibold mt-2.5 w-5 flex-shrink-0">{{ $i + 1 }}.</span>
                        <textarea name="challenges[{{ $i }}]" rows="2" class="wf-input w-full resize-none" {{ $readonly ? 'readonly' : '' }} placeholder="Describe challenge…">{{ $chal }}</textarea>
                        @if(!$readonly)
                            <button type="button" onclick="removeRow(this, '.chal-row')" class="mt-2 text-red-400 hover:text-red-600 flex-shrink-0"><i class="ti ti-trash text-sm"></i></button>
                        @endif
                    </div>
                @endforeach
            </div>
            @if(!$readonly)
                <button type="button" onclick="addSimpleRow('chal-tbody','challenges','chal-row', chalIdx++)" class="mt-3 btn-wf-ghost btn-wf-sm"><i class="ti ti-plus text-xs"></i> Add Challenge</button>
            @endif
        </div>

        @if(!$readonly)
            <div class="flex items-center justify-between mb-6">
                <a href="{{ route('staff.aper.senior.part_a2', $aperForm->uuid) }}" class="btn-wf-ghost">
                    <i class="ti ti-arrow-left text-sm"></i> Back to Education
                </a>
                <button type="submit" class="btn-wf-primary">
                    <i class="ti ti-device-floppy text-sm"></i> Save &amp; Continue to Part B
                </button>
            </div>
        @endif
    </form>

    <script>
        let posIdx   = {{ count($posRows) }};
        let jobIdx   = {{ count($jobRows) }};
        let practIdx = {{ count($practRows) }};
        let pubIdx   = {{ count($pubRows) }};
        let actIdx   = {{ count($actRows) }};
        let svcIdx   = {{ count($svcRows) }};
        let chalIdx  = {{ count($chalRows) }};

        function addPosRow() {
            const container = document.getElementById('pos-tbody');
            const i   = posIdx++;
            const num = container.querySelectorAll('.pos-row').length + 1;
            const div = document.createElement('div');
            div.className = 'pos-row border border-gray-100 rounded-lg p-3 bg-gray-50/50';
            div.innerHTML = `
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10.5px] font-bold text-gray-400 tracking-wider">ENTRY ${num}</span>
                    <button type="button" onclick="removeRow(this, '.pos-row')" class="text-red-400 hover:text-red-600"><i class="ti ti-trash text-sm"></i></button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div><label class="wf-label">Position Held</label><input type="text" name="positions[${i}][position_held]" class="wf-input w-full" placeholder="e.g. Ag. Head, Accounts Unit" /></div>
                    <div><label class="wf-label">Duties</label><textarea name="positions[${i}][duty]" rows="3" class="wf-input w-full resize-none" placeholder="Describe duties performed…"></textarea></div>
                </div>`;
            container.appendChild(div);
        }

        function addSimpleRow(containerId, fieldName, rowClass, idx) {
            const container = document.getElementById(containerId);
            const num = container.querySelectorAll(`.${rowClass}`).length + 1;
            const div = document.createElement('div');
            div.className = `${rowClass} flex items-start gap-2`;
            div.innerHTML = `
                <span class="text-[11px] text-gray-400 font-semibold mt-2.5 w-5 flex-shrink-0">${num}.</span>
                <textarea name="${fieldName}[${idx}]" rows="2" class="wf-input w-full resize-none" placeholder="Enter details…"></textarea>
                <button type="button" onclick="removeRow(this, '.${rowClass}')" class="mt-2 text-red-400 hover:text-red-600 flex-shrink-0"><i class="ti ti-trash text-sm"></i></button>`;
            container.appendChild(div);
        }

        function removeRow(btn, selector) {
            if (document.querySelectorAll(selector).length > 1) btn.closest('div').remove();
        }
    </script>

</x-staff-layout>
