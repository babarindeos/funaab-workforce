<x-admin-layout title="APER HODs">

    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">APER Heads of Department</h1>
            <p class="pg-sub">Assign HODs responsible for evaluating staff APER submissions</p>
        </div>
        <button onclick="document.getElementById('modal-add').classList.remove('hidden')"
                class="btn-wf-primary">
            <i class="ti ti-plus text-sm"></i> Add HOD
        </button>
    </div>

     

    {{-- Flash --}}
    @if(session('success'))
        <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-xs font-semibold px-4 py-3 rounded-lg mb-4">
            <i class="ti ti-circle-check text-base"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Search --}}
    <div class="wf-card mb-4">
        <form method="GET" action="{{ route('admin.aper.hods.index') }}" class="flex gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                
                <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by name…"
                   class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg
                          focus:outline-none focus:border-funaab-500 bg-white" />
            </div>
            <button type="submit" class="btn-wf-ghost"><i class="ti ti-filter text-sm"></i> Filter</button>
            @if(request('search'))
                <a href="{{ route('admin.aper.hods.index') }}" class="btn-wf-ghost">Clear</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="wf-card">
        <div class="flex items-center justify-between mb-3">
            <div class="wf-card-title mb-0">
                <i class="ti ti-user-star text-gray-400"></i> HOD Records
            </div>
            <span class="text-[10.5px] text-gray-400 font-semibold">{{ $hods->total() }} record(s)</span>
        </div>

        <table class="wf-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>STAFF NAME</th>
                    <th>STAFF ID</th>
                    <th>DEPARTMENT</th>
                    <th>STAFF CATEGORY</th>
                    <th>STATUS</th>
                    <th class="text-right">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hods as $hod)
                    <tr>
                        <td class="text-gray-400 text-[11px]">{{ $hods->firstItem() + $loop->index }}</td>
                        <td class="col-name">{{ $hod->staff?->surname }}, {{ $hod->staff?->firstname }}</td>
                        <td class="col-mono">{{ $hod->staff?->staff_no ?? '—' }}</td>
                        <td>{{ $hod->department?->department_name ?? '—' }}</td>
                        <td>{{ $hod->staffCategory?->category ?? '—' }}</td>
                        <td>
                            @if($hod->is_current)
                                <span class="badge badge-active">Current</span>
                            @else
                                <span class="badge badge-inactive">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-1">
                                <button onclick="openEdit('{{ $hod->uuid }}', {{ $hod->department_id }}, {{ $hod->staff_category_id }}, {{ $hod->staff_id }}, {{ $hod->is_current ? 1 : 0 }})"
                                        class="btn-wf-ghost btn-wf-sm">
                                    <i class="ti ti-edit text-xs"></i> Edit
                                </button>
                                <form action="{{ route('admin.aper.hods.destroy', $hod->uuid) }}"
                                      method="POST" onsubmit="return confirm('Delete this HOD record?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-wf-danger btn-wf-sm"><i class="ti ti-trash text-xs"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 text-gray-400">
                            <i class="ti ti-user-star text-3xl block mb-2"></i>
                            No HOD records yet. Add one to get started.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($hods->hasPages())
            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                <p class="text-[11px] text-gray-400">
                    Showing {{ $hods->firstItem() }}–{{ $hods->lastItem() }} of {{ $hods->total() }}
                </p>
                {{ $hods->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    @php
        $inp = 'w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50';
        $lbl = 'block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5';
    @endphp

    {{-- ADD MODAL --}}
    <div id="modal-add" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Add HOD</h2>
                <button onclick="document.getElementById('modal-add').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
            </div>
            <form action="{{ route('admin.aper.hods.store') }}" method="POST" class="px-5 py-4 space-y-3">
                @csrf
                <div>
                    <label class="{{ $lbl }}">DEPARTMENT <span class="text-red-500">*</span></label>
                    <select name="department_id" class="{{ $inp }}" required>
                        <option value="">— Select Department —</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $lbl }}">STAFF CATEGORY <span class="text-red-500">*</span></label>
                    <select name="staff_category_id" class="{{ $inp }}" required>
                        <option value="">— Select Category —</option>
                        @foreach($staffCategories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->category }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $lbl }}">STAFF MEMBER <span class="text-red-500">*</span></label>
                    <select name="staff_id" class="{{ $inp }}" required>
                        <option value="">— Select Staff —</option>
                        @foreach($staff as $s)
                            <option value="{{ $s->id }}">{{ $s->staff_no }} — {{ $s->surname }}, {{ $s->firstname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_current" value="1" id="add-is-current"
                           class="rounded border-gray-300 text-funaab-600" />
                    <label for="add-is-current" class="text-sm text-gray-700 font-semibold cursor-pointer">
                        Set as Current HOD
                    </label>
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
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Edit HOD</h2>
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
            </div>
            <form id="edit-form" method="POST" class="px-5 py-4 space-y-3">
                @csrf @method('PUT')
                <div>
                    <label class="{{ $lbl }}">DEPARTMENT <span class="text-red-500">*</span></label>
                    <select name="department_id" id="e-dept" class="{{ $inp }}" required>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>                    
                    <label class="{{ $lbl }}">STAFF CATEGORY <span class="text-red-500">*</span></label>
                    <select name="staff_category_id" id="e-cat" class="{{ $inp }}" required>
                        <option value="">— Select Category —</option>   {{-- ADD THIS LINE --}}
                        @foreach($staffCategories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->category }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $lbl }}">STAFF MEMBER <span class="text-red-500">*</span></label>
                    <select name="staff_id" id="e-staff" class="{{ $inp }}" required>
                        @foreach($staff as $s)
                            <option value="{{ $s->id }}">{{ $s->staff_no }} — {{ $s->surname }}, {{ $s->firstname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_current" value="1" id="e-is-current"
                           class="rounded border-gray-300 text-funaab-600" />
                    <label for="e-is-current" class="text-sm text-gray-700 font-semibold cursor-pointer">
                        Set as Current HOD
                    </label>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')" class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary"><i class="ti ti-device-floppy text-sm"></i> Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEdit(uuid, deptId, catId, staffId, isCurrent) {
            document.getElementById('e-dept').value        = deptId;
            document.getElementById('e-cat').value         = catId;
            document.getElementById('e-staff').value       = staffId;
            document.getElementById('e-is-current').checked = isCurrent === 1;
            document.getElementById('edit-form').action    = `/admin/aper/hods/${uuid}`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }
        @if($errors->any())
            document.getElementById('modal-add').classList.remove('hidden');
        @endif
    </script>

</x-admin-layout>
