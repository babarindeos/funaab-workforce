<x-settings-page title="Degrees" subtitle="Academic degree types awarded to staff"
    back-route="admin.settings.index"
    add-label="Add Degree"
    add-onclick="document.getElementById('modal-create').classList.remove('hidden')">

    <div class="wf-card">
        <table class="wf-table">
            <thead>
                <tr><th>#</th><th>DEGREE NAME</th><th>ORDER LEVEL</th><th>REMARK</th><th>CREATED</th><th class="text-right">ACTIONS</th></tr>
            </thead>
            <tbody>
                @forelse($degrees as $degree)
                <tr>
                    <td class="text-gray-400">{{ $loop->iteration }}</td>
                    <td class="col-name">{{ $degree->degree_name }}</td>
                    <td class="text-gray-500">{{ $degree->order_level ?? '—' }}</td>
                    <td class="text-gray-400 text-xs max-w-[180px] truncate">{{ $degree->remark ?? '—' }}</td>
                    <td class="text-gray-400 text-xs">{{ $degree->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="flex items-center justify-end gap-1">
                            <button onclick="openEdit({{ $degree->id }}, '{{ addslashes($degree->degree_name) }}', {{ $degree->order_level ?? 'null' }}, '{{ addslashes($degree->remark ?? '') }}')"
                                    class="btn-wf-ghost btn-wf-sm"><i class="ti ti-edit text-xs"></i> Edit</button>
                            <form action="{{ route('admin.degrees.delete', $degree) }}" method="POST"
                                  onsubmit="return confirm('Delete this degree?')">
                                @csrf @method('DELETE')
                                <button class="btn-wf-danger btn-wf-sm"><i class="ti ti-trash text-xs"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-10 text-gray-400"><i class="ti ti-school text-3xl block mb-2"></i>No degrees defined.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @foreach(['create','edit'] as $mode)
    <div id="modal-{{ $mode }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">{{ $mode === 'create' ? 'Add Degree' : 'Edit Degree' }}</h2>
                <button onclick="document.getElementById('modal-{{ $mode }}').classList.add('hidden')" class="text-gray-400"><i class="ti ti-x"></i></button>
            </div>
            <form id="{{ $mode }}-form" method="POST" class="px-5 py-4 space-y-3">
                @csrf
                @if($mode === 'edit') @method('PUT') @endif
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">DEGREE NAME <span class="text-red-500">*</span></label>
                    <input type="text" name="degree_name" id="{{ $mode }}-name"
                           @if($mode === 'create') value="{{ old('degree_name') }}" @endif
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500" required />
                </div>
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">ORDER LEVEL</label>
                    <input type="number" name="order_level" id="{{ $mode }}-order"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500" />
                </div>
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">REMARK</label>
                    <textarea name="remark" id="{{ $mode }}-remark" rows="2"
                              class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500 resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-{{ $mode }}').classList.add('hidden')" class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary">{{ $mode === 'create' ? 'Save' : 'Update' }}</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    <script>
        document.getElementById('create-form').action = '{{ route("admin.degrees.store") }}';
        function openEdit(id, name, order, remark) {
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-order').value = order || '';
            document.getElementById('edit-remark').value = remark || '';
            document.getElementById('edit-form').action = `/admin/degrees/${id}`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }
    </script>

</x-settings-page>
