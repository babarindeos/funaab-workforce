<x-progression-page title="Progression" subtitle="Staff progression management" active-tab="cadre_ranks">

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
            <i class="ti ti-certificate text-gray-400"></i> Cadre Ranks
        </h2>
        <button onclick="document.getElementById('modal-add').classList.remove('hidden')" class="btn-wf-primary btn-wf-sm">
            <i class="ti ti-plus text-xs"></i> Add Rank
        </button>
    </div>

    <table class="wf-table">
        <thead>
            <tr><th>#</th><th>RANK NAME</th><th>CADRE</th><th>DEPARTMENT</th><th>DESIGNATION LINKED</th><th>CREATED</th><th class="text-right">ACTIONS</th></tr>
        </thead>
        <tbody>
            @forelse($cadreRanks as $rank)
            <tr>
                <td class="text-gray-400">{{ $loop->iteration }}</td>
                <td class="col-name">{{ $rank->cadre_rank_name }}</td>
                <td>{{ $rank->cadre?->cadre_name ?? '—' }}</td>
                <td class="text-xs text-gray-500">{{ $rank->cadre?->department?->department_name ?? '—' }}</td>
                <td>
                    @if($rank->designation)
                        <span class="badge badge-active">Linked</span>
                    @else
                        <span class="badge badge-inactive">Not linked</span>
                    @endif
                </td>
                <td class="text-gray-400 text-xs">{{ $rank->created_at->format('d M Y') }}</td>
                <td>
                    <div class="flex items-center justify-end gap-1">
                        <button onclick="openEdit({{ $rank->id }}, '{{ addslashes($rank->cadre_rank_name) }}', {{ $rank->cadre_id }})"
                                class="btn-wf-ghost btn-wf-sm"><i class="ti ti-edit text-xs"></i> Edit</button>
                        <form action="{{ route('admin.cadre_ranks.destroy', $rank) }}" method="POST"
                              onsubmit="return confirm('Delete this rank?')">
                            @csrf @method('DELETE')
                            <button class="btn-wf-danger btn-wf-sm"><i class="ti ti-trash text-xs"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-10 text-gray-400">
                <i class="ti ti-certificate text-3xl block mb-2"></i>No cadre ranks defined yet.
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
                <h2 class="font-bold text-gray-800 text-sm">Add Cadre Rank</h2>
                <button onclick="document.getElementById('modal-add').classList.add('hidden')" class="text-gray-400"><i class="ti ti-x"></i></button>
            </div>
            <form action="{{ route('admin.cadre_ranks.store') }}" method="POST" class="px-5 py-4 space-y-3">
                @csrf
                <div>
                    <label class="{{ $lbl }}">CADRE <span class="text-red-500">*</span></label>
                    <select name="cadre_id" class="{{ $inp }}" required>
                        <option value="">— Select Cadre —</option>
                        @foreach($cadres as $cadre)
                            <option value="{{ $cadre->id }}">{{ $cadre->cadre_name }}</option>
                        @endforeach
                    </select>
                    @error('cadre_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="{{ $lbl }}">RANK NAME <span class="text-red-500">*</span></label>
                    <input type="text" name="cadre_rank_name" value="{{ old('cadre_rank_name') }}"
                           placeholder="e.g. Professor, Lecturer I, Admin Officer II…"
                           class="{{ $inp }}" required />
                    @error('cadre_rank_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
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
                <h2 class="font-bold text-gray-800 text-sm">Edit Cadre Rank</h2>
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-gray-400"><i class="ti ti-x"></i></button>
            </div>
            <form id="edit-form" method="POST" class="px-5 py-4 space-y-3">
                @csrf @method('PUT')
                <div>
                    <label class="{{ $lbl }}">CADRE</label>
                    <select name="cadre_id" id="e-cadre" class="{{ $inp }}" required>
                        @foreach($cadres as $cadre)
                            <option value="{{ $cadre->id }}">{{ $cadre->cadre_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $lbl }}">RANK NAME</label>
                    <input type="text" name="cadre_rank_name" id="e-name" class="{{ $inp }}" required />
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')" class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary"><i class="ti ti-device-floppy text-sm"></i> Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEdit(id, name, cadreId) {
            document.getElementById('e-name').value  = name;
            document.getElementById('e-cadre').value = cadreId;
            document.getElementById('edit-form').action = `/admin/cadre_ranks/${id}`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }
        @if($errors->any()) document.getElementById('modal-add').classList.remove('hidden'); @endif
    </script>

</x-progression-page>
