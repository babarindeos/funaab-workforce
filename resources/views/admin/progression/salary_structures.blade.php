<x-progression-page title="Progression" subtitle="Staff progression management" active-tab="salary_structures">

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
            <i class="ti ti-receipt text-gray-400"></i> Salary Structures
        </h2>
        <button onclick="document.getElementById('modal-add').classList.remove('hidden')" class="btn-wf-primary btn-wf-sm">
            <i class="ti ti-plus text-xs"></i> Add Structure
        </button>
    </div>

    <div class="mb-3 p-3 bg-funaab-50 border border-funaab-100 rounded-lg text-xs text-funaab-700 flex items-start gap-2">
        <i class="ti ti-info-circle text-sm flex-shrink-0 mt-0.5"></i>
        Salary structures (e.g. UASS, CONTISS, CONTEDISS) are the named pay scales used to group designations.
    </div>

    <table class="wf-table">
        <thead>
            <tr><th>#</th><th>STRUCTURE NAME</th><th>UUID</th><th>DESIGNATIONS</th><th>CREATED</th><th class="text-right">ACTIONS</th></tr>
        </thead>
        <tbody>
            @forelse($salaryStructures as $ss)
            <tr>
                <td class="text-gray-400">{{ $loop->iteration }}</td>
                <td class="col-name">{{ $ss->salary_structure_name }}</td>
                <td class="col-mono text-[10px] text-gray-400">{{ Str::limit($ss->uuid, 20, '…') }}</td>
                <td><span class="badge badge-info">{{ $ss->designations_count }}</span></td>
                <td class="text-gray-400 text-xs">{{ $ss->created_at->format('d M Y') }}</td>
                <td>
                    <div class="flex items-center justify-end gap-1">
                        <button onclick="openEdit({{ $ss->id }}, '{{ addslashes($ss->salary_structure_name) }}')"
                                class="btn-wf-ghost btn-wf-sm"><i class="ti ti-edit text-xs"></i> Edit</button>
                        <form action="{{ route('admin.salary_structures.destroy', $ss) }}" method="POST"
                              onsubmit="return confirm('Delete this salary structure?')">
                            @csrf @method('DELETE')
                            <button class="btn-wf-danger btn-wf-sm"><i class="ti ti-trash text-xs"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-10 text-gray-400">
                <i class="ti ti-receipt text-3xl block mb-2"></i>No salary structures defined yet.
            </td></tr>
            @endforelse
        </tbody>
    </table>

    @php $inp = 'w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50';
         $lbl = 'block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5'; @endphp

    {{-- ADD MODAL --}}
    <div id="modal-add" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Add Salary Structure</h2>
                <button onclick="document.getElementById('modal-add').classList.add('hidden')" class="text-gray-400"><i class="ti ti-x"></i></button>
            </div>
            <form action="{{ route('admin.salary_structures.store') }}" method="POST" class="px-5 py-4">
                @csrf
                <div class="mb-4">
                    <label class="{{ $lbl }}">STRUCTURE NAME <span class="text-red-500">*</span></label>
                    <input type="text" name="salary_structure_name" value="{{ old('salary_structure_name') }}"
                           placeholder="e.g. UASS, CONTISS, CONTEDISS…"
                           class="{{ $inp }}" required />
                    @error('salary_structure_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
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
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Edit Salary Structure</h2>
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-gray-400"><i class="ti ti-x"></i></button>
            </div>
            <form id="edit-form" method="POST" class="px-5 py-4">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="{{ $lbl }}">STRUCTURE NAME</label>
                    <input type="text" name="salary_structure_name" id="e-name" class="{{ $inp }}" required />
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')" class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary"><i class="ti ti-device-floppy text-sm"></i> Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEdit(id, name) {
            document.getElementById('e-name').value = name;
            document.getElementById('edit-form').action = `/admin/salary_structures/${id}`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }
        @if($errors->any()) document.getElementById('modal-add').classList.remove('hidden'); @endif
    </script>

</x-progression-page>
