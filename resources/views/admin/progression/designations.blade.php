<x-progression-page title="Progression" subtitle="Staff progression management" active-tab="designations">

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
            <i class="ti ti-id-badge text-gray-400"></i> Designations
        </h2>
        <button onclick="document.getElementById('modal-add').classList.remove('hidden')" class="btn-wf-primary btn-wf-sm">
            <i class="ti ti-plus text-xs"></i> Add Designation
        </button>
    </div>

    <div class="mb-3 p-3 bg-funaab-50 border border-funaab-100 rounded-lg text-xs text-funaab-700 flex items-start gap-2">
        <i class="ti ti-info-circle text-sm flex-shrink-0 mt-0.5"></i>
        A designation links a <strong>Cadre Rank</strong> to a <strong>Salary Structure</strong> and <strong>Grade Level</strong>,
        and optionally points to the next rank for promotion purposes.
    </div>

    <table class="wf-table">
        <thead>
            <tr><th>#</th><th>CADRE RANK</th><th>CADRE</th><th>SALARY STRUCTURE</th><th>GRADE LEVEL</th><th>NEXT RANK</th><th>STAFF</th><th class="text-right">ACTIONS</th></tr>
        </thead>
        <tbody>
            @forelse($designations as $des)
            <tr>
                <td class="text-gray-400">{{ $loop->iteration }}</td>
                <td class="col-name">{{ $des->cadreRank?->cadre_rank_name ?? '—' }}</td>
                <td class="text-xs text-gray-500">{{ $des->cadreRank?->cadre?->cadre_name ?? '—' }}</td>
                <td><span class="badge badge-info">{{ $des->salaryStructure?->salary_structure_name ?? '—' }}</span></td>
                <td><span class="badge badge-active">GL {{ $des->salaryLevel?->level ?? '?' }}</span></td>
                <td class="text-xs text-gray-500">{{ $des->nextRank?->cadre_rank_name ?? '—' }}</td>
                <td>
                    <span class="badge badge-active">{{ $des->employments->count() }}</span>
                </td>
                <td>
                    <div class="flex items-center justify-end gap-1">
                        <button onclick="openEdit(
                                    {{ $des->id }},
                                    {{ $des->cadre_rank_id }},
                                    {{ $des->salary_structure_id }},
                                    {{ $des->salary_level_id }},
                                    {{ $des->next_cadre_rank ?? 'null' }})"
                                class="btn-wf-ghost btn-wf-sm"><i class="ti ti-edit text-xs"></i> Edit</button>
                        <form action="{{ route('admin.designations.destroy', $des) }}" method="POST"
                              onsubmit="return confirm('Delete this designation?')">
                            @csrf @method('DELETE')
                            <button class="btn-wf-danger btn-wf-sm"><i class="ti ti-trash text-xs"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center py-10 text-gray-400">
                <i class="ti ti-id-badge text-3xl block mb-2"></i>No designations defined yet.
            </td></tr>
            @endforelse
        </tbody>
    </table>

    @php
        $inp = 'w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50';
        $lbl = 'block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5';
    @endphp

    {{-- ADD MODAL --}}
    <div id="modal-add" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Add Designation</h2>
                <button onclick="document.getElementById('modal-add').classList.add('hidden')" class="text-gray-400"><i class="ti ti-x"></i></button>
            </div>
            <form action="{{ route('admin.designations.store') }}" method="POST" class="px-5 py-4 space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="{{ $lbl }}">CADRE RANK <span class="text-red-500">*</span></label>
                        <select name="cadre_rank_id" class="{{ $inp }}" required>
                            <option value="">— Select Rank —</option>
                            @foreach($cadreRanks as $rank)
                                <option value="{{ $rank->id }}" {{ old('cadre_rank_id') == $rank->id ? 'selected' : '' }}>
                                    {{ $rank->cadre_rank_name }} ({{ $rank->cadre?->cadre_name }})
                                </option>
                            @endforeach
                        </select>
                        @error('cadre_rank_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="{{ $lbl }}">SALARY STRUCTURE <span class="text-red-500">*</span></label>
                        <select name="salary_structure_id" class="{{ $inp }}" required>
                            <option value="">— Select —</option>
                            @foreach($salaryStructures as $ss)
                                <option value="{{ $ss->id }}">{{ $ss->salary_structure_name }}</option>
                            @endforeach
                        </select>
                        @error('salary_structure_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="{{ $lbl }}">GRADE LEVEL <span class="text-red-500">*</span></label>
                        <select name="salary_level_id" class="{{ $inp }}" required>
                            <option value="">— Select —</option>
                            @foreach($salaryLevels as $sl)
                                <option value="{{ $sl->id }}">GL {{ $sl->level }}</option>
                            @endforeach
                        </select>
                        @error('salary_level_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="col-span-2">
                        <label class="{{ $lbl }}">NEXT RANK FOR PROMOTION <span class="text-gray-400 font-normal">(optional)</span></label>
                        <select name="next_cadre_rank" class="{{ $inp }}">
                            <option value="">— None —</option>
                            @foreach($cadreRanks as $rank)
                                <option value="{{ $rank->id }}">{{ $rank->cadre_rank_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add').classList.add('hidden')" class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary"><i class="ti ti-device-floppy text-sm"></i> Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Edit Designation</h2>
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-gray-400"><i class="ti ti-x"></i></button>
            </div>
            <form id="edit-form" method="POST" class="px-5 py-4 space-y-3">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="{{ $lbl }}">CADRE RANK</label>
                        <select name="cadre_rank_id" id="e-rank" class="{{ $inp }}" required>
                            @foreach($cadreRanks as $rank)
                                <option value="{{ $rank->id }}">{{ $rank->cadre_rank_name }} ({{ $rank->cadre?->cadre_name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $lbl }}">SALARY STRUCTURE</label>
                        <select name="salary_structure_id" id="e-struct" class="{{ $inp }}" required>
                            @foreach($salaryStructures as $ss)
                                <option value="{{ $ss->id }}">{{ $ss->salary_structure_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $lbl }}">GRADE LEVEL</label>
                        <select name="salary_level_id" id="e-level" class="{{ $inp }}" required>
                            @foreach($salaryLevels as $sl)
                                <option value="{{ $sl->id }}">GL {{ $sl->level }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="{{ $lbl }}">NEXT RANK FOR PROMOTION</label>
                        <select name="next_cadre_rank" id="e-next" class="{{ $inp }}">
                            <option value="">— None —</option>
                            @foreach($cadreRanks as $rank)
                                <option value="{{ $rank->id }}">{{ $rank->cadre_rank_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')" class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary"><i class="ti ti-device-floppy text-sm"></i> Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEdit(id, rankId, structId, levelId, nextId) {
            document.getElementById('e-rank').value   = rankId;
            document.getElementById('e-struct').value = structId;
            document.getElementById('e-level').value  = levelId;
            document.getElementById('e-next').value   = nextId || '';
            document.getElementById('edit-form').action = `/admin/designations/${id}`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }
        @if($errors->any()) document.getElementById('modal-add').classList.remove('hidden'); @endif
    </script>

</x-progression-page>
