<x-settings-page title="Contact Types" subtitle="Types of contact information for staff"
    back-route="admin.settings.index"
    add-label="Add Contact Type"
    add-onclick="document.getElementById('modal-create').classList.remove('hidden')">

    <div class="wf-card">
        <table class="wf-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>CONTACT TYPE</th>
                    <th>CREATED</th>
                    <th class="text-right">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contact_types as $item)
                <tr>
                    <td class="text-gray-400">{{$loop->iteration}}</td>
                    <td class="col-name">{{$item->contact_type_name}}</td>
                    <td class="text-gray-400 text-xs">{{$item->created_at->format('d M Y')}}</td>
                    <td>
                        <div class="flex items-center justify-end gap-1">
                            <button onclick="openEdit({{$item->id}}, '{{ addslashes($item->contact_type_name) }}')"
                                    class="btn-wf-ghost btn-wf-sm">
                                <i class="ti ti-edit text-xs"></i> Edit
                            </button>
                            <form action="{{route('admin.contact_type.delete', $item)}}" method="POST"
                                  onsubmit="return confirm('Delete this record?')">
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
                    <td colspan="4" class="text-center py-10 text-gray-400">
                        <i class="ti ti-inbox text-3xl block mb-2"></i>
                        No records yet.
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
                <h2 class="font-bold text-gray-800 text-sm">Add Contact Type</h2>
                <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
            </div>
            <form action="{{route('admin.contact_types.store')}}" method="POST" class="px-5 py-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">
                        CONTACT TYPE <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="contact_type_name" value="{{old('contact_type_name')}}"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                  focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50"
                           required autofocus />
                    @error('contact_type_name')<p class="text-red-500 text-xs mt-1">{{$message}}</p>@enderror
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                            onclick="document.getElementById('modal-create').classList.add('hidden')"
                            class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Edit Contact Types</h2>
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
            </div>
            <form id="edit-form" method="POST" class="px-5 py-4">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">
                        CONTACT TYPE <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="contact_type_name" id="edit-field"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                  focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50"
                           required />
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
        function openEdit(id, value) {
            document.getElementById('edit-field').value = value;
            document.getElementById('edit-form').action = `/admin/contact_types/${id}`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }
        @if($errors->any())
            document.getElementById('modal-create').classList.remove('hidden');
        @endif
    </script>

</x-settings-page>
