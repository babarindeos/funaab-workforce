<x-staff-layout title="APER Form — Part A: Education">

    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">{{ $aperForm->name }}</h1>
            <p class="pg-sub">Senior Non-Teaching Staff — Part A: Education &amp; Qualifications</p>
        </div>
        <a href="{{ route('staff.aper.index') }}" class="btn-wf-ghost btn-wf-sm">
            <i class="ti ti-arrow-left text-sm"></i> Back to APER Forms
        </a>
    </div>

    @include('staff.aper._status_banner')

    {{-- Part Progress --}}
    <div class="wf-card mb-4">
        <div class="flex items-center gap-0">
            <div class="flex items-center gap-2 flex-1">
                <a href="{{ route('staff.aper.open', $aperForm->uuid) }}"
                   class="w-7 h-7 rounded-full bg-green-500 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                    <i class="ti ti-check text-xs"></i>
                </a>
                <a href="{{ route('staff.aper.open', $aperForm->uuid) }}" class="text-xs font-bold text-green-600 hover:underline">Part A</a>
                <span class="text-[10px] text-green-400">Personal Info</span>
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
           class="px-4 py-1.5 text-xs font-semibold text-gray-500 rounded-md hover:bg-white/60">
            Personal
        </a>
        <div class="px-4 py-1.5 text-xs font-bold rounded-md bg-white text-funaab-700 shadow-sm">
            <i class="ti ti-school text-xs"></i> Education
        </div>
        <a href="{{ route('staff.aper.senior.part_a3', $aperForm->uuid) }}"
           class="px-4 py-1.5 text-xs font-semibold text-gray-500 rounded-md hover:bg-white/60">
            University Experience
        </a>
    </div>


    <form action="{{ $readonly ? '#' : route('staff.aper.senior.part_a2.store', $aperForm->uuid) }}" method="POST">
        @csrf
        @if($hasQuals || $hasProfs || $hasAssocs) @method('PUT') @endif

        {{-- ── 12(a): ACADEMIC QUALIFICATIONS ─────────────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-certificate text-gray-400"></i> 12(a). Academic Qualifications</div>
            <p class="text-[11px] text-gray-400 mb-3">Tick <strong>*New this period</strong> for any additional qualification obtained during the period under review.</p>

            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden" id="qual-table">
                    <thead>
                        <tr class="bg-gray-50 text-[10.5px] font-bold tracking-wider text-gray-500">
                            <th class="px-3 py-2.5 text-left border-b border-gray-200">DEGREE / DIPLOMA / CERTIFICATE</th>
                            <th class="px-3 py-2.5 text-left border-b border-gray-200">CLASS (IF ANY)</th>
                            <th class="px-3 py-2.5 text-left border-b border-gray-200">INSTITUTION</th>
                            <th class="px-3 py-2.5 text-left border-b border-gray-200">DATE OF AWARD</th>
                            <th class="px-3 py-2.5 text-center border-b border-gray-200" title="Additional qualification obtained during the period under review">*NEW THIS PERIOD</th>
                            @if(!$readonly) <th class="px-3 py-2.5 w-10 border-b border-gray-200"></th> @endif
                        </tr>
                    </thead>
                    <tbody id="qual-tbody">
                        @php
                            $qualRows = old('qualifications',
                                $qualifications->isNotEmpty()
                                    ? $qualifications->map(fn($q) => ['degree'=>$q->degree,'class'=>$q->class,'institution'=>$q->institution,'date_obtained'=>$q->date_obtained,'is_additional'=>$q->is_additional])->toArray()
                                    : [['degree'=>'','class'=>'','institution'=>'','date_obtained'=>'','is_additional'=>0]]
                            );
                        @endphp
                        @foreach($qualRows as $i => $row)
                            <tr class="qual-row border-b border-gray-100">
                                <td class="px-3 py-2"><input type="text" name="qualifications[{{ $i }}][degree]" value="{{ $row['degree'] }}" class="wf-input w-full" {{ $readonly ? 'readonly' : '' }} placeholder="e.g. B.Sc." /></td>
                                <td class="px-3 py-2"><input type="text" name="qualifications[{{ $i }}][class]" value="{{ $row['class'] }}" class="wf-input w-full" {{ $readonly ? 'readonly' : '' }} placeholder="e.g. Second Class Upper" /></td>
                                <td class="px-3 py-2"><input type="text" name="qualifications[{{ $i }}][institution]" value="{{ $row['institution'] }}" class="wf-input w-full" {{ $readonly ? 'readonly' : '' }} placeholder="Institution name" /></td>
                                <td class="px-3 py-2"><input type="text" name="qualifications[{{ $i }}][date_obtained]" value="{{ $row['date_obtained'] }}" class="wf-input w-full" {{ $readonly ? 'readonly' : '' }} placeholder="e.g. 2005" /></td>
                                <td class="px-3 py-2 text-center"><input type="checkbox" name="qualifications[{{ $i }}][is_additional]" value="1" {{ !empty($row['is_additional']) ? 'checked' : '' }} {{ $readonly ? 'disabled' : '' }} /></td>
                                @if(!$readonly)
                                    <td class="px-3 py-2 text-center">
                                        <button type="button" onclick="removeTableRow(this, '.qual-row')" class="text-red-400 hover:text-red-600"><i class="ti ti-trash text-sm"></i></button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(!$readonly)
                <button type="button" onclick="addQualRow()" class="mt-3 btn-wf-ghost btn-wf-sm">
                    <i class="ti ti-plus text-xs"></i> Add Row
                </button>
            @endif
        </div>

        {{-- ── 12(b): PROFESSIONAL QUALIFICATIONS ─────────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-rosette text-gray-400"></i> 12(b). Professional Qualifications</div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-gray-50 text-[10.5px] font-bold tracking-wider text-gray-500">
                            <th class="px-3 py-2.5 text-left border-b border-gray-200">QUALIFICATION</th>
                            <th class="px-3 py-2.5 text-left border-b border-gray-200">AWARDING BODY / SOCIETY</th>
                            <th class="px-3 py-2.5 text-left border-b border-gray-200">DATE OF AWARD</th>
                            <th class="px-3 py-2.5 text-center border-b border-gray-200" title="Additional qualification obtained during the period under review">*NEW THIS PERIOD</th>
                            @if(!$readonly) <th class="px-3 py-2.5 w-10 border-b border-gray-200"></th> @endif
                        </tr>
                    </thead>
                    <tbody id="prof-tbody">
                        @php
                            $profRows = old('professionals',
                                $professionals->isNotEmpty()
                                    ? $professionals->map(fn($p) => ['qualification'=>$p->qualification,'award_body'=>$p->award_body,'date_obtained'=>$p->date_obtained,'is_additional'=>$p->is_additional])->toArray()
                                    : [['qualification'=>'','award_body'=>'','date_obtained'=>'','is_additional'=>0]]
                            );
                        @endphp
                        @foreach($profRows as $i => $row)
                            <tr class="prof-row border-b border-gray-100">
                                <td class="px-3 py-2"><input type="text" name="professionals[{{ $i }}][qualification]" value="{{ $row['qualification'] }}" class="wf-input w-full" {{ $readonly ? 'readonly' : '' }} placeholder="e.g. ACCA" /></td>
                                <td class="px-3 py-2"><input type="text" name="professionals[{{ $i }}][award_body]" value="{{ $row['award_body'] }}" class="wf-input w-full" {{ $readonly ? 'readonly' : '' }} placeholder="Awarding body" /></td>
                                <td class="px-3 py-2"><input type="text" name="professionals[{{ $i }}][date_obtained]" value="{{ $row['date_obtained'] }}" class="wf-input w-full" {{ $readonly ? 'readonly' : '' }} placeholder="e.g. 2010" /></td>
                                <td class="px-3 py-2 text-center"><input type="checkbox" name="professionals[{{ $i }}][is_additional]" value="1" {{ !empty($row['is_additional']) ? 'checked' : '' }} {{ $readonly ? 'disabled' : '' }} /></td>
                                @if(!$readonly)
                                    <td class="px-3 py-2 text-center">
                                        <button type="button" onclick="removeTableRow(this, '.prof-row')" class="text-red-400 hover:text-red-600"><i class="ti ti-trash text-sm"></i></button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(!$readonly)
                <button type="button" onclick="addProfRow()" class="mt-3 btn-wf-ghost btn-wf-sm">
                    <i class="ti ti-plus text-xs"></i> Add Row
                </button>
            @endif
        </div>

        {{-- ── 12(c): PROFESSIONAL ASSOCIATIONS ───────────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-users-group text-gray-400"></i> 12(c). Membership of Professional Association / Learned Societies</div>

            <div id="assoc-tbody" class="space-y-2">
                @php
                    $assocRows = old('associations',
                        $associations->isNotEmpty()
                            ? $associations->pluck('membership')->toArray()
                            : ['', '']
                    );
                @endphp
                @foreach($assocRows as $i => $mem)
                    <div class="assoc-row flex items-start gap-2">
                        <span class="text-[11px] text-gray-400 font-semibold mt-2.5 w-5 flex-shrink-0">{{ $i + 1 }}.</span>
                        <textarea name="associations[{{ $i }}]" rows="2"
                                  class="wf-input w-full resize-none"
                                  {{ $readonly ? 'readonly' : '' }}
                                  placeholder="e.g. ICAN — Member (2012 to date)">{{ $mem }}</textarea>
                        @if(!$readonly)
                            <button type="button" onclick="removeRow(this, '.assoc-row')"
                                    class="mt-2 text-red-400 hover:text-red-600 flex-shrink-0">
                                <i class="ti ti-trash text-sm"></i>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
            @if(!$readonly)
                <button type="button" onclick="addAssocRow()" class="mt-3 btn-wf-ghost btn-wf-sm">
                    <i class="ti ti-plus text-xs"></i> Add Entry
                </button>
            @endif
        </div>

        @if(!$readonly)
            <div class="flex items-center justify-between mb-6">
                <a href="{{ route('staff.aper.open', $aperForm->uuid) }}" class="btn-wf-ghost">
                    <i class="ti ti-arrow-left text-sm"></i> Back to Personal
                </a>
                <button type="submit" class="btn-wf-primary">
                    <i class="ti ti-arrow-right text-sm"></i> Save &amp; Continue to University Experience
                </button>
            </div>
        @endif
    </form>

    <script>
        let qualIndex = {{ count($qualRows) }};
        let profIndex = {{ count($profRows) }};
        let assocIndex = {{ count($assocRows) }};

        function addQualRow() {
            const tbody = document.getElementById('qual-tbody');
            const i = qualIndex++;
            const tr = document.createElement('tr');
            tr.className = 'qual-row border-b border-gray-100';
            tr.innerHTML = `
                <td class="px-3 py-2"><input type="text" name="qualifications[${i}][degree]" class="wf-input w-full" placeholder="e.g. B.Sc." /></td>
                <td class="px-3 py-2"><input type="text" name="qualifications[${i}][class]" class="wf-input w-full" placeholder="e.g. Second Class Upper" /></td>
                <td class="px-3 py-2"><input type="text" name="qualifications[${i}][institution]" class="wf-input w-full" placeholder="Institution name" /></td>
                <td class="px-3 py-2"><input type="text" name="qualifications[${i}][date_obtained]" class="wf-input w-full" placeholder="e.g. 2005" /></td>
                <td class="px-3 py-2 text-center"><input type="checkbox" name="qualifications[${i}][is_additional]" value="1" /></td>
                <td class="px-3 py-2 text-center"><button type="button" onclick="removeTableRow(this, '.qual-row')" class="text-red-400 hover:text-red-600"><i class="ti ti-trash text-sm"></i></button></td>`;
            tbody.appendChild(tr);
        }

        function addProfRow() {
            const tbody = document.getElementById('prof-tbody');
            const i = profIndex++;
            const tr = document.createElement('tr');
            tr.className = 'prof-row border-b border-gray-100';
            tr.innerHTML = `
                <td class="px-3 py-2"><input type="text" name="professionals[${i}][qualification]" class="wf-input w-full" placeholder="e.g. ACCA" /></td>
                <td class="px-3 py-2"><input type="text" name="professionals[${i}][award_body]" class="wf-input w-full" placeholder="Awarding body" /></td>
                <td class="px-3 py-2"><input type="text" name="professionals[${i}][date_obtained]" class="wf-input w-full" placeholder="e.g. 2010" /></td>
                <td class="px-3 py-2 text-center"><input type="checkbox" name="professionals[${i}][is_additional]" value="1" /></td>
                <td class="px-3 py-2 text-center"><button type="button" onclick="removeTableRow(this, '.prof-row')" class="text-red-400 hover:text-red-600"><i class="ti ti-trash text-sm"></i></button></td>`;
            tbody.appendChild(tr);
        }

        function addAssocRow() {
            const container = document.getElementById('assoc-tbody');
            const i = assocIndex++;
            const num = container.querySelectorAll('.assoc-row').length + 1;
            const div = document.createElement('div');
            div.className = 'assoc-row flex items-start gap-2';
            div.innerHTML = `
                <span class="text-[11px] text-gray-400 font-semibold mt-2.5 w-5 flex-shrink-0">${num}.</span>
                <textarea name="associations[${i}]" rows="2" class="wf-input w-full resize-none" placeholder="e.g. ICAN — Member (2012 to date)"></textarea>
                <button type="button" onclick="removeRow(this, '.assoc-row')" class="mt-2 text-red-400 hover:text-red-600 flex-shrink-0"><i class="ti ti-trash text-sm"></i></button>`;
            container.appendChild(div);
        }

        function removeTableRow(btn, selector) {
            if (document.querySelectorAll(selector).length > 1) btn.closest('tr').remove();
        }

        function removeRow(btn, selector) {
            if (document.querySelectorAll(selector).length > 1) btn.closest('div').remove();
        }
    </script>

</x-staff-layout>
