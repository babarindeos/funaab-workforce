<x-settings-page title="Departments" subtitle="Academic and administrative departments across all divisions"
    back-route="admin.settings.index"
    add-label="Add Department"
    add-onclick="document.getElementById('modal-create').classList.remove('hidden')">

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-3 mb-4">
        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-funaab-50 flex items-center justify-center">
                    <i class="ti ti-building text-funaab-700 text-sm"></i>
                </div>
                TOTAL DEPARTMENTS
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $total }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-blue-50 flex items-center justify-center">
                    <i class="ti ti-hierarchy-2 text-blue-600 text-sm"></i>
                </div>
                DIVISIONS
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $divisions->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-gold-50 flex items-center justify-center">
                    <i class="ti ti-users text-gold-600 text-sm"></i>
                </div>
                TOTAL STAFF
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $departments->sum('employments_count') }}</div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex gap-2 mb-4 flex-wrap">
        <div class="relative flex-1 min-w-[200px]">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search department or short name…"
                   class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg
                          focus:outline-none focus:border-funaab-500 bg-white" />
        </div>
        <select name="division_id"
                class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none
                       focus:border-funaab-500 bg-white">
            <option value="">All Divisions</option>
            @foreach($divisions as $div)
                <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>
                    {{ $div->short_name }} — {{ $div->division_name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn-wf-primary">
            <i class="ti ti-filter text-sm"></i> Filter
        </button>
        @if(request()->hasAny(['search','division_id']))
            <a href="{{ route('admin.departments.index') }}" class="btn-wf-ghost">Clear</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="wf-card">
        <table class="wf-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>DEPARTMENT NAME</th>
                    <th>SHORT NAME</th>
                    <th>DIVISION</th>
                    <th>PARENT DEPT.</th>
                    <th>STAFF</th>
                    <th>CREATED</th>
                    <th class="text-right">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departments as $dept)
                <tr>
                    <td class="text-gray-400">{{ $departments->firstItem() + $loop->index }}</td>
                    <td class="col-name">{{ $dept->department_name }}</td>
                    <td>
                        <span class="badge badge-info font-mono text-[10px]">{{ $dept->short_name }}</span>
                    </td>
                    <td>
                        <div class="text-xs">
                            <div class="font-bold text-gray-700">{{ $dept->division?->short_name ?? '—' }}</div>
                            <div class="text-gray-400">{{ $dept->division?->division_name ?? '' }}</div>
                        </div>
                    </td>
                    <td class="text-gray-500 text-xs">{{ $dept->parent?->department_name ?? '—' }}</td>
                    <td>
                        <span class="badge badge-active">{{ $dept->employments_count }}</span>
                    </td>
                    <td class="text-gray-400 text-xs">{{ $dept->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="flex items-center justify-end gap-1">
                            <button onclick="openEdit(
                                        {{ $dept->id }},
                                        '{{ addslashes($dept->department_name) }}',
                                        '{{ addslashes($dept->short_name) }}',
                                        {{ $dept->division_id }},
                                        {{ $dept->parent_department ?? 'null' }})"
                                    class="btn-wf-ghost btn-wf-sm">
                                <i class="ti ti-edit text-xs"></i> Edit
                            </button>
                            <form action="{{ route('admin.departments.delete', $dept) }}" method="POST"
                                  onsubmit="return confirm('Delete {{ addslashes($dept->department_name) }}? This cannot be undone.')">
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
                        <i class="ti ti-building text-3xl block mb-2"></i>
                        No departments found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($departments->hasPages())
            <div class="p-3 border-t border-gray-100 flex items-center justify-between">
                <p class="text-xs text-gray-400">
                    Showing {{ $departments->firstItem() }}–{{ $departments->lastItem() }}
                    of {{ $departments->total() }} departments
                </p>
                {{ $departments->links() }}
            </div>
        @endif
    </div>

    {{-- CREATE MODAL --}}
    <div id="modal-create" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Add Department</h2>
                <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
            </div>
            <form action="{{ route('admin.departments.store') }}" method="POST" class="px-5 py-4 space-y-3">
                @csrf
                @php $inp = 'w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50';
                     $lbl = 'block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5'; @endphp

                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="{{ $lbl }}">DEPARTMENT NAME <span class="text-red-500">*</span></label>
                        <input type="text" name="department_name" value="{{ old('department_name') }}"
                               placeholder="e.g. Crop Production & Protection"
                               class="{{ $inp }}" required />
                        @error('department_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="{{ $lbl }}">SHORT NAME <span class="text-red-500">*</span></label>
                        <input type="text" name="short_name" value="{{ old('short_name') }}"
                               placeholder="e.g. CROP, REGISTRY"
                               class="{{ $inp }}" required />
                        @error('short_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="{{ $lbl }}">DIVISION <span class="text-red-500">*</span></label>
                        <select name="division_id" class="{{ $inp }}" required>
                            <option value="">— Select Division —</option>
                            @foreach($divisions as $div)
                                <option value="{{ $div->id }}" {{ old('division_id') == $div->id ? 'selected' : '' }}>
                                    {{ $div->short_name }} — {{ $div->division_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('division_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="col-span-2">
                        <label class="{{ $lbl }}">PARENT DEPARTMENT <span class="text-gray-400 font-normal">(optional)</span></label>
                        <select name="parent_department" class="{{ $inp }}">
                            <option value="">— None (Top-level) —</option>
                            @foreach($departments as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->department_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                            onclick="document.getElementById('modal-create').classList.add('hidden')"
                            class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary">
                        <i class="ti ti-device-floppy text-sm"></i> Save Department
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Edit Department</h2>
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
            </div>
            <form id="edit-form" method="POST" class="px-5 py-4 space-y-3">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="{{ $lbl }}">DEPARTMENT NAME</label>
                        <input type="text" name="department_name" id="edit-name" class="{{ $inp }}" required />
                    </div>
                    <div>
                        <label class="{{ $lbl }}">SHORT NAME</label>
                        <input type="text" name="short_name" id="edit-short" class="{{ $inp }}" required />
                    </div>
                    <div>
                        <label class="{{ $lbl }}">DIVISION</label>
                        <select name="division_id" id="edit-division" class="{{ $inp }}" required>
                            @foreach($divisions as $div)
                                <option value="{{ $div->id }}">
                                    {{ $div->short_name }} — {{ $div->division_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="{{ $lbl }}">PARENT DEPARTMENT</label>
                        <select name="parent_department" id="edit-parent" class="{{ $inp }}">
                            <option value="">— None (Top-level) —</option>
                            @foreach($departments as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->department_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                            onclick="document.getElementById('modal-edit').classList.add('hidden')"
                            class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary">
                        <i class="ti ti-device-floppy text-sm"></i> Update Department
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEdit(id, name, shortName, divisionId, parentId) {
            document.getElementById('edit-name').value     = name;
            document.getElementById('edit-short').value    = shortName;
            document.getElementById('edit-division').value = divisionId;
            document.getElementById('edit-parent').value   = parentId || '';
            document.getElementById('edit-form').action    = `/admin/departments/${id}/update`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }
        @if($errors->any())
            document.getElementById('modal-create').classList.remove('hidden');
        @endif
    </script>

</x-settings-page>