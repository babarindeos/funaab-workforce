<x-settings-page title="Deployment Posts" subtitle="Positions staff can be deployed to"
    back-route="admin.settings.index"
    add-label="Add Deployment Post"
    add-onclick="document.getElementById('modal-create').classList.remove('hidden')">

    <div class="wf-card">
        <table class="wf-table">
            <thead>
                <tr><th>#</th><th>DEPLOYMENT POST</th><th>DEPLOYMENTS</th><th>CREATED</th><th class="text-right">ACTIONS</th></tr>
            </thead>
            <tbody>
                @forelse($deployment_posts as $post)
                <tr>
                    <td class="text-gray-400">{{ $loop->iteration }}</td>
                    <td class="col-name">{{ $post->deployment_post }}</td>
                    <td><span class="badge badge-info">{{ $post->deployments_count ?? 0 }}</span></td>
                    <td class="text-gray-400 text-xs">{{ $post->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="flex items-center justify-end gap-1">
                            <button onclick="openEdit({{ $post->id }}, '{{ addslashes($post->deployment_post) }}')"
                                    class="btn-wf-ghost btn-wf-sm"><i class="ti ti-edit text-xs"></i> Edit</button>
                            <form action="{{ route('admin.deployment_posts.delete', $post) }}" method="POST"
                                  onsubmit="return confirm('Delete this post?')">
                                @csrf @method('DELETE')
                                <button class="btn-wf-danger btn-wf-sm"><i class="ti ti-trash text-xs"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-10 text-gray-400"><i class="ti ti-map-2 text-3xl block mb-2"></i>No deployment posts defined.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="modal-create" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Add Deployment Post</h2>
                <button onclick="document.getElementById('modal-create').classList.add('hidden')" class="text-gray-400"><i class="ti ti-x"></i></button>
            </div>
            <form action="{{ route('admin.deployment_posts.store') }}" method="POST" class="px-5 py-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">POST NAME <span class="text-red-500">*</span></label>
                    <input type="text" name="deployment_post" value="{{ old('deployment_post') }}"
                           placeholder="e.g. HOD Acting, Academic Advisor…"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500" required />
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-create').classList.add('hidden')" class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">Edit Deployment Post</h2>
                <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-gray-400"><i class="ti ti-x"></i></button>
            </div>
            <form id="edit-form" method="POST" class="px-5 py-4">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">POST NAME</label>
                    <input type="text" name="deployment_post" id="edit-field"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500" required />
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')" class="btn-wf-ghost">Cancel</button>
                    <button type="submit" class="btn-wf-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEdit(id, value) {
            document.getElementById('edit-field').value = value;
            document.getElementById('edit-form').action = `/admin/deployment_posts/${id}`;
            document.getElementById('modal-edit').classList.remove('hidden');
        }
    </script>

</x-settings-page>
