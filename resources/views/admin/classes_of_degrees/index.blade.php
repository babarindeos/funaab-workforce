<x-settings-page title="Classes of Degree" subtitle="Academic grade classifications for qualifications"
    back-route="admin.settings.index"
    add-label="Add Class"
    add-onclick="document.getElementById('modal-create').classList.remove('hidden')">

    <div class="wf-card">
        <table class="wf-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>CLASS NAME</th>
                    <th>ORDER LEVEL</th>
                    <th>CREATED</th>
                    <th class="text-right">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($classes_of_degrees as $class)
                <tr>
                    <td class="text-gray-400">{{ $loop->iteration }}</td>
                    <td class="col-name">{{ $class->degree_class_name }}</td>
                    <td>
                        @if($class->order_level)
                            <span class="badge badge-info">Level {{ $class->order_level }}</span>
                        @else
                            <span class="text-gray-400 text-xs">—</span>
                        @endif
                    </td>
                    <td class="text-gray-400 text-xs">{{ $class->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="flex items-center justify-end gap-1">
                            <button onclick="openEdit({{ $class->id }}, '{{ addslashes($class->degree_class_name) }}', {{ $class->order_level ?? 'null' }})"
                                    class="btn-wf-ghost btn-wf-sm">
                                <i class="ti ti-edit text-xs"></i> Edit
                            </button>
                            <form action="{{ route('admin.classes_of_degrees.delete', $class) }}" method="POST"
                                  onsubmit="return confirm('Delete this class of degree?')">
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
                    <td colspan="5" class="text-center py-10 text-gray-400">
                        <i class="ti ti-certificate-2 text-3xl block mb-2"></i>
                        No classes of degree defined yet.
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
                <h2 class="font-bold text-gray-800 text-sm">Add Class of Degree</h2>
                <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
            </div>
            <form action="{{ route('admin.classes_of_degrees.store') }}" method="POST" class="px-5 py-4 space-y-3">
                @csrf
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">
                        CLASS NAME <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="degree_class_name" value="{{ old('degree_class_name') }}"
                           placeholder="e.g. First Class, Second Class Upper…"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                  focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50"
                           required autofocus />
                    @error('degree_class_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">
                        ORDER LEVEL <span class="text-gray-400 font-normal">(1 = highest)</span>
                    </label>
                    <input type="number" name="order_level" value="{{ old('order_level') }}"
                           placeholder="e.g. 1"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                  focus:outline-none focus:border-funaab-500" />
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
                <h2 class="font-bold text-gray-800 text-sm">Edit Class of Degree</h2>
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600"><i class="ti ti-x"></i></button>
            </div>
            <form id="edit-form" method="POST" class="px-5 py-4 space-y-3">
                @csrf @method('PUT')
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">CLASS NAME</label>
                    <input type="text" name="degree_class_name" id="edit-name"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                  focus:outline-none focus:border-funaab-500" required />
                </div>
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">ORDER LEVEL</label>
                    <input type="number" name="order_level" id="edit-order"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                  focus:outline-none focus:border-funaab-500" />
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
        function openEdit(id, name, order) {
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-order').value = order || '';
            document.getElementById('edit-form').action = `/admin/classes_of_degrees/${id}/update`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }
        @if($errors->any())
            document.getElementById('modal-create').classList.remove('hidden');
        @endif
    </script>

</x-settings-page>
