<x-settings-page title="Senatorial Districts" subtitle="Manage senatorial districts by LGA"
    back-route="admin.settings.index"
    add-label="Add Senatorial District"
    add-onclick="document.getElementById('modal-create').classList.remove('hidden')">

    {{-- Search + filter --}}
    <form method="GET" class="flex gap-2 mb-4">
        <div class="relative flex-1">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search senatorial district name…"
                   class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-funaab-500 bg-white" />
        </div>
        <select name="lga_id" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-funaab-500 bg-white">
            <option value="">All LGAs</option>
            @foreach($lgas as $lga)
                <option value="{{ $lga->id }}" {{ request('lga_id') == $lga->id ? 'selected' : '' }}>
                    {{ $lga->lga_name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn-wf-primary"><i class="ti ti-filter text-sm"></i> Filter</button>
        @if(request()->hasAny(['search','lga_id']))
            <a href="{{ route('admin.senatorial_districts.index') }}" class="btn-wf-ghost">Clear</a>
        @endif
    </form>

    <div class="wf-card">
        <table class="wf-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>SENATORIAL DISTRICT</th>
                    <th>LGA</th>
                    <th>CREATED</th>
                    <th class="text-right">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($districts as $district)
                <tr>
                    <td class="text-gray-400">{{ $districts->firstItem() + $loop->index }}</td>
                    <td class="col-name">{{ $district->senatorial_district_name }}</td>
                    <td>{{ $district->lga?->lga_name ?? '—' }}</td>
                    <td class="text-gray-400 text-xs">{{ $district->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="flex items-center justify-end gap-1">
                            <button onclick="openEdit('{{ $district->uuid }}', '{{ addslashes($district->senatorial_district_name) }}', {{ $district->lga_id }})"
                                    class="btn-wf-ghost btn-wf-sm">
                                <i class="ti ti-edit text-xs"></i> Edit
                            </button>
                            <form action="{{ route('admin.senatorial_districts.destroy', $district->id) }}"
                                  method="POST" onsubmit="return confirm('Delete this senatorial district?')">
                                @csrf @method('DELETE')
                                <button class="btn-wf-danger btn-wf-sm"><i class="ti ti-trash text-xs"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-10 text-gray-400">No senatorial districts found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($districts->hasPages())
            <div class="p-3 border-t border-gray-100 flex items-center justify-between">
                <p class="text-xs text-gray-400">
                    Showing {{ $districts->firstItem() }}–{{ $districts->lastItem() }} of {{ $districts->total() }}
                </p>
                {{ $districts->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- CREATE MODAL --}}
    <div id="modal-create" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Add Senatorial District</h2>
                <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
            </div>
            <form action="{{ route('admin.senatorial_districts.store') }}" method="POST" class="px-5 py-4 space-y-3">
                @csrf
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">
                        SENATORIAL DISTRICT NAME <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="senatorial_district_name" value="{{ old('senatorial_district_name') }}"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                  focus:outline-none focus:border-funaab-500"
                           placeholder="e.g. Oyo North" required />
                </div>
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">
                        LGA <span class="text-red-500">*</span>
                    </label>
                    <select name="lga_id"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                   focus:outline-none focus:border-funaab-500" required>
                        <option value="">— Select LGA —</option>
                        @foreach($lgas as $lga)
                            <option value="{{ $lga->id }}" {{ old('lga_id') == $lga->id ? 'selected' : '' }}>
                                {{ $lga->lga_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                            onclick="document.getElementById('modal-create').classList.add('hidden')"
                            class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary">
                        <i class="ti ti-device-floppy text-sm"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Edit Senatorial District</h2>
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
            </div>
            <form id="edit-form" method="POST" class="px-5 py-4 space-y-3">
                @csrf @method('PUT')
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">
                        SENATORIAL DISTRICT NAME
                    </label>
                    <input type="text" name="senatorial_district_name" id="edit-name"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                  focus:outline-none focus:border-funaab-500" required />
                </div>
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">LGA</label>
                    <select name="lga_id" id="edit-lga"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                   focus:outline-none focus:border-funaab-500">
                        @foreach($lgas as $lga)
                            <option value="{{ $lga->id }}">{{ $lga->lga_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                            onclick="document.getElementById('modal-edit').classList.add('hidden')"
                            class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary">
                        <i class="ti ti-device-floppy text-sm"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEdit(uuid, name, lgaId) {
            document.getElementById('edit-name').value  = name;
            document.getElementById('edit-lga').value   = lgaId;
            document.getElementById('edit-form').action = `/admin/senatorial-districts/${uuid}`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }

        @if($errors->any())
            document.getElementById('modal-create').classList.remove('hidden');
        @endif
    </script>

</x-settings-page>
