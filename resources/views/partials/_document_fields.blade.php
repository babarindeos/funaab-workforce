{{-- Shared document upload fields (staff and Registry). Needs $categories (with types). --}}
@php $selectedType = old('document_type_id'); @endphp
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="wf-label">Category <span class="text-red-500">*</span></label>
        <select id="doc-category" class="wf-input w-full" onchange="filterDocTypes()">
            <option value="">— Select category —</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" {{ $c->types->contains('id', (int) $selectedType) ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="wf-label">Document type <span class="text-red-500">*</span></label>
        <select name="document_type_id" id="doc-type" class="wf-input w-full @error('document_type_id') border-red-400 @enderror" required>
            <option value="">— Select document type —</option>
            @foreach($categories as $c)
                @foreach($c->types as $t)
                    <option value="{{ $t->id }}" data-category="{{ $c->id }}" {{ (int) $selectedType === $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            @endforeach
        </select>
        @error('document_type_id')<p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label class="wf-label">Title / reference <span class="text-gray-400 font-normal">(optional)</span></label>
        <input type="text" name="title" value="{{ old('title') }}" class="wf-input w-full" placeholder="e.g. Promotion to Senior Technologist (Ref. FUNAAB/REG/123)">
        @error('title')<p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="wf-label">Date on the document <span class="text-gray-400 font-normal">(optional)</span></label>
        <input type="date" name="document_date" value="{{ old('document_date') }}" class="wf-input w-full">
        @error('document_date')<p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="wf-label">File <span class="text-red-500">*</span></label>
        <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required class="wf-input w-full @error('file') border-red-400 @enderror">
        <p class="text-[10px] text-gray-400 mt-1">PDF, JPG or PNG — up to 5 MB</p>
        @error('file')<p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label class="wf-label">Note <span class="text-gray-400 font-normal">(optional)</span></label>
        <textarea name="description" rows="2" class="wf-input w-full resize-none">{{ old('description') }}</textarea>
    </div>
</div>

<script>
    function filterDocTypes() {
        const cat = document.getElementById('doc-category').value;
        const sel = document.getElementById('doc-type');
        [...sel.options].forEach(o => {
            if (!o.value) return;
            o.hidden = cat !== '' && o.dataset.category !== cat;
        });
        if (sel.selectedOptions[0]?.hidden) sel.value = '';
    }
    document.addEventListener('DOMContentLoaded', filterDocTypes);
</script>
