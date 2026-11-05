<x-admin-layout title="Login Mails">
    @php
        $badge = ['not_sent' => 'badge-leave', 'sent' => 'badge-info', 'signed_in' => 'badge-active', 'no_email' => 'badge-inactive'];
        $scope = collect([
            ($f['division'] ?? null) ? $divisions->firstWhere('id', $f['division'])?->division_name : null,
            ($f['department'] ?? null) ? $departments->firstWhere('id', $f['department'])?->department_name : null,
            ($f['q'] ?? null) ? "matching \"{$f['q']}\"" : null,
        ])->filter()->implode(' · ');
        $bulkQuery = array_diff_key($f, ['status' => 1]);
    @endphp

    <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
        <div>
            <h1 class="pg-title">Login Mails</h1>
            <p class="pg-sub">Send staff their FUNAAB Workforce login. Nothing is sent until you click Send. The mail contains a "set your password" link, never a password.</p>
        </div>
        <a href="{{ route('admin.nominal_roll.index') }}" class="btn-wf-ghost"><i class="ti ti-file-spreadsheet text-sm"></i> Nominal Roll Import</a>
    </div>


    {{-- Status tabs with counts --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4">
        @foreach(['' => ['All serving staff', 'ti-users'], 'not_sent' => ['Never sent', 'ti-mail-off'], 'sent' => ['Sent', 'ti-mail-check'], 'signed_in' => ['Signed in', 'ti-login'], 'no_email' => ['No e-mail', 'ti-alert-circle']] as $key => [$label, $icon])
            <a href="{{ route('admin.login_mails.index', array_merge($f, ['status' => $key ?: null, 'page' => null])) }}"
               class="stat-card block hover:shadow-sm {{ ($f['status'] ?? '') === $key ? 'ring-2 ring-funaab-400' : '' }}">
                <div class="flex items-center gap-2 text-[10px] font-bold tracking-widest text-gray-400 mb-2 uppercase"><i class="ti {{ $icon }} text-sm"></i> {{ $label }}</div>
                <div class="text-2xl font-bold text-gray-800">{{ number_format($key === '' ? $counts['all'] : $counts[$key]) }}</div>
            </a>
        @endforeach
    </div>

    {{-- Bulk send --}}
    <div class="wf-card mb-4" id="bulk">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <div class="wf-card-title mb-1"><i class="ti ti-send text-funaab-600"></i> Bulk send to staff never sent a login mail</div>
                <p class="text-xs text-gray-500"><b id="bulk-count">{{ number_format($counts['not_sent']) }}</b> staff {{ $scope ? "in {$scope}" : 'across the University' }} have an e-mail address and have never been sent one.
                    Staff already sent (including DICTS) are skipped. Use the filters below to send to one division or department.</p>
            </div>
            <button type="button" id="bulk-start" class="btn-wf-primary" {{ $counts['not_sent'] ? '' : 'disabled' }}>
                <i class="ti ti-send text-sm"></i> Send to {{ number_format($counts['not_sent']) }}
            </button>
        </div>
        <div id="bulk-progress" class="hidden mt-4">
            <div class="h-2 bg-gray-100 rounded-full overflow-hidden"><div id="bulk-bar" class="h-full bg-funaab-500 rounded-full transition-all" style="width:0%"></div></div>
            <div class="flex justify-between items-center mt-2 text-xs">
                <span id="bulk-text" class="text-gray-600">Starting…</span>
                <button type="button" id="bulk-stop" class="btn-wf-ghost btn-wf-sm">Stop</button>
            </div>
            <ul id="bulk-errors" class="mt-2 text-xs text-red-600 space-y-0.5 max-h-48 overflow-y-auto"></ul>
        </div>
    </div>

    {{-- Filters --}}
    <div class="wf-card mb-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            @if(!empty($f['status']))<input type="hidden" name="status" value="{{ $f['status'] }}">@endif
            <div class="relative flex-1 min-w-[180px]">
                <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="q" value="{{ $f['q'] ?? '' }}" placeholder="Staff No. or name…" class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg bg-white">
            </div>
            <select name="division" class="wf-input" onchange="this.form.department && (this.form.department.value=''); this.form.submit()">
                <option value="">All divisions / colleges</option>
                @foreach($divisions as $d)<option value="{{ $d->id }}" @selected(($f['division'] ?? null) == $d->id)>{{ $d->division_name }}</option>@endforeach
            </select>
            <select name="department" class="wf-input">
                <option value="">All departments</option>
                @foreach($departments as $d)<option value="{{ $d->id }}" @selected(($f['department'] ?? null) == $d->id)>{{ $d->department_name }}</option>@endforeach
            </select>
            <button class="btn-wf-ghost"><i class="ti ti-filter text-sm"></i> Filter</button>
            @if($f)<a href="{{ route('admin.login_mails.index') }}" class="text-xs text-gray-500 hover:underline">Clear</a>@endif
        </form>
    </div>

    <div class="wf-card">
        <div class="overflow-x-auto">
        <table class="wf-table">
            <thead><tr><th>STAFF</th><th>DEPARTMENT</th><th>E-MAIL</th><th>LOGIN MAIL</th><th class="text-right">ACTION</th></tr></thead>
            <tbody>
            @forelse($staff as $s)
                @php $st = $mails->status($s); $email = $mails->emailFor($s); $u = $s->user; $fail = $failures->get($s->id); @endphp
                <tr>
                    <td class="col-name"><a href="{{ route('admin.staff.show', $s->id) }}" class="hover:underline">{{ $s->title?->title }} {{ $s->surname }}, {{ $s->firstname }}</a>
                        <div class="col-mono text-[11px] text-gray-400">{{ $s->staff_no }}</div></td>
                    <td class="text-xs">{{ $s->employment?->department?->department_name ?? '—' }}</td>
                    <td class="text-xs">{{ $email ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $badge[$st] }}">{{ $statuses[$st] }}</span>
                        @if($u?->login_mail_sent_at)
                            <div class="text-[10.5px] text-gray-400 mt-0.5">{{ $u->login_mail_sent_at->format('d M Y') }}{{ $u->login_mail_count > 1 ? " · sent {$u->login_mail_count}×" : '' }}</div>
                        @endif
                        @if($u?->last_login_at)<div class="text-[10.5px] text-gray-400">last signed in {{ $u->last_login_at->format('d M Y') }}</div>@endif
                        @if($fail && (!$u?->login_mail_sent_at || $fail->created_at > $u->login_mail_sent_at))
                            <div class="text-[10.5px] text-red-600 mt-0.5" title="{{ $fail->note }}">Last attempt failed: {{ \Illuminate\Support\Str::limit($fail->note, 60) }}</div>
                        @endif
                    </td>
                    <td class="text-right">
                        @if($email)
                            <form method="POST" action="{{ route('admin.login_mails.send', $s->id) }}"
                                  onsubmit="return confirm('{{ $u?->login_mail_sent_at ? 'Send the login mail again' : 'Send the login mail' }} to {{ $email }}?{{ $u?->login_mail_sent_at ? ' A new link replaces the old one.' : '' }}')">
                                @csrf
                                <button class="btn-wf-ghost btn-wf-sm"><i class="ti ti-send text-xs"></i> {{ $u?->login_mail_sent_at || $u?->last_login_at ? 'Resend' : 'Send' }}</button>
                            </form>
                        @else
                            <a href="{{ route('admin.staff.show', $s->id) }}#contact" class="text-[11px] text-amber-700 hover:underline">Add e-mail</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-10 text-gray-400">No staff match these filters.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
        <div class="mt-3">{{ $staff->links() }}</div>
    </div>

    <script>
    (function () {
        const start = document.getElementById('bulk-start');
        if (!start) return;
        const total = {{ (int) $counts['not_sent'] }};
        const query = @json($bulkQuery);
        const scope = @json($scope ?: 'across the University');
        const token = document.querySelector('meta[name="csrf-token"]')?.content || @json(csrf_token());
        let stop = false, sent = 0, failed = [];

        document.getElementById('bulk-stop').addEventListener('click', () => { stop = true; });

        start.addEventListener('click', async () => {
            if (!confirm(`Send the login mail to ${total.toLocaleString()} staff ${scope} who have never been sent one?\n\nMails go out in small batches through the mail server. Keep this page open until it finishes.`)) return;
            start.disabled = true;
            document.getElementById('bulk-progress').classList.remove('hidden');
            const bar = document.getElementById('bulk-bar'), text = document.getElementById('bulk-text'), errs = document.getElementById('bulk-errors');

            while (!stop) {
                let res;
                try {
                    const r = await fetch(@json(route('admin.login_mails.bulk')), {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                        body: JSON.stringify({ ...query, skip: failed.map(f => f.id) }),
                    });
                    if (!r.ok) throw new Error('The server answered ' + r.status);
                    res = await r.json();
                } catch (e) {
                    text.textContent = `Stopped: ${e.message}. ${sent} sent so far — click Send again to continue.`;
                    break;
                }
                sent += res.sent;
                res.failed.forEach(f => { failed.push(f); errs.insertAdjacentHTML('beforeend', `<li>${f.staff_no} ${f.name}: ${f.error}</li>`); });
                const done = sent + failed.length;
                bar.style.width = Math.min(100, Math.round(done / Math.max(total, 1) * 100)) + '%';
                text.textContent = `${sent.toLocaleString()} sent, ${failed.length} failed, ${res.remaining.toLocaleString()} remaining`;
                if (res.remaining === 0 || (res.sent === 0 && res.failed.length === 0)) {
                    text.textContent = `Finished: ${sent.toLocaleString()} sent, ${failed.length} failed.` + (failed.length ? ' Failed ones stay "Never sent" — fix the e-mail or retry later.' : '');
                    break;
                }
            }
            if (stop) text.textContent = `Stopped: ${sent} sent, ${failed.length} failed. Click Send again to continue with the rest.`;
            document.getElementById('bulk-stop').classList.add('hidden');
            setTimeout(() => start.insertAdjacentHTML('afterend', ' <a href="" class="btn-wf-ghost btn-wf-sm ml-2">Refresh list</a>'), 300);
        });
    })();
    </script>
</x-admin-layout>
