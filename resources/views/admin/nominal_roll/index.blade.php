<x-admin-layout title="Nominal Roll Import">

    <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
        <div>
            <h1 class="pg-title">Nominal Roll Import</h1>
            <p class="pg-sub">Upload the Registry nominal roll (.xlsx). You will see every change before anything is saved. No e-mails are sent.</p>
        </div>
        <a href="{{ route('admin.login_mails.index') }}" class="btn-wf-ghost"><i class="ti ti-mail text-sm"></i> Login Mails</a>
    </div>


    <div class="wf-card mb-4">
        <div class="wf-card-title"><i class="ti ti-file-spreadsheet text-gray-400"></i> Upload nominal roll</div>
        <form method="POST" action="{{ route('admin.nominal_roll.store') }}" enctype="multipart/form-data" class="flex flex-wrap items-end gap-3"
              onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerHTML='Reading the workbook…';">
            @csrf
            <div class="flex-1 min-w-[260px]">
                <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">EXCEL WORKBOOK (.XLSX)</label>
                <input type="file" name="file" accept=".xlsx" required class="block w-full text-sm border border-gray-200 rounded-lg p-2 bg-white">
                @error('file')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <button class="btn-wf-primary"><i class="ti ti-eye text-sm"></i> Upload &amp; preview</button>
        </form>
        <ul class="text-[11.5px] text-gray-500 mt-4 space-y-1 list-disc pl-5">
            <li>The <b>NOMINAL ROLL</b> sheet adds new staff and updates existing ones (matched by Staff ID No.).</li>
            <li>The <b>… DISENGAGEMENT</b> sheets record Off-Employment (retired, withdrawn, deceased …) for staff already in the system.</li>
            <li>Other sheets (visiting, part-time, contract, locum, sabbatical) are not imported.</li>
            <li>You can upload a new roll every month — only the differences are applied.</li>
        </ul>
    </div>

    <div class="wf-card">
        <div class="wf-card-title"><i class="ti ti-history text-gray-400"></i> Recent uploads</div>
        <div class="overflow-x-auto">
        <table class="wf-table">
            <thead><tr><th>FILE</th><th>UPLOADED</th><th>STATUS</th><th>NEW</th><th>UPDATED</th><th>PROBLEMS</th><th></th></tr></thead>
            <tbody>
            @forelse($imports as $imp)
                @php $c = $imp->summary['counts'] ?? []; @endphp
                <tr>
                    <td class="col-name">{{ $imp->file_name }}</td>
                    <td class="text-xs text-gray-500">{{ $imp->created_at->format('d M Y, g:i A') }} · {{ $imp->uploader?->surname }}</td>
                    <td>
                        @if($imp->status === 'applied')
                            <span class="badge badge-active">Applied {{ $imp->applied_at?->format('d M Y') }}</span>
                        @else
                            <span class="badge badge-leave">Preview only</span>
                        @endif
                    </td>
                    <td>{{ number_format($c['new'] ?? 0) }}</td>
                    <td>{{ number_format($c['update'] ?? 0) }}</td>
                    <td>{{ number_format($c['problems'] ?? 0) }}</td>
                    <td class="text-right"><a href="{{ route('admin.nominal_roll.show', $imp) }}" class="btn-wf-ghost btn-wf-sm">Open</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-8 text-gray-400">No uploads yet.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>
</x-admin-layout>
