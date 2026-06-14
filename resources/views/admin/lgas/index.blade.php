<x-settings-page title="Local Government Areas" subtitle="774 LGAs across all states"
    back-route="admin.settings.index"
    add-label="Add LGA"
    add-onclick="document.getElementById('modal-create').classList.remove('hidden')">

    {{-- Search + filter --}}
    <form method="GET" class="flex gap-2 mb-4">
        <div class="relative flex-1">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search LGA name…"
                   class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-funaab-500 bg-white" />
        </div>
        <select name="state_id" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-funaab-500 bg-white">
            <option value="">All States</option>
            @foreach($states as $state)
                <option value="{{ $state->id }}" {{ request('state_id') == $state->id ? 'selected' : '' }}>{{ $state->state_name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-wf-primary"><i class="ti ti-filter text-sm"></i> Filter</button>
        @if(request()->hasAny(['search','state_id']))
            <a href="{{ route('admin.lgas.index') }}" class="btn-wf-ghost">Clear</a>
        @endif
    </form>

    <div class="wf-card">
        <table class="wf-table">
            <thead>
                <tr><th>#</th><th>LGA NAME</th><th>STATE</th><th>CREATED</th><th class="text-right">ACTIONS</th></tr>
            </thead>
            <tbody>
                @forelse($lgas as $lga)
                <tr>
                    <td class="text-gray-400">{{ $lgas->firstItem() + $loop->index }}</td>
                    <td class="col-name">{{ $lga->lga_name }}</td>
                    <td>{{ $lga->state?->state_name ?? '—' }}</td>
                    <td class="text-gray-400 text-xs">{{ $lga->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="flex items-center justify-end gap-1">
                            <button onclick="openEdit({{ $lga->id }}, '{{ addslashes($lga->lga_name) }}', {{ $lga->state_id }})"
                                    class="btn-wf-ghost btn-wf-sm"><i class="ti ti-edit text-xs"></i> Edit</button>
                            <form action="{{ route('admin.lgas.delete', $lga) }}" method="POST"
                                  onsubmit="return confirm('Delete this LGA?')">
                                @csrf @method('DELETE')
                                <button class="btn-wf-danger btn-wf-sm"><i class="ti ti-trash text-xs"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-10 text-gray-400">No LGAs found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($lgas->hasPages())
            <div class="p-3 border-t border-gray-100 flex items-center justify-between">
                <p class="text-xs text-gray-400">Showing {{ $lgas->firstItem() }}–{{ $lgas->lastItem() }} of {{ $lgas->total() }}</p>
                {{ $lgas->links() }}
            </div>
        @endif
    </div>

    {{-- CREATE MODAL --}}
    <div id="modal-create" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Add LGA</h2>
                <button onclick="document.getElementById('modal-create').classList.add('hidden')" class="text-gray-400"><i class="ti ti-x"></i></button>
            </div>
            <form action="{{ route('admin.lgas.store') }}" method="POST" class="px-5 py-4 space-y-3">
                @csrf
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">LGA NAME <span class="text-red-500">*</span></label>
                    <input type="text" name="lga_name" value="{{ old('lga_name') }}"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500" required />
                </div>
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">STATE <span class="text-red-500">*</span></label>
                    <select name="state_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500" required>
                        <option value="">— Select State —</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-create').classList.add('hidden')" class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary">Save LGA</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Edit LGA</h2>
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-gray-400"><i class="ti ti-x"></i></button>
            </div>
            <form id="edit-form" method="POST" class="px-5 py-4 space-y-3">
                @csrf @method('PUT')
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">LGA NAME</label>
                    <input type="text" name="lga_name" id="edit-name"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500" required />
                </div>
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">STATE</label>
                    <select name="state_id" id="edit-state"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500">
                        @foreach($states as $state)
                            <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')" class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEdit(id, name, stateId) {
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-state').value = stateId;
            document.getElementById('edit-form').action = `/admin/lgas/${id}`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }
    </script>

</x-settings-page>
