{{-- Actions for one document row (Registry). Needs $doc. --}}
<div class="flex items-center justify-end gap-1 flex-wrap" x-data="{ rejecting: false }">
    <a href="{{ route('admin.staff_documents.view', $doc) }}" target="_blank" class="btn-wf-ghost btn-wf-sm"><i class="ti ti-eye text-xs"></i> View</a>
    <a href="{{ route('admin.staff_documents.download', $doc) }}" class="btn-wf-ghost btn-wf-sm" title="Download"><i class="ti ti-download text-xs"></i></a>

    @if($doc->isPending())
        <form action="{{ route('admin.staff_documents.approve', $doc) }}" method="POST">
            @csrf @method('PATCH')
            <button class="btn-wf-primary btn-wf-sm"><i class="ti ti-check text-xs"></i> Approve</button>
        </form>
        <button type="button" class="btn-wf-danger btn-wf-sm" @click="rejecting = !rejecting"><i class="ti ti-x text-xs"></i> Reject</button>
        <form x-show="rejecting" x-cloak action="{{ route('admin.staff_documents.reject', $doc) }}" method="POST" class="w-full mt-2 flex gap-2">
            @csrf @method('PATCH')
            <input type="text" name="rejection_reason" required placeholder="Reason (the staff member will see this)" class="wf-input flex-1 text-xs">
            <button class="btn-wf-danger btn-wf-sm">Send</button>
        </form>
    @endif

    <form action="{{ route('admin.staff_documents.destroy', $doc) }}" method="POST"
          onsubmit="return confirm('Permanently remove this document from the staff file?')">
        @csrf @method('DELETE')
        <button class="btn-wf-ghost btn-wf-sm text-red-500" title="Remove"><i class="ti ti-trash text-xs"></i></button>
    </form>
</div>
