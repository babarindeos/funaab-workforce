<x-admin-layout title="Document Categories & Types">

    <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
        <div>
            <h1 class="pg-title">Document Categories &amp; Types</h1>
            <p class="pg-sub">The kinds of records kept in a staff member's Registry file</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.staff_documents.index') }}" class="btn-wf-ghost"><i class="ti ti-arrow-left text-sm"></i> Staff Documents</a>
            <button onclick="document.getElementById('modal-cat').classList.remove('hidden')" class="btn-wf-primary"><i class="ti ti-plus text-sm"></i> Add Category</button>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    @forelse($categories as $cat)
        <div class="wf-card mb-4" x-data="{ editCat: false, addType: false }">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                <div>
                    <p class="text-sm font-bold text-gray-800"><i class="ti ti-folder text-gray-400"></i> {{ $cat->name }}</p>
                    @if($cat->description)<p class="text-[11px] text-gray-500">{{ $cat->description }}</p>@endif
                </div>
                <div class="flex gap-1">
                    <button type="button" @click="addType = !addType" class="btn-wf-ghost btn-wf-sm"><i class="ti ti-plus text-xs"></i> Add type</button>
                    <button type="button" @click="editCat = !editCat" class="btn-wf-ghost btn-wf-sm"><i class="ti ti-edit text-xs"></i> Edit</button>
                    @if($cat->types_count === 0)
                        <form action="{{ route('admin.document_setup.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Delete this category?')">
                            @csrf @method('DELETE')
                            <button class="btn-wf-danger btn-wf-sm"><i class="ti ti-trash text-xs"></i></button>
                        </form>
                    @endif
                </div>
            </div>

            <form x-show="editCat" x-cloak action="{{ route('admin.document_setup.categories.update', $cat) }}" method="POST" class="flex flex-wrap gap-2 mb-3 bg-gray-50 p-3 rounded-lg">
                @csrf @method('PUT')
                <input name="name" value="{{ $cat->name }}" required class="wf-input flex-1 min-w-[200px]" placeholder="Category name">
                <input name="description" value="{{ $cat->description }}" class="wf-input flex-1 min-w-[200px]" placeholder="Description (optional)">
                <input name="sort_order" type="number" value="{{ $cat->sort_order }}" class="wf-input w-20" title="Display order">
                <button class="btn-wf-primary btn-wf-sm">Save</button>
            </form>

            <form x-show="addType" x-cloak action="{{ route('admin.document_setup.types.store') }}" method="POST" class="flex flex-wrap gap-2 mb-3 bg-gray-50 p-3 rounded-lg">
                @csrf
                <input type="hidden" name="document_category_id" value="{{ $cat->id }}">
                <input name="name" required class="wf-input flex-1 min-w-[200px]" placeholder="New document type, e.g. Letter of Confirmation">
                <input name="description" class="wf-input flex-1 min-w-[200px]" placeholder="Description (optional)">
                <button class="btn-wf-primary btn-wf-sm">Add</button>
            </form>

            <table class="wf-table">
                <thead><tr><th>DOCUMENT TYPE</th><th>DOCUMENTS FILED</th><th class="text-right">ACTIONS</th></tr></thead>
                <tbody>
                @forelse($cat->types as $type)
                    <tr x-data="{ edit: false }">
                        <td>
                            <span x-show="!edit">{{ $type->name }}</span>
                            <form x-show="edit" x-cloak action="{{ route('admin.document_setup.types.update', $type) }}" method="POST" class="flex flex-wrap gap-2">
                                @csrf @method('PUT')
                                <input name="name" value="{{ $type->name }}" required class="wf-input flex-1 min-w-[180px]">
                                <select name="document_category_id" class="wf-input">
                                    @foreach($categories as $c)
                                        <option value="{{ $c->id }}" {{ $c->id === $cat->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                                <button class="btn-wf-primary btn-wf-sm">Save</button>
                            </form>
                        </td>
                        <td>{{ $type->documents_count }}</td>
                        <td>
                            <div class="flex justify-end gap-1">
                                <button type="button" @click="edit = !edit" class="btn-wf-ghost btn-wf-sm"><i class="ti ti-edit text-xs"></i></button>
                                @if($type->documents_count === 0)
                                    <form action="{{ route('admin.document_setup.types.destroy', $type) }}" method="POST" onsubmit="return confirm('Delete this document type?')">
                                        @csrf @method('DELETE')
                                        <button class="btn-wf-danger btn-wf-sm"><i class="ti ti-trash text-xs"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-gray-400 text-center py-4">No document types yet — click "Add type".</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    @empty
        <div class="wf-card text-center py-12 text-gray-400">No categories yet. Click "Add Category".</div>
    @endforelse

    {{-- Add category modal --}}
    <div id="modal-cat" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
        <form action="{{ route('admin.document_setup.categories.store') }}" method="POST" class="bg-white rounded-xl w-full max-w-md p-5 space-y-3">
            @csrf
            <p class="text-sm font-bold text-gray-800">Add Category</p>
            <input name="name" required class="wf-input w-full" placeholder="e.g. Pension Records">
            <input name="description" class="wf-input w-full" placeholder="Description (optional)">
            <div class="flex justify-end gap-2">
                <button type="button" class="btn-wf-ghost" onclick="document.getElementById('modal-cat').classList.add('hidden')">Cancel</button>
                <button class="btn-wf-primary">Add</button>
            </div>
        </form>
    </div>

</x-admin-layout>
