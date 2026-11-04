<x-staff-layout title="My Documents">

    <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
        <div>
            <h1 class="pg-title">My Documents</h1>
            <p class="pg-sub">Your important records held in Registry</p>
        </div>
        <a href="{{ route('staff.documents.create') }}" class="btn-wf-primary">
            <i class="ti ti-upload text-sm"></i> Upload a Document
        </a>
    </div>

    @foreach(['success' => 'bg-green-50 border-green-200 text-green-800', 'error' => 'bg-red-50 border-red-200 text-red-700'] as $key => $cls)
        @if(session($key))
            <div class="mb-4 rounded-lg border px-4 py-3 text-sm {{ $cls }}">{{ session($key) }}</div>
        @endif
    @endforeach

    <div class="grid grid-cols-3 gap-3 mb-4">
        <div class="wf-card py-3 text-center"><p class="text-lg font-bold text-gray-800">{{ $counts['approved'] ?? 0 }}</p><p class="text-[11px] text-gray-500">Official</p></div>
        <div class="wf-card py-3 text-center"><p class="text-lg font-bold text-amber-600">{{ $counts['pending'] ?? 0 }}</p><p class="text-[11px] text-gray-500">Awaiting Registry approval</p></div>
        <div class="wf-card py-3 text-center"><p class="text-lg font-bold text-red-600">{{ $counts['rejected'] ?? 0 }}</p><p class="text-[11px] text-gray-500">Rejected</p></div>
    </div>

    @forelse($grouped as $categoryName => $docs)
        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-folder text-gray-400"></i> {{ $categoryName }}</div>
            <div class="overflow-x-auto">
            <table class="wf-table">
                <thead>
                    <tr><th>DOCUMENT</th><th>DATE</th><th>STATUS</th><th class="text-right">ACTIONS</th></tr>
                </thead>
                <tbody>
                @foreach($docs as $doc)
                    <tr>
                        <td>
                            <p class="font-semibold text-gray-700">{{ $doc->type?->name }}</p>
                            @if($doc->title)<p class="text-[11px] text-gray-500">{{ $doc->title }}</p>@endif
                            <p class="text-[10px] text-gray-400">
                                {{ $doc->source === 'registry' ? 'Added by Registry' : 'Uploaded by you' }} · {{ $doc->created_at->format('d M Y') }} · {{ $doc->size_label }}
                            </p>
                            @if($doc->isRejected() && $doc->rejection_reason)
                                <p class="text-[11px] text-red-600 mt-1"><i class="ti ti-alert-circle text-xs"></i> Reason: {{ $doc->rejection_reason }}</p>
                            @endif
                        </td>
                        <td class="whitespace-nowrap">{{ $doc->document_date?->format('d M Y') ?? '—' }}</td>
                        <td><span class="badge {{ $doc->status_badge }}">{{ $doc->status_label }}</span></td>
                        <td>
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('staff.documents.view', $doc) }}" target="_blank" class="btn-wf-ghost btn-wf-sm"><i class="ti ti-eye text-xs"></i> View</a>
                                <a href="{{ route('staff.documents.download', $doc) }}" class="btn-wf-ghost btn-wf-sm"><i class="ti ti-download text-xs"></i></a>
                                @if($doc->source === 'staff' && !$doc->isApproved())
                                    <form action="{{ route('staff.documents.destroy', $doc) }}" method="POST"
                                          onsubmit="return confirm('Withdraw this upload?')">
                                        @csrf @method('DELETE')
                                        <button class="btn-wf-danger btn-wf-sm" title="Withdraw"><i class="ti ti-trash text-xs"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            </div>
        </div>
    @empty
        <div class="wf-card flex flex-col items-center justify-center py-16 text-center">
            <i class="ti ti-file-off text-3xl text-gray-300 mb-3"></i>
            <p class="text-sm font-bold text-gray-600">No documents in your file yet.</p>
            <p class="text-xs text-gray-400 mt-1">Documents added by Registry, and the ones you upload, will appear here.</p>
        </div>
    @endforelse

</x-staff-layout>
