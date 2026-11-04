<x-staff-layout title="Upload a Document">

    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">Upload a Document</h1>
            <p class="pg-sub">Registry will review it before it becomes part of your official file</p>
        </div>
        <a href="{{ route('staff.documents.index') }}" class="btn-wf-ghost btn-wf-sm">
            <i class="ti ti-arrow-left text-sm"></i> Back to My Documents
        </a>
    </div>

    <form action="{{ route('staff.documents.store') }}" method="POST" enctype="multipart/form-data" class="wf-card max-w-2xl">
        @csrf
        @include('partials._document_fields', ['categories' => $categories])

        <div class="flex justify-end gap-2 mt-5">
            <a href="{{ route('staff.documents.index') }}" class="btn-wf-ghost">Cancel</a>
            <button type="submit" class="btn-wf-primary"><i class="ti ti-upload text-sm"></i> Upload for approval</button>
        </div>
    </form>

</x-staff-layout>
