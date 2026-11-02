<x-admin-layout>
<div class="max-w-5xl mx-auto px-4 py-6">

    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Edit Identity Record</h1>
            <p class="text-sm text-gray-500">{{ $identity->title }} {{ $identity->names }} ({{ $identity->fileno }}) — every change is logged.</p>
        </div>
        <a href="{{ route('admin.identity.show', $identity->id) }}" class="btn-wf-ghost btn-wf-sm">
            <i class="ti ti-arrow-left text-sm"></i> Back
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            Please correct the highlighted fields.
        </div>
    @endif

    <form action="{{ route('admin.identity.update', $identity->id) }}" method="POST" class="wf-card p-5">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($fields as $f)
                <div>
                    <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5 uppercase">{{ $f->caption }}</label>
                    <input type="text" name="{{ $f->field }}" value="{{ old($f->field, $identity->{$f->field}) }}"
                           class="wf-input w-full @error($f->field) border-red-400 @enderror">
                    @error($f->field)<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            <label class="block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5">REASON FOR CHANGE (OPTIONAL)</label>
            <textarea name="remark" rows="2" class="wf-input w-full">{{ old('remark') }}</textarea>
        </div>

        <div class="mt-5 flex justify-end gap-2">
            <a href="{{ route('admin.identity.show', $identity->id) }}" class="btn-wf-ghost">Cancel</a>
            <button type="submit" class="btn-wf-primary">Save changes</button>
        </div>
    </form>

    @if ($history->isNotEmpty())
        <div class="wf-card mt-6">
            <table class="wf-table">
                <thead><tr><th>WHEN</th><th>FIELD</th><th>OLD</th><th>NEW</th><th>REMARK</th></tr></thead>
                <tbody>
                @foreach ($history as $h)
                    <tr>
                        <td class="whitespace-nowrap">{{ \Carbon\Carbon::parse($h->created_at)->format('d M Y H:i') }}</td>
                        <td>{{ optional($fields->firstWhere('id', $h->update_field_id))->caption ?? $h->update_field_id }}</td>
                        <td class="text-gray-500">{{ $h->old }}</td>
                        <td>{{ $h->new }}</td>
                        <td class="text-gray-500">{{ $h->remark }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
</x-admin-layout>
