<x-admin-layout title="Staff Documents">

    <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
        <div>
            <h1 class="pg-title">Staff Documents</h1>
            <p class="pg-sub">Staff records held in Registry — review staff uploads and add documents</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.document_setup.index') }}" class="btn-wf-ghost"><i class="ti ti-settings text-sm"></i> Categories &amp; Types</a>
            <a href="{{ route('admin.staff_documents.create') }}" class="btn-wf-primary"><i class="ti ti-upload text-sm"></i> Upload Document</a>
        </div>
    </div>

    <div class="wf-card mb-4">
        <form method="GET" action="{{ route('admin.staff_documents.index') }}" class="flex flex-wrap items-center gap-3">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="relative flex-1 min-w-[200px]">
                <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="q" value="{{ $search }}" placeholder="Staff No. or name…"
                       class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-funaab-500 bg-white" />
            </div>
            <select name="category" class="wf-input">
                <option value="">All categories</option>
                @foreach($categories as $c)
                    <option value="{{ $c->uuid }}" {{ $category === $c->uuid ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-wf-ghost"><i class="ti ti-filter text-sm"></i> Filter</button>
        </form>
        <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1 mt-3 w-fit">
            @foreach(['pending' => 'Awaiting approval', 'approved' => 'Official', 'rejected' => 'Rejected', 'all' => 'All'] as $val => $label)
                <a href="{{ route('admin.staff_documents.index', array_merge(request()->query(), ['status' => $val, 'page' => 1])) }}"
                   class="px-3 py-1.5 text-xs font-semibold rounded-md {{ $status === $val ? 'bg-white text-funaab-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    {{ $label }}
                    @if($val !== 'all')<span class="ml-1 text-[10px] text-gray-400">{{ $counts[$val] ?? 0 }}</span>@endif
                </a>
            @endforeach
        </div>
    </div>

    <div class="wf-card">
        <div class="overflow-x-auto">
        <table class="wf-table">
            <thead>
                <tr><th>STAFF</th><th>DOCUMENT</th><th>DATE</th><th>UPLOADED</th><th>STATUS</th><th class="text-right">ACTIONS</th></tr>
            </thead>
            <tbody>
            @forelse($documents as $doc)
                <tr>
                    <td>
                        <a href="{{ route('admin.staff_documents.staff', $doc->staff) }}" class="font-semibold text-funaab-700 hover:underline">
                            {{ $doc->staff?->surname }}, {{ $doc->staff?->firstname }}
                        </a>
                        <p class="text-[10px] text-gray-400 col-mono">{{ $doc->staff?->staff_no }}</p>
                    </td>
                    <td>
                        <p class="font-semibold text-gray-700">{{ $doc->type?->name }}</p>
                        <p class="text-[10px] text-gray-400">{{ $doc->type?->category?->name }}</p>
                        @if($doc->title)<p class="text-[11px] text-gray-500">{{ $doc->title }}</p>@endif
                        @if($doc->isRejected())<p class="text-[11px] text-red-600">Reason: {{ $doc->rejection_reason }}</p>@endif
                    </td>
                    <td class="whitespace-nowrap">{{ $doc->document_date?->format('d M Y') ?? '—' }}</td>
                    <td class="text-[11px] whitespace-nowrap">
                        {{ $doc->source === 'registry' ? 'Registry' : 'Staff' }}<br>
                        <span class="text-gray-400">{{ $doc->created_at->format('d M Y H:i') }}</span>
                    </td>
                    <td><span class="badge {{ $doc->status_badge }}">{{ $doc->status_label }}</span></td>
                    <td>@include('admin.documents._row_actions', ['doc' => $doc])</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-10 text-gray-400">
                    <i class="ti ti-files text-3xl block mb-2"></i> No documents found.
                </td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
        @if($documents->hasPages())
            <div class="mt-4 pt-4 border-t border-gray-100">{{ $documents->links() }}</div>
        @endif
    </div>

</x-admin-layout>
