<x-admin-layout title="Nominal Roll Preview">
    @php
        $c = $s['counts'];
        $applied = $import->status === 'applied';
        $disengage = collect($s['disengaged'] ?? []);
        $modeLabels = ['apply' => 'Will be recorded', 'same' => 'Already recorded', 'conflict' => 'Also on the serving roll — not changed',
                       'skip' => 'Not a disengagement (sabbatical, contract …) — ignored', 'no_date' => 'No date — not changed'];
    @endphp

    <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
        <div>
            <h1 class="pg-title">{{ $applied ? 'Nominal Roll — Applied' : 'Nominal Roll — Preview' }}</h1>
            <p class="pg-sub">{{ $import->file_name }} · uploaded {{ $import->created_at->format('d M Y, g:i A') }}
                @if($applied) · applied {{ $import->applied_at->format('d M Y, g:i A') }} by {{ $import->applier?->surname }} {{ $import->applier?->firstname }}@endif
            </p>
        </div>
        <a href="{{ route('admin.nominal_roll.index') }}" class="btn-wf-ghost"><i class="ti ti-arrow-left text-sm"></i> Back</a>
    </div>

    @if(session('applied'))<div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3">{{ session('applied') }}
        <a href="{{ route('admin.login_mails.index') }}" class="font-bold underline ml-1">Go to Login Mails →</a></div>@endif

    <div class="grid grid-cols-2 md:grid-cols-6 gap-3 mb-4">
        @foreach([
            ['Rows on roll', $c['rows'], 'ti-list', 'text-gray-800'],
            [$applied ? 'Staff added' : 'New staff', $c['new'], 'ti-user-plus', 'text-funaab-700'],
            [$applied ? 'Staff updated' : 'Will be updated', $c['update'], 'ti-edit', 'text-blue-700'],
            ['Unchanged', $c['same'], 'ti-check', 'text-gray-500'],
            ['Problems (skipped)', $c['problems'], 'ti-alert-triangle', $c['problems'] ? 'text-red-600' : 'text-gray-500'],
            ['Disengagements', $c['disengaged'], 'ti-door-exit', 'text-amber-700'],
        ] as [$label, $n, $icon, $colour])
            <div class="stat-card">
                <div class="flex items-center gap-2 text-[10px] font-bold tracking-widest text-gray-400 mb-2 uppercase"><i class="ti {{ $icon }} text-sm"></i> {{ $label }}</div>
                <div class="text-2xl font-bold {{ $colour }}">{{ number_format($n) }}</div>
            </div>
        @endforeach
    </div>

    @unless($applied)
        <div class="wf-card mb-4 border-2 border-funaab-200">
            <div class="wf-card-title"><i class="ti ti-player-play text-funaab-600"></i> Apply this roll</div>
            <form method="POST" action="{{ route('admin.nominal_roll.apply', $import) }}"
                  onsubmit="if(!confirm('Apply the nominal roll now? This adds {{ $c['new'] }} staff and cannot be undone automatically. No e-mails will be sent.')) return false; this.querySelector('button').disabled=true; this.querySelector('button').innerHTML='Applying — please wait…';">
                @csrf
                <label class="flex items-start gap-2 text-sm text-gray-700 mb-3">
                    <input type="checkbox" name="update_existing" value="1" checked class="mt-1">
                    <span>Also update the <b>{{ number_format($c['update']) }}</b> staff already in the system with the roll's details (listed under "Changes to existing staff" below).
                        Untick to only add new staff.</span>
                </label>
                <p class="text-[11.5px] text-gray-500 mb-3">Rows under "Problems" are skipped — correct them in the workbook and upload again. Login mails are <b>not</b> sent; send them from Login Mails when you are ready.</p>
                <button class="btn-wf-primary"><i class="ti ti-check text-sm"></i> Apply nominal roll</button>
            </form>
        </div>
    @endunless

    @if($s['problems'])
        <div class="wf-card mb-4">
            <div class="wf-card-title text-red-700"><i class="ti ti-alert-triangle"></i> Problems — these rows are skipped ({{ count($s['problems']) }})</div>
            <table class="wf-table">
                <thead><tr><th>ROW</th><th>STAFF NO.</th><th>NAME</th><th>PROBLEM</th></tr></thead>
                <tbody>
                @foreach($s['problems'] as $p)
                    <tr><td class="text-gray-400">{{ $p['line'] }}</td><td class="col-mono">{{ $p['staff_no'] ?: '—' }}</td><td>{{ $p['name'] }}</td><td class="text-red-600 text-xs">{{ implode('; ', $p['issues']) }}</td></tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($s['warnings'])
        <div class="wf-card mb-4">
            <div class="wf-card-title text-amber-700"><i class="ti ti-info-circle"></i> Imported, but please check</div>
            <p class="text-[11.5px] text-gray-500 mb-3">These rows are imported; the item mentioned is left blank so Registry can fill it in on the staff record.</p>
            @foreach($s['warnings'] as $warning => $nos)
                <details class="border-b border-gray-100 py-2">
                    <summary class="text-sm cursor-pointer"><b>{{ count($nos) }}</b> — {{ $warning }}</summary>
                    <p class="text-xs text-gray-500 mt-2 col-mono leading-6">{{ implode(', ', $nos) }}</p>
                </details>
            @endforeach
        </div>
    @endif

    @if(!empty($s['added_lookups']))
        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-list-check text-gray-400"></i> {{ $applied ? 'Added to the lists' : 'Will be added to the lists' }}</div>
            @foreach(['title' => 'Staff titles', 'degree' => 'Degrees'] as $k => $label)
                @if(!empty($s['added_lookups'][$k]))
                    <p class="text-sm mb-1"><span class="text-gray-500">{{ $label }}:</span> {{ implode(', ', $s['added_lookups'][$k]) }}</p>
                @endif
            @endforeach
        </div>
    @endif

    @if($s['update'])
        <details class="wf-card mb-4" {{ count($s['update']) <= 60 ? 'open' : '' }}>
            <summary class="wf-card-title cursor-pointer"><i class="ti ti-edit text-gray-400"></i> Changes to existing staff ({{ count($s['update']) }})
                @if($applied && empty($s['update_existing']))<span class="badge badge-inactive ml-2">Not applied — "update existing" was unticked</span>@endif</summary>
            <div class="overflow-x-auto">
            <table class="wf-table">
                <thead><tr><th>STAFF</th><th>FIELD</th><th>NOW</th><th>FROM THE ROLL</th></tr></thead>
                <tbody>
                @foreach($s['update'] as $u)
                    @foreach($u['changes'] as $field => [$old, $new])
                        <tr>
                            @if($loop->first)<td rowspan="{{ count($u['changes']) }}" class="col-name align-top">{{ $u['name'] }}<div class="col-mono text-[11px] text-gray-400">{{ $u['staff_no'] }}</div></td>@endif
                            <td class="text-xs text-gray-500">{{ $labels[$field] ?? $field }}</td>
                            <td class="text-xs text-gray-400">{{ $old }}</td>
                            <td class="text-xs font-semibold">{{ $new }}</td>
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
            </div>
        </details>
    @endif

    @if($s['new'])
        <details class="wf-card mb-4">
            <summary class="wf-card-title cursor-pointer"><i class="ti ti-user-plus text-gray-400"></i> New staff ({{ count($s['new']) }}) — click to list</summary>
            <div class="overflow-x-auto max-h-[480px] overflow-y-auto">
            <table class="wf-table">
                <thead><tr><th>STAFF NO.</th><th>NAME</th><th>COLLEGE / UNIT</th><th>DEPT.</th><th>E-MAIL</th></tr></thead>
                <tbody>
                @foreach($s['new'] as $n)
                    <tr><td class="col-mono">{{ $n['staff_no'] }}</td><td>{{ $n['name'] }}</td><td class="text-xs">{{ $n['college'] }}</td><td class="text-xs">{{ $n['dept'] }}</td>
                        <td>@if($n['has_email'])<i class="ti ti-check text-green-600"></i>@else<span class="text-xs text-amber-600">none</span>@endif</td></tr>
                @endforeach
                </tbody>
            </table>
            </div>
        </details>
    @endif

    @if($disengage->isNotEmpty())
        <details class="wf-card mb-4" {{ $c['disengaged'] ? 'open' : '' }}>
            <summary class="wf-card-title cursor-pointer"><i class="ti ti-door-exit text-gray-400"></i> Disengagement sheets — staff in the system ({{ $disengage->count() }})</summary>
            <table class="wf-table">
                <thead><tr><th>STAFF</th><th>SHEET</th><th>MODE</th><th>DATE</th><th>STATUS</th><th>RESULT</th></tr></thead>
                <tbody>
                @foreach($disengage->sortBy(fn ($d) => $d['action'] === 'apply' ? 0 : 1) as $d)
                    <tr><td>{{ $d['name'] }}<div class="col-mono text-[11px] text-gray-400">{{ $d['staff_no'] }}</div></td><td class="text-xs">{{ $d['sheet'] }}</td>
                        <td class="text-xs">{{ $d['mode'] }}</td><td class="text-xs">{{ $d['date'] ? \Carbon\Carbon::parse($d['date'])->format('d M Y') : '—' }}</td>
                        <td class="text-xs">{{ $d['status'] ?? '—' }}</td>
                        <td class="text-xs {{ $d['action'] === 'apply' ? 'font-bold text-amber-700' : 'text-gray-400' }}">{{ $modeLabels[$d['action']] ?? $d['action'] }}</td></tr>
                @endforeach
                </tbody>
            </table>
        </details>
    @endif
</x-admin-layout>
