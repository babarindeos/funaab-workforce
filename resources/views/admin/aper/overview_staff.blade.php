<x-admin-layout title="APER Overview — Staff">

    <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
        <div>
            <h1 class="pg-title">{{ $title }}</h1>
            <p class="pg-sub">{{ $form->name }} — {{ $rows->count() }} {{ \Illuminate\Support\Str::plural('staff member', $rows->count()) }}</p>
        </div>
        <a href="{{ route('admin.aper.overview', ['form' => $form->uuid]) }}" class="btn-wf-ghost btn-wf-sm"><i class="ti ti-arrow-left text-sm"></i> Back to Overview</a>
    </div>

    <div class="wf-card">
        <div class="overflow-x-auto">
        <table class="wf-table">
            <thead>
                <tr><th>#</th><th>STAFF</th><th>FILE NO.</th><th>DEPARTMENT</th><th>FORM</th><th>STAGE</th><th>SUBMITTED</th><th>LAST SIGNED</th><th>PART C</th></tr>
            </thead>
            <tbody>
            @forelse($rows as $r)
                @php
                    $s = $r['staff']; $sub = $r['submission'];
                    $last = collect([
                        ['Panel', $sub?->panel_signed_at], ['Staff comment', $sub?->staff_commented_at],
                        ['HOD', $sub?->hod_signed_at], ['Supervisor', $sub?->supervisor_signed_at],
                    ])->first(fn ($x) => $x[1]);
                @endphp
                <tr>
                    <td class="text-gray-400 text-[11px]">{{ $loop->iteration }}</td>
                    <td class="col-name"><a href="{{ route('admin.staff.show', $s) }}" class="hover:underline">{{ $s->title?->title }} {{ $s->surname }}, {{ $s->firstname }}</a></td>
                    <td class="col-mono">{{ $s->staff_no }}</td>
                    <td>{{ $s->employment?->department?->department_name ?? '—' }}</td>
                    <td>{{ ['junior' => 'Junior', 'senior' => 'Senior', 'academic' => 'Academic'][$r['type']] ?? 'Not determined' }}</td>
                    <td><span class="badge {{ $r['stage'] === 'completed' ? 'badge-approved' : ($r['stage'] === 'not_started' ? 'badge-inactive' : 'badge-pending') }}">{{ $stages[$r['stage']] }}</span></td>
                    <td class="whitespace-nowrap text-[11px]">{{ $sub?->staff_submitted_at?->format('d M Y') ?? '—' }}</td>
                    <td class="whitespace-nowrap text-[11px]">{{ $last ? $last[0] . ', ' . $last[1]->format('d M Y') : '—' }}</td>
                    <td class="text-[11px] whitespace-nowrap">
                        @if($sub?->total_score !== null)
                            {{ rtrim(rtrim(number_format($sub->total_score, 2), '0'), '.') }}%
                            @if($sub->min_required !== null)
                                <span class="{{ $sub->meets_minimum ? 'text-green-600' : 'text-red-600' }}">(min {{ rtrim(rtrim(number_format($sub->min_required, 2), '0'), '.') }})</span>
                            @endif
                        @elseif($sub?->recommended_for_promotion)
                            <span class="text-gray-500">Recommended</span>
                        @else — @endif
                        @if($sub?->punctuality_ok === false)<span class="block text-amber-600">Punctuality &lt; 70%</span>@endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center py-10 text-gray-400">No staff in this group.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>

</x-admin-layout>
