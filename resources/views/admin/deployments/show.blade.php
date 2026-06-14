<x-settings-page title="Deployment Detail" back-route="admin.deployments.index">

    <div class="max-w-2xl space-y-4">

        {{-- Staff Banner --}}
        <div class="rounded-xl bg-gradient-to-r from-funaab-800 to-funaab-600 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-gold-500 flex items-center justify-center
                        text-funaab-900 font-bold text-lg flex-shrink-0">
                {{ strtoupper(substr($deployment->staff?->surname ?? 'S', 0, 1)) }}{{ strtoupper(substr($deployment->staff?->firstname ?? 'S', 0, 1)) }}
            </div>
            <div class="flex-1 text-white">
                <div class="font-bold text-base">
                    {{ $deployment->staff?->surname }}, {{ $deployment->staff?->firstname }}
                </div>
                <div class="text-white/70 text-xs mt-0.5 font-mono">
                    {{ $deployment->staff?->staff_no }}
                </div>
            </div>
            @if($deployment->end_date)
                <span class="badge badge-inactive">Completed</span>
            @else
                <span class="badge badge-active">Ongoing</span>
            @endif
        </div>

        <div class="wf-card">
            <div class="wf-card-title"><i class="ti ti-arrows-exchange text-gray-400"></i> Deployment Details</div>
            @php $cell = 'bg-gray-50 rounded-lg p-3'; $ck = 'text-[10px] text-gray-400 font-bold tracking-wider mb-1'; $cv = 'text-sm font-bold text-gray-800'; @endphp
            <div class="grid grid-cols-2 gap-3">
                <div class="{{ $cell }}"><div class="{{ $ck }}">DEPARTMENT</div><div class="{{ $cv }}">{{ $deployment->department?->department_name ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">POST</div><div class="{{ $cv }}">{{ $deployment->deploymentPost?->deployment_post ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">START DATE</div><div class="{{ $cv }}">{{ $deployment->start_date?->format('d M Y') ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">EXPECTED END</div><div class="{{ $cv }}">{{ $deployment->expected_end_date?->format('d M Y') ?? '—' }}</div></div>
                <div class="{{ $cell }}"><div class="{{ $ck }}">ACTUAL END</div><div class="{{ $cv }}">{{ $deployment->end_date?->format('d M Y') ?? 'Ongoing' }}</div></div>
                @if($deployment->remark)
                <div class="{{ $cell }} col-span-2"><div class="{{ $ck }}">REMARK</div><div class="{{ $cv }} font-normal">{{ $deployment->remark }}</div></div>
                @endif
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('admin.deployments.edit', $deployment) }}" class="btn-wf-primary">
                <i class="ti ti-edit text-sm"></i> Edit
            </a>
            <form action="{{ route('admin.deployments.delete', $deployment) }}" method="POST"
                  onsubmit="return confirm('Delete this deployment?')">
                @csrf @method('DELETE')
                <button class="btn-wf-danger"><i class="ti ti-trash text-sm"></i> Delete</button>
            </form>
        </div>

    </div>

</x-settings-page>
