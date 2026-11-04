<x-admin-layout title="APER Overview">
@php
    $link = fn (array $q) => route('admin.aper.overview.staff', array_merge(['form' => $form?->uuid], $q));
    $stageColours = [
        'not_started' => 'text-gray-500', 'draft' => 'text-amber-600', 'with_supervisor' => 'text-blue-600',
        'with_hod' => 'text-blue-700', 'awaiting_comment' => 'text-purple-600', 'awaiting_panel' => 'text-purple-700',
        'completed' => 'text-green-600',
    ];
@endphp

    <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
        <div>
            <h1 class="pg-title">APER Overview</h1>
            <p class="pg-sub">Live progress of the Annual Performance Evaluation Report</p>
        </div>
        @if($forms->isNotEmpty())
            <form method="GET" action="{{ route('admin.aper.overview') }}">
                <select name="form" onchange="this.form.submit()" class="wf-input">
                    @foreach($forms as $f)
                        <option value="{{ $f->uuid }}" {{ $form?->id === $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    @if(!$form)
        <div class="wf-card text-center py-16 text-gray-400">
            <i class="ti ti-forms text-3xl block mb-2"></i>
            No APER form has been created yet. <a href="{{ route('admin.aper.index') }}" class="text-funaab-700 font-semibold hover:underline">Create one</a>.
        </div>
    @else

    {{-- Form details --}}
    <div class="wf-card mb-4 flex flex-wrap items-center gap-x-6 gap-y-2 text-xs text-gray-600">
        <div><span class="font-bold text-gray-700">Period of report:</span>
            @if($form->review_period_start)
                {{ $form->review_period_start->format('d M Y') }} – {{ $form->review_period_end?->format('d M Y') }}
            @else <span class="text-amber-600">not set</span> @endif
        </div>
        <div><span class="font-bold text-gray-700">Submission window:</span>
            {{ $form->start_date->format('d M Y') }} – {{ $form->end_date->format('d M Y') }}
        </div>
        <span class="badge {{ $form->is_open ? 'badge-active' : 'badge-inactive' }}">{{ $form->is_open ? 'Open' : 'Closed' }}</span>
    </div>

    {{-- Progress boxes --}}
    <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-3 mb-2">
        <a href="{{ $link([]) }}" class="stat-card block hover:shadow-sm">
            <div class="text-[10px] font-bold tracking-widest text-gray-400 mb-1">ELIGIBLE STAFF</div>
            <div class="text-2xl font-bold text-gray-800">{{ number_format($data['eligibleTotal']) }}</div>
        </a>
        @foreach($stages as $key => $label)
            <a href="{{ $link(['stage' => $key]) }}" class="stat-card block hover:shadow-sm">
                <div class="text-[10px] font-bold tracking-widest text-gray-400 mb-1 uppercase">{{ $label }}</div>
                <div class="text-2xl font-bold {{ $stageColours[$key] }}">{{ number_format($data['stageCounts'][$key]) }}</div>
                @if($data['eligibleTotal'])
                    <div class="text-[10px] text-gray-400">{{ round($data['stageCounts'][$key] / $data['eligibleTotal'] * 100) }}%</div>
                @endif
            </a>
        @endforeach
    </div>
    <p class="text-[10.5px] text-gray-400 mb-4">
        Eligible = serving Junior and Senior Non-Teaching staff.
        @if($data['academicCount'])
            <a href="{{ $link(['type' => 'academic']) }}" class="hover:underline">{{ $data['academicCount'] }} academic staff</a> are not counted (Academic APER not yet available).
        @endif
        @if($data['unknownCount'])
            <a href="{{ $link(['type' => 'unknown']) }}" class="text-amber-600 hover:underline">{{ $data['unknownCount'] }} staff have no category / grade recorded</a>, so their form type cannot be determined.
        @endif
    </p>

    {{-- Junior vs Senior --}}
    <div class="wf-card mb-4">
        <div class="wf-card-title"><i class="ti ti-users text-gray-400"></i> By Form Type</div>
        <div class="overflow-x-auto">
        <table class="wf-table">
            <thead>
                <tr>
                    <th>FORM</th><th class="text-center">STAFF</th>
                    @foreach($stages as $label)<th class="text-center whitespace-nowrap">{{ strtoupper($label) }}</th>@endforeach
                </tr>
            </thead>
            <tbody>
            @foreach($data['byType'] as $t => $row)
                <tr>
                    <td class="col-name whitespace-nowrap">{{ $row['label'] }}</td>
                    <td class="text-center"><a href="{{ $link(['type' => $t]) }}" class="font-bold hover:underline">{{ $row['total'] }}</a></td>
                    @foreach($stages as $key => $label)
                        <td class="text-center">
                            @if($t === 'senior' && $key === 'with_supervisor')
                                <span class="text-gray-300" title="Senior form has no Supervisor stage">—</span>
                            @elseif($row['stages'][$key])
                                <a href="{{ $link(['type' => $t, 'stage' => $key]) }}" class="font-semibold {{ $stageColours[$key] }} hover:underline">{{ $row['stages'][$key] }}</a>
                            @else
                                <span class="text-gray-300">0</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>

    {{-- By department --}}
    <div class="wf-card mb-4">
        <div class="wf-card-title"><i class="ti ti-building text-gray-400"></i> Progress by Department</div>
        <div class="overflow-x-auto">
        <table class="wf-table">
            <thead>
                <tr>
                    <th>DEPARTMENT</th><th class="text-center">STAFF</th>
                    @foreach($stages as $label)<th class="text-center whitespace-nowrap">{{ strtoupper($label) }}</th>@endforeach
                    <th>ASSESSORS</th>
                </tr>
            </thead>
            <tbody>
            @forelse($data['byDepartment'] as $d)
                <tr>
                    <td class="col-name">{{ $d['name'] }}</td>
                    <td class="text-center"><a href="{{ $link(['dept' => $d['id']]) }}" class="font-bold hover:underline">{{ $d['total'] }}</a></td>
                    @foreach($stages as $key => $label)
                        <td class="text-center">
                            @if($d['stages'][$key])
                                <a href="{{ $link(['dept' => $d['id'], 'stage' => $key]) }}" class="font-semibold {{ $stageColours[$key] }} hover:underline">{{ $d['stages'][$key] }}</a>
                            @else
                                <span class="text-gray-300">0</span>
                            @endif
                        </td>
                    @endforeach
                    <td class="text-[11px] whitespace-nowrap">
                        @if(empty($d['missing']))
                            <span class="text-green-600"><i class="ti ti-check"></i> All assigned</span>
                        @else
                            <a href="{{ route('admin.aper.supervisors.index') }}" class="text-red-600 hover:underline" title="Forms in this department cannot move forward until these are assigned">
                                <i class="ti ti-alert-triangle"></i> No {{ implode(', ', $d['missing']) }}
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="{{ count($stages) + 3 }}" class="text-center py-8 text-gray-400">No eligible staff found.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>

    {{-- Promotion results --}}
    <div class="wf-card">
        <div class="wf-card-title"><i class="ti ti-award text-gray-400"></i> Promotion Results</div>
        @php $r = $data['results']; @endphp
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            @foreach([
                'recommended'      => ['Recommended by HOD', 'text-gray-800'],
                'scored'           => ['Scored by Panel', 'text-blue-700'],
                'meets'            => ['Met minimum score', 'text-green-600'],
                'below'            => ['Below minimum score', 'text-red-600'],
                'punctuality_fail' => ['Below 70% punctuality', 'text-amber-600'],
            ] as $key => [$label, $cls])
                <a href="{{ $link(['result' => $key]) }}" class="bg-gray-50 hover:bg-gray-100 rounded-lg p-3 block">
                    <div class="text-[10px] font-bold tracking-wider text-gray-500 mb-1 uppercase">{{ $label }}</div>
                    <div class="text-xl font-bold {{ $cls }}">{{ number_format($r[$key]) }}</div>
                </a>
            @endforeach
        </div>
        @if($r['recommended'] > $r['scored'])
            <p class="text-[11px] text-gray-500 mt-3">{{ $r['recommended'] - $r['scored'] }} recommended staff are still waiting for the staff comment or the Panel's scoring.</p>
        @endif
    </div>

    @endif
</x-admin-layout>
