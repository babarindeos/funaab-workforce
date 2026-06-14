<x-progression-page title="Progression" subtitle="Staff progression management" active-tab="cadres">

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
            <i class="ti ti-hierarchy-2 text-gray-400"></i> Cadres
        </h2>
        <button onclick="document.getElementById('modal-add').classList.remove('hidden')" class="btn-wf-primary btn-wf-sm">
            <i class="ti ti-plus text-xs"></i> Add Cadre
        </button>
    </div>

    <table class="wf-table">
        <thead>
            <tr><th>#</th><th>CADRE NAME</th><th>DEPARTMENT</th><th>RANKS</th><th>CREATED</th><th class="text-right">ACTIONS</th></tr>
        </thead>
        <tbody>
            @forelse($cadres as $cadre)
            <tr>
                <td class="text-gray-400">{{ $loop->iteration }}</td>
                <td class="col-name">{{ $cadre->cadre_name }}</td>
                <td>{{ $cadre->department?->department_name ?? '—' }}</td>
                <td><span class="badge badge-info">{{ $cadre->ranks_count }} ranks</span></td>
                <td class="text-gray-400 text-xs">{{ $cadre->created_at->format('d M Y') }}</td>
                <td>
                    <div class="flex items-center justify-end gap-1">
                        <button onclick="openEdit({{ $cadre->id }}, '{{ addslashes($cadre->cadre_name) }}', {{ $cadre->department_id }})"
                                class="btn-wf-ghost btn-wf-sm"><i class="ti ti-edit text-xs"></i> Edit</button>
                        <form action="{{ route('admin.cadres.destroy', $cadre) }}" method="POST"
                              onsubmit="return confirm('Delete this cadre?')">
                            @csrf @method('DELETE')
                            <button class="btn-wf-danger btn-wf-sm"><i class="ti ti-trash text-xs"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-10 text-gray-400">
                <i class="ti ti-hierarchy-2 text-3xl block mb-2"></i>No cadres defined yet.
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
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Add Cadre</h2>
                <button onclick="document.getElementById('modal-add').classList.add('hidden')" class="text-gray-400"><i class="ti ti-x"></i></button>
            </div>
            <form action="{{ route('admin.cadres.store') }}" method="POST" class="px-5 py-4 space-y-3">
                @csrf
                <div>
                    <label class="{{ $lbl }}">CADRE NAME <span class="text-red-500">*</span></label>
                    <input type="text" name="cadre_name" value="{{ old('cadre_name') }}"
                           placeholder="e.g. Teaching / Academic, Administrative…"
                           class="{{ $inp }}" required />
                    @error('cadre_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="{{ $lbl }}">DEPARTMENT <span class="text-red-500">*</span></label>
                    <select name="department_id" class="{{ $inp }}" required>
                        <option value="">— Select Department —</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->department_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
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
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Edit Cadre</h2>
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-gray-400"><i class="ti ti-x"></i></button>
            </div>
            <form id="edit-form" method="POST" class="px-5 py-4 space-y-3">
                @csrf @method('PUT')
                <div>
                    <label class="{{ $lbl }}">CADRE NAME</label>
                    <input type="text" name="cadre_name" id="e-name" class="{{ $inp }}" required />
                </div>
                <div>
                    <label class="{{ $lbl }}">DEPARTMENT</label>
                    <select name="department_id" id="e-dept" class="{{ $inp }}" required>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')" class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary"><i class="ti ti-device-floppy text-sm"></i> Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEdit(id, name, deptId) {
            document.getElementById('e-name').value = name;
            document.getElementById('e-dept').value = deptId;
            document.getElementById('edit-form').action = `/admin/cadres/${id}`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }
        @if($errors->any()) document.getElementById('modal-add').classList.remove('hidden'); @endif
    </script>

</x-progression-page>
