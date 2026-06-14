<x-settings-page title="States" subtitle="All 36 states and FCT"
    back-route="admin.settings.index"
    add-label="Add State"
    add-onclick="document.getElementById('modal-create').classList.remove('hidden')">

    {{-- Search + filter --}}
    <form method="GET" class="flex gap-2 mb-4">
        <div class="relative flex-1">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search State name…"
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
            <a href="{{ route('admin.states.index') }}" class="btn-wf-ghost">Clear</a>
        @endif
    </form>

    <div class="wf-card">
        <table class="wf-table">
            <thead>
                <tr><th>#</th><th>STATE NAME</th><th>GEO-POL ZONE</th><th>LGAs</th><th>CREATED</th><th class="text-right">ACTIONS</th></tr>
            </thead>
            <tbody>
                @forelse($states as $state)
                <tr>
                    <td class="text-gray-400">{{ $loop->iteration }}</td>
                    <td class="col-name">{{ $state->state_name }}</td>
                    <td>{{ $state->geoPolZone->zone ?? '—' }}</td>
                    <td><span class="badge badge-info">{{ $state->lgas->count() ?? 0 }} LGAs</span></td>
                    <td class="text-gray-400 text-xs">{{ $state->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="flex items-center justify-end gap-1">
                            <button onclick="openEdit({{ $state->id }}, '{{ addslashes($state->state_name) }}', {{ $state->geo_pol_zone_id }})"
                                    class="btn-wf-ghost btn-wf-sm"><i class="ti ti-edit text-xs"></i> Edit</button>
                            <form action="{{ route('admin.states.delete', $state) }}" method="POST"
                                  onsubmit="return confirm('Delete this state?')">
                                @csrf @method('DELETE')
                                <button class="btn-wf-danger btn-wf-sm"><i class="ti ti-trash text-xs"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-10 text-gray-400"><i class="ti ti-map-pin text-3xl block mb-2"></i>No states defined.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($states->count())
            <div class="p-3 border-t border-gray-100">{{ $states->links() }}</div>
        @endif
    </div>

    @php $zonesList = $zones ?? []; @endphp

    {{-- CREATE MODAL --}}
    <div id="modal-create" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Add State</h2>
                <button onclick="document.getElementById('modal-create').classList.add('hidden')" class="text-gray-400"><i class="ti ti-x"></i></button>
            </div>
            <form action="{{ route('admin.states.store') }}" method="POST" class="px-5 py-4 space-y-3">
                @csrf
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">STATE NAME <span class="text-red-500">*</span></label>
                    <input type="text" name="state_name" value="{{ old('state_name') }}"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500" required />
                </div>
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">GEO-POLITICAL ZONE <span class="text-red-500">*</span></label>
                    <select name="geo_pol_zone_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500" required>
                        <option value="">— Select Zone —</option>
                        @foreach($zonesList as $zone)
                            <option value="{{ $zone->id }}">{{ $zone->zone }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-create').classList.add('hidden')" class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary">Save State</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Edit State</h2>
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-gray-400"><i class="ti ti-x"></i></button>
            </div>
            <form id="edit-form" method="POST" class="px-5 py-4 space-y-3">
                @csrf @method('PUT')
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">STATE NAME</label>
                    <input type="text" name="state_name" id="edit-name"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500" required />
                </div>
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">GEO-POLITICAL ZONE</label>
                    <select name="geo_pol_zone_id" id="edit-zone"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500">
                        @foreach($zonesList as $zone)
                            <option value="{{ $zone->id }}">{{ $zone->zone }}</option>
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
        function openEdit(id, name, zoneId) {
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-zone').value = zoneId;
            document.getElementById('edit-form').action = `/admin/states/${id}`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }
    </script>

</x-settings-page>
