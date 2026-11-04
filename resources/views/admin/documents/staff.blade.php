<x-admin-layout title="Staff File — Documents">

    <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
        <div>
            <h1 class="pg-title">{{ $staff->surname }}, {{ $staff->firstname }} {{ $staff->othernames }}</h1>
            <p class="pg-sub">{{ $staff->staff_no }} — documents in the Registry file</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.staff_documents.index', ['status' => 'all', 'q' => $staff->staff_no]) }}" class="btn-wf-ghost btn-wf-sm"><i class="ti ti-arrow-left text-sm"></i> All documents</a>
            <a href="{{ route('admin.staff_documents.create', ['staff_no' => $staff->staff_no]) }}" class="btn-wf-primary btn-wf-sm"><i class="ti ti-upload text-sm"></i> Add document</a>
        </div>
    </div>

    @forelse($grouped as $categoryName => $docs)
        <div class="wf-card mb-4">
            <div class="wf-card-title"><i class="ti ti-folder text-gray-400"></i> {{ $categoryName }}</div>
            <div class="overflow-x-auto">
            <table class="wf-table">
                <thead><tr><th>DOCUMENT</th><th>DATE</th><th>SOURCE</th><th>STATUS</th><th class="text-right">ACTIONS</th></tr></thead>
                <tbody>
                @foreach($docs as $doc)
                    <tr>
                        <td>
                            <p class="font-semibold text-gray-700">{{ $doc->type?->name }}</p>
                            @if($doc->title)<p class="text-[11px] text-gray-500">{{ $doc->title }}</p>@endif
                            @if($doc->description)<p class="text-[10px] text-gray-400">{{ $doc->description }}</p>@endif
                            @if($doc->isRejected())<p class="text-[11px] text-red-600">Reason: {{ $doc->rejection_reason }}</p>@endif
                        </td>
                        <td class="whitespace-nowrap">{{ $doc->document_date?->format('d M Y') ?? '—' }}</td>
                        <td class="text-[11px]">
                            {{ $doc->source === 'registry' ? 'Registry' : 'Staff' }}
                            @if($doc->reviewer && $doc->source === 'staff')
                                <br><span class="text-gray-400">reviewed by {{ $doc->reviewer->surname }}</span>
                            @endif
                        </td>
                        <td><span class="badge {{ $doc->status_badge }}">{{ $doc->status_label }}</span></td>
                        <td>@include('admin.documents._row_actions', ['doc' => $doc])</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            </div>
        </div>
    @empty
        <div class="wf-card text-center py-12 text-gray-400">
            <i class="ti ti-file-off text-3xl block mb-2"></i> No documents in this staff member's file yet.
        </div>
    @endforelse

</x-admin-layout>
