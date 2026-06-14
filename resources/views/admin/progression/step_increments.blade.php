<x-progression-page title="Progression" subtitle="Staff progression management" active-tab="step_increments">

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
            <i class="ti ti-step-into text-gray-400"></i> Step Increment Records
        </h2>
        <button onclick="document.getElementById('modal-add').classList.remove('hidden')" class="btn-wf-primary btn-wf-sm">
            <i class="ti ti-plus text-xs"></i> Record Increment
        </button>
    </div>

    <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-funaab-50 flex items-center justify-center"><i class="ti ti-step-into text-funaab-700 text-sm"></i></div>
                TOTAL INCREMENTS
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $total }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-gold-50 flex items-center justify-center"><i class="ti ti-calendar text-gold-600 text-sm"></i></div>
                THIS YEAR
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $thisYear }}</div>
        </div>
    </div>

    <form method="GET" class="flex gap-2 mb-4">
        <div class="relative flex-1">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by staff name or ID…"
                   class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-funaab-500 bg-white" />
        </div>
        <button type="submit" class="btn-wf-primary"><i class="ti ti-filter text-sm"></i> Filter</button>
        @if(request('search'))<a href="{{ route('admin.step_increments.index') }}" class="btn-wf-ghost">Clear</a>@endif
    </form>

    <table class="wf-table">
        <thead>
            <tr><th>STAFF ID</th><th>NAME</th><th>DESIGNATION</th><th>NEW STEP</th><th>EFFECTIVE DATE</th><th>REMARK</th><th class="text-right">ACTIONS</th></tr>
        </thead>
        <tbody>
            @forelse($stepIncrements as $si)
            <tr>
                <td class="col-mono">{{ $si->staff?->staff_no ?? '—' }}</td>
                <td class="col-name">{{ $si->staff?->surname }}, {{ $si->staff?->firstname }}</td>
                <td>{{ $si->designation?->cadreRank?->cadre_rank_name ?? '—' }}</td>
                <td><span class="badge badge-info">Step {{ $si->salaryStep?->step ?? '—' }}</span></td>
                <td>{{ $si->effective_date?->format('d M Y') ?? '—' }}</td>
                <td class="text-gray-400 text-xs max-w-[150px] truncate">{{ $si->remark ?? '—' }}</td>
                <td>
                    <div class="flex items-center justify-end gap-1">
                        <button onclick="openEdit({{ $si->id }}, {{ $si->staff_id }}, {{ $si->designation_id }}, {{ $si->salary_step_id }}, '{{ $si->effective_date?->format('Y-m-d') }}', '{{ addslashes($si->remark ?? '') }}')"
                                class="btn-wf-ghost btn-wf-sm"><i class="ti ti-edit text-xs"></i></button>
                        <form action="{{ route('admin.step_increments.destroy', $si) }}" method="POST" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn-wf-danger btn-wf-sm"><i class="ti ti-trash text-xs"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-10 text-gray-400"><i class="ti ti-step-into text-3xl block mb-2"></i>No step increment records yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($stepIncrements->hasPages())
        <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs text-gray-400">Showing {{ $stepIncrements->firstItem() }}–{{ $stepIncrements->lastItem() }} of {{ $stepIncrements->total() }}</p>
            {{ $stepIncrements->links() }}
        </div>
    @endif

    @php $inp = 'w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50'; $lbl = 'block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5'; @endphp

    @foreach(['modal-add' => ['title' => 'Record Step Increment', 'action' => route('admin.step_increments.store'), 'method' => 'POST'],
              'modal-edit'=> ['title' => 'Edit Step Increment', 'action' => '', 'method' => 'PUT']] as $modalId => $cfg)
    <div id="{{ $modalId }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">{{ $cfg['title'] }}</h2>
                <button onclick="document.getElementById('{{ $modalId }}').classList.add('hidden')" class="text-gray-400"><i class="ti ti-x"></i></button>
            </div>
            <form id="{{ $modalId }}-form" action="{{ $cfg['action'] }}" method="POST" class="px-5 py-4 space-y-3">
                @csrf @if($cfg['method'] === 'PUT') @method('PUT') @endif
                <div>
                    <label class="{{ $lbl }}">STAFF MEMBER <span class="text-red-500">*</span></label>
                    <select name="staff_id" id="{{ $modalId }}-staff" class="{{ $inp }}" required>
                        <option value="">— Select Staff —</option>
                        @foreach($staff as $s)
                            <option value="{{ $s->id }}">{{ $s->staff_no }} — {{ $s->surname }}, {{ $s->firstname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="{{ $lbl }}">DESIGNATION <span class="text-red-500">*</span></label>
                        <select name="designation_id" id="{{ $modalId }}-des" class="{{ $inp }}" required>
                            <option value="">— Select —</option>
                            @foreach($designations as $d)
                                <option value="{{ $d->id }}">{{ $d->cadreRank?->cadre_rank_name ?? '#'.$d->id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $lbl }}">NEW STEP <span class="text-red-500">*</span></label>
                        <select name="salary_step_id" id="{{ $modalId }}-step" class="{{ $inp }}" required>
                            <option value="">— Select —</option>
                            @foreach($salarySteps as $step)
                                <option value="{{ $step->id }}">Step {{ $step->step }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $lbl }}">EFFECTIVE DATE <span class="text-red-500">*</span></label>
                        <input type="date" name="effective_date" id="{{ $modalId }}-date" class="{{ $inp }}" required />
                    </div>
                    <div>
                        <label class="{{ $lbl }}">REMARK</label>
                        <input type="text" name="remark" id="{{ $modalId }}-remark" class="{{ $inp }}" />
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('{{ $modalId }}').classList.add('hidden')" class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary"><i class="ti ti-device-floppy text-sm"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    <script>
        function openEdit(id, staffId, desId, stepId, date, remark) {
            document.getElementById('modal-edit-staff').value  = staffId;
            document.getElementById('modal-edit-des').value    = desId;
            document.getElementById('modal-edit-step').value   = stepId;
            document.getElementById('modal-edit-date').value   = date;
            document.getElementById('modal-edit-remark').value = remark;
            document.getElementById('modal-edit-form').action  = `/admin/step_increments/${id}`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }
    </script>

</x-progression-page>
