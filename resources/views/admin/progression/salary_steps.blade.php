<x-progression-page title="Progression" subtitle="Staff progression management" active-tab="salary_steps">

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
            <i class="ti ti-stairs text-gray-400"></i> Salary Steps
        </h2>
        <button onclick="document.getElementById('modal-add').classList.remove('hidden')" class="btn-wf-primary btn-wf-sm">
            <i class="ti ti-plus text-xs"></i> Add Step
        </button>
    </div>

    {{-- Visual row --}}
    <div class="flex flex-wrap gap-2 mb-5">
        @foreach($salarySteps as $step)
        <div class="bg-gold-50 border border-gold-100 rounded-lg px-4 py-3 text-center cursor-pointer
                    hover:bg-gold-100 hover:border-gold-300 transition-colors min-w-[70px]"
             onclick="openEdit({{ $step->id }}, '{{ $step->step }}')">
            <div class="text-[9px] font-bold tracking-widest text-gold-600 mb-1">STEP</div>
            <div class="text-xl font-bold text-gold-800">{{ $step->step }}</div>
            <div class="text-[9px] text-gold-500 mt-1">{{ $step->employments_count }} staff</div>
        </div>
        @endforeach
    </div>

    {{-- Table --}}
    <table class="wf-table">
        <thead>
            <tr><th>#</th><th>STEP</th><th>UUID</th><th>STAFF ON STEP</th><th>CREATED</th><th class="text-right">ACTIONS</th></tr>
        </thead>
        <tbody>
            @forelse($salarySteps as $step)
            <tr>
                <td class="text-gray-400">{{ $loop->iteration }}</td>
                <td class="col-name">{{ $step->salaryLevel->salaryStructure->salary_structure_name}} {{  $step->salaryLevel->level  }} Step {{ $step->step }}</td>
                <td class="col-mono text-[10px] text-gray-400">{{ Str::limit($step->uuid, 20, '…') }}</td>
                <td><span class="badge badge-active">{{ $step->employments_count }}</span></td>
                <td class="text-gray-400 text-xs">{{ $step->created_at->format('d M Y') }}</td>
                <td>
                    <div class="flex items-center justify-end gap-1">
                        <button onclick="openEdit({{ $step->id }}, '{{ $step->step }}')"
                                class="btn-wf-ghost btn-wf-sm"><i class="ti ti-edit text-xs"></i> Edit</button>
                        <form action="{{ route('admin.salary_steps.destroy', $step) }}" method="POST"
                              onsubmit="return confirm('Delete Step {{ $step->step }}?')">
                            @csrf @method('DELETE')
                            <button class="btn-wf-danger btn-wf-sm"><i class="ti ti-trash text-xs"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-10 text-gray-400">
                <i class="ti ti-stairs text-3xl block mb-2"></i>No salary steps defined yet.
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
                <h2 class="font-bold text-gray-800 text-sm">Add Salary Step</h2>
                <button onclick="document.getElementById('modal-add').classList.add('hidden')" class="text-gray-400"><i class="ti ti-x"></i></button>
            </div>
            <form action="{{ route('admin.salary_steps.store') }}" method="POST" class="px-5 py-4">
                @csrf
                <div class="grid grid-cols-1 gap-3">
                        <div class="col-span-2">
                                        <label class="{{ $lbl }}">GRADE LEVEL <span class="text-red-500">*</span></label>
                                        <select name="salary_level_id" class="{{ $inp }}" required>
                                            <option value="">— Select Salary Level  —</option>
                                            @foreach($salaryLevels as $salaryLevel)
                                            <option value="{{ $salaryLevel->id }}" {{ old('salary_level_id') == $salaryLevel->id ? 'selected' : '' }}>
                                               {{ $salaryLevel->salaryStructure->salary_structure_name }} - {{ $salaryLevel->level }}
                                            </option>
                                            @endforeach                                        
                                        </select>
                                        @error('salary_level_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        
                        <div class="mb-4">
                            <label class="{{ $lbl }}">STEP NUMBER <span class="text-red-500">*</span></label>
                            <input type="text" name="step" value="{{ old('step') }}"
                                placeholder="e.g. 1, 2, 3… or 01, 02, 03"
                                class="{{ $inp }}" required />
                            @error('step')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
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
                <h2 class="font-bold text-gray-800 text-sm">Edit Salary Step</h2>
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-gray-400"><i class="ti ti-x"></i></button>
            </div>
            <form id="edit-form" method="POST" class="px-5 py-4">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="{{ $lbl }}">STEP NUMBER</label>
                    <input type="text" name="step" id="e-step" class="{{ $inp }}" required />
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')" class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary"><i class="ti ti-device-floppy text-sm"></i> Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEdit(id, step) {
            document.getElementById('e-step').value = step;
            document.getElementById('edit-form').action = `/admin/salary_steps/${id}`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }
    </script>

</x-progression-page>
