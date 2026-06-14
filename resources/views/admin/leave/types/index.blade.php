<x-admin-layout title="Leave Types">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">Leave Types</h1>
            <p class="pg-sub">Define and manage the categories of leave available to staff</p>
        </div>
        <button onclick="document.getElementById('modal-create').classList.remove('hidden')"
                class="btn-wf-primary">
            <i class="ti ti-plus text-sm"></i> Add Leave Type
        </button>
    </div>

    {{-- Table --}}
    <div class="wf-card">
        <table class="wf-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>LEAVE TYPE</th>
                    <th>TOTAL RECORDS</th>
                    <th>CREATED</th>
                    <th class="text-right">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaveTypes as $type)
                    <tr>
                        <td class="text-gray-400">{{ $loop->iteration }}</td>
                        <td class="col-name">{{ $type->leave_type }}</td>
                        <td>
                            <span class="badge badge-info">{{ $type->leaves_count }} records</span>
                        </td>
                        <td>{{ $type->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="flex items-center justify-end gap-1">
                                <button onclick="openEdit({{ $type->id }}, '{{ addslashes($type->leave_type) }}')"
                                        class="btn-wf-ghost btn-wf-sm">
                                    <i class="ti ti-edit text-xs"></i> Edit
                                </button>
                                <form action="{{ route('admin.leave_types.destroy', $type) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete this leave type?')">
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
                            <i class="ti ti-tags text-3xl block mb-2"></i>
                            No leave types defined yet. Add one to get started.
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
                <h2 class="font-bold text-gray-800 text-sm">Add Leave Type</h2>
                <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form action="{{ route('admin.leave_types.store') }}" method="POST" class="px-5 py-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">
                        LEAVE TYPE NAME <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="leave_type"
                           placeholder="e.g. Annual Leave, Sick Leave…"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm
                                  focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50"
                           required />
                    @error('leave_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                            onclick="document.getElementById('modal-create').classList.add('hidden')"
                            class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary">Save Leave Type</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Edit Leave Type</h2>
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form id="edit-form" method="POST" class="px-5 py-4">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-[11px] font-bold tracking-wider text-gray-500 mb-1.5">
                        LEAVE TYPE NAME <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="leave_type" id="edit-leave-type"
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
        function openEdit(id, name) {
            document.getElementById('edit-leave-type').value = name;
            document.getElementById('edit-form').action = `/admin/leave-types/${id}`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }
        // Auto-open create modal on validation error
        @if($errors->any())
            document.getElementById('modal-create').classList.remove('hidden');
        @endif
    </script>

</x-admin-layout>