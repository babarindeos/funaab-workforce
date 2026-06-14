<x-progression-page title="Progression" subtitle="Staff progression management" active-tab="promotions">

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
            <i class="ti ti-award text-gray-400"></i> Promotion Records
        </h2>
        <button onclick="document.getElementById('modal-add').classList.remove('hidden')"
                class="btn-wf-primary btn-wf-sm">
            <i class="ti ti-plus text-xs"></i> Record Promotion
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-3 mb-4">
        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-funaab-50 flex items-center justify-center"><i class="ti ti-award text-funaab-700 text-sm"></i></div>
                TOTAL PROMOTIONS
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $total }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-gold-50 flex items-center justify-center"><i class="ti ti-calendar text-gold-600 text-sm"></i></div>
                THIS YEAR
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $thisYear }}</div>
            <div class="text-[10.5px] text-gray-400 mt-1">{{ now()->year }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-blue-50 flex items-center justify-center"><i class="ti ti-users text-blue-600 text-sm"></i></div>
                UNIQUE STAFF
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $promotions->total() }}</div>
        </div>
    </div>

    {{-- Search --}}
    <form method="GET" class="flex gap-2 mb-4">
        <div class="relative flex-1">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by staff name or ID…"
                   class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg
                          focus:outline-none focus:border-funaab-500 bg-white" />
        </div>
        <button type="submit" class="btn-wf-primary"><i class="ti ti-filter text-sm"></i> Filter</button>
        @if(request('search'))
            <a href="{{ route('admin.promotions.index') }}" class="btn-wf-ghost">Clear</a>
        @endif
    </form>

    {{-- Table --}}
    <table class="wf-table">
        <thead>
            <tr><th>STAFF ID</th><th>NAME</th><th>DESIGNATION</th><th>SALARY STEP</th><th>EFFECTIVE DATE</th><th>REMARK</th><th class="text-right">ACTIONS</th></tr>
        </thead>
        <tbody>
            @forelse($promotions as $p)
            <tr>
                <td class="col-mono">{{ $p->staff?->staff_no ?? '—' }}</td>
                <td class="col-name">{{ $p->staff?->surname }}, {{ $p->staff?->firstname }}</td>
                <td>{{ $p->designation?->cadreRank?->cadre_rank_name ?? '—' }}</td>
                <td><span class="badge badge-info">Step {{ $p->salaryStep?->step ?? '—' }}</span></td>
                <td>{{ $p->effective_date?->format('d M Y') ?? '—' }}</td>
                <td class="text-gray-400 text-xs max-w-[150px] truncate">{{ $p->remark ?? '—' }}</td>
                <td>
                    <div class="flex items-center justify-end gap-1">
                        <button onclick="openEdit(
                                    {{ $p->id }},
                                    {{ $p->staff_id }},
                                    {{ $p->designation_id }},
                                    {{ $p->salary_step_id }},
                                    '{{ $p->effective_date?->format('Y-m-d') }}',
                                    '{{ addslashes($p->remark ?? '') }}')"
                                class="btn-wf-ghost btn-wf-sm"><i class="ti ti-edit text-xs"></i></button>
                        <form action="{{ route('admin.promotions.destroy', $p) }}" method="POST"
                              onsubmit="return confirm('Delete this record?')">
                            @csrf @method('DELETE')
                            <button class="btn-wf-danger btn-wf-sm"><i class="ti ti-trash text-xs"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-10 text-gray-400"><i class="ti ti-award text-3xl block mb-2"></i>No promotion records yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($promotions->hasPages())
        <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs text-gray-400">Showing {{ $promotions->firstItem() }}–{{ $promotions->lastItem() }} of {{ $promotions->total() }}</p>
            {{ $promotions->links() }}
        </div>
    @endif

    @php
        $inp = 'w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50';
        $lbl = 'block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5';
    @endphp

    {{-- ADD MODAL --}}
    <div id="modal-add" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Record Promotion</h2>
                <button onclick="document.getElementById('modal-add').classList.add('hidden')" class="text-gray-400"><i class="ti ti-x"></i></button>
            </div>
            <form action="{{ route('admin.promotions.store') }}" method="POST" class="px-5 py-4 space-y-3">
                @csrf
                <div>
                    <label class="{{ $lbl }}">STAFF MEMBER <span class="text-red-500">*</span></label>
                    <select name="staff_id" class="{{ $inp }}" required>
                        <option value="">— Select Staff —</option>
                        @foreach($staff as $s)
                            <option value="{{ $s->id }}">{{ $s->staff_no }} — {{ $s->surname }}, {{ $s->firstname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="{{ $lbl }}">NEW DESIGNATION <span class="text-red-500">*</span></label>
                        <select name="designation_id" class="{{ $inp }}" required>
                            <option value="">— Select —</option>
                            @foreach($designations as $d)
                                <option value="{{ $d->id }}">{{ $d->cadreRank?->cadre_rank_name ?? 'Designation #'.$d->id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $lbl }}">SALARY STEP <span class="text-red-500">*</span></label>
                        <select name="salary_step_id" class="{{ $inp }}" required>
                            <option value="">— Select —</option>
                            @foreach($salarySteps as $step)
                                <option value="{{ $step->id }}">Step {{ $step->step }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $lbl }}">EFFECTIVE DATE <span class="text-red-500">*</span></label>
                        <input type="date" name="effective_date" class="{{ $inp }}" required />
                    </div>
                    <div>
                        <label class="{{ $lbl }}">REMARK</label>
                        <input type="text" name="remark" class="{{ $inp }}" placeholder="Optional notes" />
                    </div>
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
                <h2 class="font-bold text-gray-800 text-sm">Edit Promotion</h2>
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-gray-400"><i class="ti ti-x"></i></button>
            </div>
            <form id="edit-form" method="POST" class="px-5 py-4 space-y-3">
                @csrf @method('PUT')
                <div>
                    <label class="{{ $lbl }}">STAFF MEMBER</label>
                    <select name="staff_id" id="e-staff" class="{{ $inp }}" required>
                        @foreach($staff as $s)
                            <option value="{{ $s->id }}">{{ $s->staff_no }} — {{ $s->surname }}, {{ $s->firstname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="{{ $lbl }}">DESIGNATION</label>
                        <select name="designation_id" id="e-des" class="{{ $inp }}" required>
                            @foreach($designations as $d)
                                <option value="{{ $d->id }}">{{ $d->cadreRank?->cadre_rank_name ?? 'Designation #'.$d->id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $lbl }}">SALARY STEP</label>
                        <select name="salary_step_id" id="e-step" class="{{ $inp }}" required>
                            @foreach($salarySteps as $step)
                                <option value="{{ $step->id }}">Step {{ $step->step }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $lbl }}">EFFECTIVE DATE</label>
                        <input type="date" name="effective_date" id="e-date" class="{{ $inp }}" required />
                    </div>
                    <div>
                        <label class="{{ $lbl }}">REMARK</label>
                        <input type="text" name="remark" id="e-remark" class="{{ $inp }}" />
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')" class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary"><i class="ti ti-device-floppy text-sm"></i> Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEdit(id, staffId, desId, stepId, date, remark) {
            document.getElementById('e-staff').value  = staffId;
            document.getElementById('e-des').value    = desId;
            document.getElementById('e-step').value   = stepId;
            document.getElementById('e-date').value   = date;
            document.getElementById('e-remark').value = remark;
            document.getElementById('edit-form').action = `/admin/promotions/${id}`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }
    </script>

</x-progression-page>
