<x-settings-page title="Staff Categories" subtitle="Categories of staff — Academic, Non-Teaching, etc."
    back-route="admin.settings.index"
    add-label="Add Category"
    add-onclick="document.getElementById('modal-create').classList.remove('hidden')">

    <div class="wf-card">
        <table class="wf-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>CATEGORY</th>
                    <th>PARENT CATEGORY</th>
                    <th>STAFF COUNT</th>
                    <th>CREATED</th>
                    <th class="text-right">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staff_categories as $cat)
                <tr>
                    <td class="text-gray-400">{{ $loop->iteration }}</td>
                    <td class="col-name">{{ $cat->category }}</td>
                    <td>
                        @if($cat->parent_staff_category && $cat->parent)
                            <span class="badge badge-info">{{ $cat->parent->category }}</span>
                        @else
                            <span class="text-gray-400 text-xs">Root</span>
                        @endif
                    </td>
                    <td>
                        <spanclass="badgebadge-active">{{ $cat->employments_count ?? 0 }}</span>
                    </td>
                    <td class="text-gray-400 text-xs">{{ $cat->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="flex items-center justify-end gap-1">
                            <button onclick="openEdit({{ $cat->id }}, '{{ addslashes($cat->category) }}', {{ $cat->parent_staff_category ?? 0 }})"
                                    class="btn-wf-ghost btn-wf-sm">
                                <i class="ti ti-edit text-xs"></i> Edit
                            </button>
                            <form action="{{ route('admin.staff_categories.delete', $cat) }}" method="POST"
                                  onsubmit="return confirm('Delete this category? This may affect staff records.')">
                                @csrf @method('DELETE')
                                <button class="btn-wf-danger btn-wf-sm">
                                    <i class="ti ti-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-10 text-gray-400">
                        <i class="ti ti-category text-3xl block mb-2"></i>
                        No staff categories defined yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- CREATE MODAL --}}
    <div id="modal-create" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Add Staff Category</h2>
                <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
            </div>
            <form action="{{ route('admin.staff_categories.store') }}" method="POST" class="px-5 py-4 space-y-3">
                @csrf
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">
                        CATEGORY NAME <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="category" value="{{ old('category') }}"
                           placeholder="e.g. Academic Staff, Senior Non-Teaching…"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                  focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50"
                           required />
                    @error('category')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">
                        PARENT CATEGORY <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <select name="parent_staff_category"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                   focus:outline-none focus:border-funaab-500">
                        <option value="0">— None (Root Category) —</option>
                        @foreach($staff_categories as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->category }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                            onclick="document.getElementById('modal-create').classList.add('hidden')"
                            class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Edit Staff Category</h2>
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
            </div>
            <form id="edit-form" method="POST" class="px-5 py-4 space-y-3">
                @csrf @method('PUT')
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">CATEGORY NAME</label>
                    <input type="text" name="category" id="edit-name"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                  focus:outline-none focus:border-funaab-500" required />
                </div>
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">PARENT CATEGORY</label>
                    <select name="parent_staff_category" id="edit-parent"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                   focus:outline-none focus:border-funaab-500">
                        <option value="0">— None (Root Category) —</option>
                        @foreach($staff_categories as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->category }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                            onclick="document.getElementById('modal-edit').classList.add('hidden')"
                            class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEdit(id, name, parentId) {
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-parent').value = parentId;
            document.getElementById('edit-form').action = `/admin/staff_categories/${id}/update`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }
        @if($errors->any())
            document.getElementById('modal-create').classList.remove('hidden');
        @endif
    </script>

</x-settings-page>
