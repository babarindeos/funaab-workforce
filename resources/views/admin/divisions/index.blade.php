<x-settings-page title="Divisions" subtitle="Colleges, faculties and organisational divisions"
    back-route="admin.settings.index"
    add-label="Add Division"
    add-onclick="document.getElementById('modal-create').classList.remove('hidden')">

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-3 mb-4">
        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-funaab-50 flex items-center justify-center">
                    <i class="ti ti-hierarchy-2 text-funaab-700 text-sm"></i>
                </div>
                TOTAL DIVISIONS
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $total }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widests text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-blue-50 flex items-center justify-center">
                    <i class="ti ti-building text-blue-600 text-sm"></i>
                </div>
                TOTAL DEPARTMENTS
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $divisions->sum('departments_count') }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-purple-50 flex items-center justify-center">
                    <i class="ti ti-layout-distribute-vertical text-purple-600 text-sm"></i>
                </div>
                DIVISION TYPES
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $division_types->count() }}</div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex gap-2 mb-4 flex-wrap">
        <div class="relative flex-1 min-w-[200px]">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search division name or short name…"
                   class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg
                          focus:outline-none focus:border-funaab-500 bg-white" />
        </div>
        <select name="division_type_id"
                class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none
                       focus:border-funaab-500 bg-white">
            <option value="">All Types</option>
            @foreach($division_types as $type)
                <option value="{{ $type->id }}" {{ request('division_type_id') == $type->id ? 'selected' : '' }}>
                    {{ $type->division_type_name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn-wf-primary">
            <i class="ti ti-filter text-sm"></i> Filter
        </button>
        @if(request()->hasAny(['search','division_type_id']))
            <a href="{{ route('admin.divisions.index') }}" class="btn-wf-ghost">Clear</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="wf-card">
        <table class="wf-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>DIVISION NAME</th>
                    <th>SHORT NAME</th>
                    <th>TYPE</th>
                    <th>PARENT</th>
                    <th>DEPARTMENTS</th>
                    <th>CREATED</th>
                    <th class="text-right">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($divisions as $div)
                <tr>
                    <td class="text-gray-400">{{ $divisions->firstItem() + $loop->index }}</td>
                    <td class="col-name">{{ $div->division_name }}</td>
                    <td>
                        <span class="badge badge-info font-mono text-[10px]">{{ $div->short_name }}</span>
                    </td>
                    <td>{{ $div->division_type?->division_type_name ?? '—' }}</td>
                    <td class="text-gray-500">{{ $div->parent?->division_name ?? '—' }}</td>
                    <td>
                        <span class="badge badge-active">{{ $div->departments_count }}</span>
                    </td>
                    <td class="text-gray-400 text-xs">{{ $div->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="flex items-center justify-end gap-1">
                            <button onclick="openEdit(
                                        {{ $div->id }},
                                        '{{ addslashes($div->division_name) }}',
                                        '{{ addslashes($div->short_name) }}',
                                        {{ $div->division_type_id }},
                                        {{ $div->parent_division ?? 'null' }})"
                                    class="btn-wf-ghost btn-wf-sm">
                                <i class="ti ti-edit text-xs"></i> Edit
                            </button>
                            <form action="{{ route('admin.divisions.delete', $div) }}" method="POST"
                                  onsubmit="return confirm('Delete {{ addslashes($div->division_name) }}? This will also remove all linked departments.')">
                                @csrf @method('DELETE')
                                <button class="btn-wf-danger btn-wf-sm">
                                    <i class="ti ti-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-10 text-gray-400">
                        <i class="ti ti-hierarchy-2 text-3xl block mb-2"></i>
                        No divisions found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($divisions->hasPages())
            <div class="p-3 border-t border-gray-100 flex items-center justify-between">
                <p class="text-xs text-gray-400">
                    Showing {{ $divisions->firstItem() }}–{{ $divisions->lastItem() }}
                    of {{ $divisions->total() }} divisions
                </p>
                {{ $divisions->links() }}
            </div>
        @endif
    </div>

    {{-- CREATE MODAL --}}
    <div id="modal-create" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Add Division</h2>
                <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
            </div>
            <form action="{{ route('admin.divisions.store') }}" method="POST" class="px-5 py-4 space-y-3">
                @csrf
                @php $inp = 'w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50';
                     $lbl = 'block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5'; @endphp

                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="{{ $lbl }}">DIVISION NAME <span class="text-red-500">*</span></label>
                        <input type="text" name="division_name" value="{{ old('division_name') }}"
                               placeholder="e.g. College of Plant Science and Crop Production"
                               class="{{ $inp }}" required />
                        @error('division_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="{{ $lbl }}">SHORT NAME <span class="text-red-500">*</span></label>
                        <input type="text" name="short_name" value="{{ old('short_name') }}"
                               placeholder="e.g. COLPLANT"
                               class="{{ $inp }}" required />
                        @error('short_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="{{ $lbl }}">DIVISION TYPE <span class="text-red-500">*</span></label>
                        <select name="division_type_id" class="{{ $inp }}" required>
                            <option value="">— Select Type —</option>
                            @foreach($division_types as $type)
                                <option value="{{ $type->id }}">{{ $type->division_type_name }}</option>
                            @endforeach
                        </select>
                        @error('division_type_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="col-span-2">
                        <label class="{{ $lbl }}">PARENT DIVISION <span class="text-gray-400 font-normal">(optional)</span></label>
                        <select name="parent_division" class="{{ $inp }}">
                            <option value="">— None (Top-level) —</option>
                            @foreach($divisions as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->division_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                            onclick="document.getElementById('modal-create').classList.add('hidden')"
                            class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary">
                        <i class="ti ti-device-floppy text-sm"></i> Save Division
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Edit Division</h2>
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
            </div>
            <form id="edit-form" method="POST" class="px-5 py-4 space-y-3">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="{{ $lbl }}">DIVISION NAME</label>
                        <input type="text" name="division_name" id="edit-name" class="{{ $inp }}" required />
                    </div>
                    <div>
                        <label class="{{ $lbl }}">SHORT NAME</label>
                        <input type="text" name="short_name" id="edit-short" class="{{ $inp }}" required />
                    </div>
                    <div>
                        <label class="{{ $lbl }}">DIVISION TYPE</label>
                        <select name="division_type_id" id="edit-type" class="{{ $inp }}" required>
                            @foreach($division_types as $type)
                                <option value="{{ $type->id }}">{{ $type->division_type_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="{{ $lbl }}">PARENT DIVISION</label>
                        <select name="parent_division" id="edit-parent" class="{{ $inp }}">
                            <option value="">— None (Top-level) —</option>
                            @foreach($divisions as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->division_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                            onclick="document.getElementById('modal-edit').classList.add('hidden')"
                            class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary">
                        <i class="ti ti-device-floppy text-sm"></i> Update Division
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEdit(id, name, shortName, typeId, parentId) {
            document.getElementById('edit-name').value  = name;
            document.getElementById('edit-short').value = shortName;
            document.getElementById('edit-type').value  = typeId;
            document.getElementById('edit-parent').value = parentId || '';
            document.getElementById('edit-form').action = `/admin/divisions/${id}/update`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }
        @if($errors->any())
            document.getElementById('modal-create').classList.remove('hidden');
        @endif
    </script>

</x-settings-page>