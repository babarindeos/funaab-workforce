<x-progression-page title="Progression" subtitle="Staff progression management" active-tab="salary_levels">

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
            <i class="ti ti-chart-bar text-gray-400"></i> Salary Levels (Grade Levels)
        </h2>
        <button onclick="document.getElementById('modal-add').classList.remove('hidden')" class="btn-wf-primary btn-wf-sm">
            <i class="ti ti-plus text-xs"></i> Add Level
        </button>
    </div>

    {{-- Visual grid --}}
    <div class="grid grid-cols-6 md:grid-cols-9 gap-2 mb-5">
        @foreach($salaryLevels as $sl)
        <div class="bg-funaab-50 border border-funaab-100 rounded-lg p-3 text-center cursor-pointer
                    hover:bg-funaab-100 hover:border-funaab-300 transition-colors group"
             onclick="openEdit({{ $sl->id }}, '{{ $sl->level }}')">
            <div class="text-[9px] font-bold tracking-widest text-funaab-600 mb-1">{{ $sl->salaryStructure?->salary_structure_name }}</div>
            <div class="text-xl font-bold text-funaab-800">{{ $sl->level }}</div>
            <div class="text-[9px] text-funaab-500 mt-1">{{ $sl->designations_count }} desig.</div>
        </div>
        @endforeach
    </div>

    {{-- Table view --}}
    <table class="wf-table">
        <thead>
            <tr><th>#</th><th>GRADE LEVEL</th><th>UUID</th><th>DESIGNATIONS</th><th>CREATED</th><th class="text-right">ACTIONS</th></tr>
        </thead>
        <tbody>
            @forelse($salaryLevels as $sl)
            <tr>
                <td class="text-gray-400">{{ $loop->iteration }}</td>
                <td class="col-name">{{ $sl->salaryStructure?->salary_structure_name }} {{ $sl->level }}</td>
                <td class="col-mono text-[10px] text-gray-400">{{ Str::limit($sl->uuid, 20, '…') }}</td>
                <td><span class="badge badge-info">{{ $sl->designations_count }}</span></td>
                <td class="text-gray-400 text-xs">{{ $sl->created_at->format('d M Y') }}</td>
                <td>
                    <div class="flex items-center justify-end gap-1">
                        <button onclick="openEdit({{ $sl->id }}, '{{ $sl->level }}')"
                                class="btn-wf-ghost btn-wf-sm"><i class="ti ti-edit text-xs"></i> Edit</button>
                        <form action="{{ route('admin.salary_levels.destroy', $sl) }}" method="POST"
                              onsubmit="return confirm('Delete GL {{ $sl->level }}?')">
                            @csrf @method('DELETE')
                            <button class="btn-wf-danger btn-wf-sm"><i class="ti ti-trash text-xs"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-10 text-gray-400">
                <i class="ti ti-chart-bar text-3xl block mb-2"></i>No salary levels defined yet.
            </td></tr>
            @endforelse
        </tbody>
    </table>

    @php $inp = 'w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50';
         $lbl = 'block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5'; @endphp

    {{-- ADD MODAL --}}
    <div id="modal-add" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Add Salary Level</h2>
                <button onclick="document.getElementById('modal-add').classList.add('hidden')" class="text-gray-400"><i class="ti ti-x"></i></button>
            </div>
            <form action="{{ route('admin.salary_levels.store') }}" method="POST" class="px-5 py-4">
                @csrf
                <div class="grid grid-cols-1 gap-3">
                        <div class="col-span-2">
                                <label class="{{ $lbl }}">SALARY STRUCTURE <span class="text-red-500">*</span></label>
                                <select name="salary_structure_id" class="{{ $inp }}" required>
                                    <option value="">— Select Salary Structure —</option>
                                    @foreach($salaryStructures as $salaryStructure)
                                        <option value="{{ $salaryStructure->id }}" {{ old('salary_structure_id') == $salaryStructure->id ? 'selected' : '' }}>
                                            {{ $salaryStructure->salary_structure_name }} 
                                        </option>
                                    @endforeach

                                
                                </select>
                                @error('salary_structure_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="mb-4">
                            <label class="{{ $lbl }}">GRADE LEVEL NUMBER <span class="text-red-500">*</span></label>
                            <input type="number" name="level" value="{{ old('level') }}" min="1" max="25"
                                placeholder="e.g. 07, 12, 14"
                                class="{{ $inp }}" required />
                            <p class="text-[10.5px] text-gray-400 mt-1">Will be zero-padded to 2 digits (e.g. 7 → 07).</p>
                            @error('level')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-add').classList.add('hidden')" class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary"><i class="ti ti-device-floppy text-sm"></i> Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Edit Salary Level</h2>
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-gray-400"><i class="ti ti-x"></i></button>
            </div>
            <form id="edit-form" method="POST" class="px-5 py-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 gap-3">
                        <div class="col-span-2">
                                        <label class="{{ $lbl }}">SALARY STRUCTURE <span class="text-red-500">*</span></label>
                                        <select name="salary_structure_id" class="{{ $inp }}" required>
                                            <option value="">— Select Salary Structure —</option>
                                            @foreach($salaryStructures as $salaryStructure)
                                                <option value="{{ $salaryStructure->id }}" {{ old('salary_structure_id') == $salaryStructure->id ? 'selected' : '' }}>
                                                    {{ $salaryStructure->salary_structure_name }} 
                                                </option>
                                            @endforeach

                                        
                                        </select>
                                        @error('salary_structure_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="{{ $lbl }}">GRADE LEVEL NUMBER</label>
                            <input type="number" name="level" id="e-level" min="1" max="25" class="{{ $inp }}" required />
                        </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')" class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary"><i class="ti ti-device-floppy text-sm"></i> Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEdit(id, level) {
            document.getElementById('e-level').value = level;
            document.getElementById('edit-form').action = `/admin/salary_levels/${id}`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }
    </script>

</x-progression-page>
