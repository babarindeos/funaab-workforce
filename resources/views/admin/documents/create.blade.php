<x-admin-layout title="Upload Staff Document">

    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">Upload Staff Document</h1>
            <p class="pg-sub">Documents added by Registry are official immediately</p>
        </div>
        <a href="{{ route('admin.staff_documents.index') }}" class="btn-wf-ghost btn-wf-sm"><i class="ti ti-arrow-left text-sm"></i> Back</a>
    </div>

    <form action="{{ route('admin.staff_documents.store') }}" method="POST" enctype="multipart/form-data" class="wf-card max-w-2xl">
        @csrf
        <div class="mb-4">
            <label class="wf-label">Staff No. <span class="text-red-500">*</span></label>
            <input type="text" name="staff_no" value="{{ old('staff_no', $staffNo) }}" required placeholder="e.g. SP1572"
                   class="wf-input w-full max-w-xs uppercase @error('staff_no') border-red-400 @enderror">
            @error('staff_no')<p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>@enderror
        </div>

        @include('partials._document_fields', ['categories' => $categories])

        <div class="flex justify-end gap-2 mt-5">
            <a href="{{ route('admin.staff_documents.index') }}" class="btn-wf-ghost">Cancel</a>
            <button type="submit" class="btn-wf-primary"><i class="ti ti-upload text-sm"></i> Add to staff file</button>
        </div>
    </form>

</x-admin-layout>
