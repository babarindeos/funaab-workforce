<x-staff-layout title="Supervisor Assessment Form">

    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">Supervisor Assessment</h1>
            <p class="pg-sub">{{ $aperForm->name }} — {{ $staff->surname }}, {{ $staff->firstname }}</p>
        </div>
        <a href="{{ route('staff.supervisor.staff_list', $aperForm->uuid) }}" class="btn-wf-ghost btn-wf-sm">
            <i class="ti ti-arrow-left text-sm"></i> Back to Staff List
        </a>
    </div>

    {{-- Staff Info Card --}}
    <div class="wf-card mb-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">STAFF NAME</p>
                <p class="text-sm font-bold text-gray-700">{{ $staff->surname }}, {{ $staff->firstname }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">FILE NO.</p>
                <p class="text-sm font-bold text-gray-700 font-mono">{{ $staff->staff_no }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">DEPARTMENT</p>
                <p class="text-sm font-bold text-gray-700">{{ $supervisor->department?->department_name }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold tracking-wider text-gray-400 mb-1">FORM STATUS</p>
                @if($isOpen)
                    <span class="badge badge-active">Open</span>
                @else
                    <span class="badge badge-inactive">Closed</span>
                @endif
            </div>
        </div>
    </div>

    @if(!$isOpen && !$hasExisting)
        <div class="wf-card flex flex-col items-center justify-center py-16 text-center">
            <i class="ti ti-lock text-3xl text-gray-300 mb-3"></i>
            <p class="text-sm font-bold text-gray-500">This APER form is closed and no assessment was submitted.</p>
        </div>
    @else

    <form action="{{ route('staff.supervisor.store_assessment', [$aperForm->uuid, $staff->uuid]) }}"
          method="POST">
        @csrf
        @if($hasExisting) @method('PUT') @endif

        {{-- ── ITEM 15: SICK LEAVE ───────────────────────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title">
                <i class="ti ti-bed text-gray-400"></i>
                15. Number of Days of Sick Leave During Period
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="wf-label">15(a). With Medical Certificate <span class="text-red-500">*</span></label>
                    <input type="text" name="with_medical_certificate"
                           value="{{ old('with_medical_certificate', $sickLeave?->with_medical_certificate) }}"
                           class="wf-input w-full"
                           {{ (!$isOpen) ? 'readonly' : '' }}
                           placeholder="e.g. 5 days" required />
                    @error('with_medical_certificate')
                        <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="wf-label">15(b). Without Medical Certificate <span class="text-red-500">*</span></label>
                    <input type="text" name="without_medical_certificate"
                           value="{{ old('without_medical_certificate', $sickLeave?->without_medical_certificate) }}"
                           class="wf-input w-full"
                           {{ (!$isOpen) ? 'readonly' : '' }}
                           placeholder="e.g. 2 days" required />
                    @error('without_medical_certificate')
                        <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Health Centre Frequency --}}
            <div>
                <label class="wf-label">Frequency of Attendance at Health Centre for Treatment <span class="text-red-500">*</span></label>
                <select name="frequency" class="wf-input w-full max-w-sm"
                        {{ (!$isOpen) ? 'disabled' : '' }} required>
                    <option value="">— Select Frequency —</option>
                    @foreach($frequencyOptions as $option)
                        <option value="{{ $option }}"
                            {{ old('frequency', $frequency?->frequency) === $option ? 'selected' : '' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
                @error('frequency')
                    <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- ── ITEM 16: SANCTIONS ────────────────────────────── --}}
        <div class="wf-card mb-4">
            <div class="wf-card-title">
                <i class="ti ti-alert-triangle text-gray-400"></i>
                16. Any Sanctions Incurred by the Employee During the Period
            </div>
            <p class="text-[11px] text-gray-400 mb-3">
                If any, state the nature of such sanctions and disciplinary action taken.
            </p>

            <div id="sanctions-tbody" class="space-y-2">
                @php
                    $sanctionRows = old('sanctions',
                        $sanctions->isNotEmpty()
                            ? $sanctions->pluck('sanction')->toArray()
                            : ['']
                    );
                @endphp
                @foreach($sanctionRows as $i => $sanction)
                    <div class="sanction-row flex items-start gap-2">
                        <span class="text-[11px] text-gray-400 font-semibold mt-2.5 w-5 flex-shrink-0">{{ $i + 1 }}.</span>
                        <textarea name="sanctions[{{ $i }}]" rows="2"
                                  class="wf-input w-full resize-none"
                                  {{ (!$isOpen) ? 'readonly' : '' }}
                                  placeholder="Describe sanction and disciplinary action taken…">{{ $sanction }}</textarea>
                        @if($isOpen)
                            <button type="button" onclick="removeSanctionRow(this)"
                                    class="mt-2 text-red-400 hover:text-red-600 flex-shrink-0">
                                <i class="ti ti-trash text-sm"></i>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>

            @if($isOpen)
                <button type="button" onclick="addSanctionRow()"
                        class="mt-3 btn-wf-ghost btn-wf-sm">
                    <i class="ti ti-plus text-xs"></i> Add Sanction
                </button>
            @endif
        </div>

        {{-- Form Actions --}}
        @if($isOpen)
            <div class="flex items-center justify-end gap-3 mb-6">
                <a href="{{ route('staff.supervisor.staff_list', $aperForm->uuid) }}" class="btn-wf-ghost">Cancel</a>
                <button type="submit" class="btn-wf-primary">
                    <i class="ti ti-device-floppy text-sm"></i>
                    {{ $hasExisting ? 'Update Assessment' : 'Submit Assessment' }}
                </button>
            </div>
        @endif

    </form>
    @endif

    <script>
        let sanctionIndex = {{ count($sanctionRows) }};

        function addSanctionRow() {
            const container = document.getElementById('sanctions-tbody');
            const num = container.querySelectorAll('.sanction-row').length + 1;
            const div = document.createElement('div');
            div.className = 'sanction-row flex items-start gap-2';
            div.innerHTML = `
                <span class="text-[11px] text-gray-400 font-semibold mt-2.5 w-5 flex-shrink-0">${num}.</span>
                <textarea name="sanctions[${sanctionIndex}]" rows="2" class="wf-input w-full resize-none"
                          placeholder="Describe sanction and disciplinary action taken…"></textarea>
                <button type="button" onclick="removeSanctionRow(this)"
                        class="mt-2 text-red-400 hover:text-red-600 flex-shrink-0">
                    <i class="ti ti-trash text-sm"></i>
                </button>`;
            container.appendChild(div);
            sanctionIndex++;
        }

        function removeSanctionRow(btn) {
            if (document.querySelectorAll('.sanction-row').length > 1) {
                btn.closest('.sanction-row').remove();
            }
        }
    </script>

</x-staff-layout>
