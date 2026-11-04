<x-admin-layout title="Dashboard">

    {{-- Page Header --}}
    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">Dashboard</h1>
            <p class="pg-sub">Federal University of Agriculture, Abeokuta — Admin Overview · as at {{ now()->format('d M Y, H:i') }}</p>
        </div>
        <a href="{{ route('admin.staff.create') }}" class="btn-wf-primary">
            <i class="ti ti-plus text-sm"></i> Add Staff
        </a>
    </div>

    {{-- ── 1. Summary boxes ── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
        <a href="{{ route('admin.staff.index') }}" class="stat-card block hover:shadow-sm">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-funaab-50 flex items-center justify-center"><i class="ti ti-users text-funaab-700 text-sm"></i></div>
                TOTAL STAFF
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ number_format($totalStaff) }}</div>
            <div class="text-[10.5px] {{ $addedThisMonth ? 'text-green-600' : 'text-gray-400' }} mt-1">
                {{ $addedThisMonth ? '↑ ' . $addedThisMonth . ' added this month' : 'None added this month' }}
            </div>
        </a>

        <a href="{{ route('admin.leaves.index') }}" class="stat-card block hover:shadow-sm">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-gold-50 flex items-center justify-center"><i class="ti ti-calendar-off text-gold-600 text-sm"></i></div>
                ON LEAVE TODAY
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ number_format($onLeave) }}</div>
            <div class="text-[10.5px] text-gray-400 mt-1">{{ $resumingSoon }} resuming in the next 7 days</div>
        </a>

        <a href="{{ route('admin.promotions.index') }}" class="stat-card block hover:shadow-sm">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-blue-50 flex items-center justify-center"><i class="ti ti-award text-blue-600 text-sm"></i></div>
                PENDING PROMOTIONS
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ number_format($pendingPromotions) }}</div>
            <div class="text-[10.5px] {{ $pendingPromotions ? 'text-red-500' : 'text-gray-400' }} mt-1">Awaiting approval or effective date</div>
        </a>

        <a href="{{ route('admin.departments.index') }}" class="stat-card block hover:shadow-sm">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-purple-50 flex items-center justify-center"><i class="ti ti-building text-purple-600 text-sm"></i></div>
                DEPARTMENTS
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ number_format($totalDepartments) }}</div>
            <div class="text-[10.5px] text-gray-400 mt-1">Across {{ $totalDivisions }} {{ \Illuminate\Support\Str::plural('division', $totalDivisions) }} / colleges</div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">

        {{-- ── 2. Recent activity ── --}}
        <div class="wf-card">
            <div class="wf-card-title"><i class="ti ti-clock-hour-4 text-gray-400"></i> Recent Activity</div>
            <div class="divide-y divide-gray-50">
                @forelse($recentActivity as $a)
                    <div class="flex items-start gap-3 py-2.5">
                        <div class="w-2 h-2 rounded-full {{ $a['colour'] }} mt-1.5 flex-shrink-0"></div>
                        <div>
                            <div class="text-xs text-gray-600">{!! $a['html'] !!}</div>
                            <div class="text-[10.5px] text-gray-400 mt-0.5">
                                {{ \Carbon\Carbon::parse($a['at'])->isToday() ? 'Today, ' . \Carbon\Carbon::parse($a['at'])->format('g:i A')
                                   : (\Carbon\Carbon::parse($a['at'])->isYesterday() ? 'Yesterday, ' . \Carbon\Carbon::parse($a['at'])->format('g:i A')
                                   : \Carbon\Carbon::parse($a['at'])->format('d M Y, g:i A')) }}
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 py-6 text-center">No activity recorded yet.</p>
                @endforelse
            </div>
        </div>

        {{-- ── 3. Staff by category ── --}}
        <div class="wf-card">
            <div class="wf-card-title"><i class="ti ti-chart-donut text-gray-400"></i> Staff by Category</div>
            @php $bars = ['bg-funaab-500', 'bg-gold-500', 'bg-blue-400', 'bg-purple-400', 'bg-gray-400']; @endphp
            <div class="space-y-3">
                @forelse($categoryRows as $row)
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-500">{{ $row['label'] }}</span>
                            <span class="font-bold text-gray-700">{{ number_format($row['count']) }} ({{ $row['pct'] }}%)</span>
                        </div>
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full {{ $bars[$loop->index % count($bars)] }} rounded-full" style="width: {{ $row['pct'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 py-6 text-center">No staff records yet.</p>
                @endforelse
            </div>

            <div class="grid grid-cols-2 gap-2 mt-4 pt-4 border-t border-gray-100">
                <div class="bg-funaab-50 rounded-lg p-3">
                    <div class="text-[10px] text-funaab-700 font-bold tracking-wider mb-1">FULL-TIME</div>
                    <div class="text-xl font-bold text-funaab-800">{{ number_format($fullTime) }}</div>
                </div>
                <div class="bg-gold-50 rounded-lg p-3">
                    <div class="text-[10px] text-gold-700 font-bold tracking-wider mb-1">PART-TIME / CONTRACT</div>
                    <div class="text-xl font-bold text-gold-800">{{ number_format($otherTime) }}</div>
                </div>
            </div>
            @if($typeNotSet)
                <p class="text-[10.5px] text-amber-600 mt-2">{{ $typeNotSet }} staff have no employment type recorded.</p>
            @endif
        </div>
    </div>

    {{-- ── 4. Recently added staff ── --}}
    <div class="wf-card">
        <div class="flex items-center justify-between mb-3">
            <div class="wf-card-title mb-0"><i class="ti ti-users text-gray-400"></i> Recently Added Staff</div>
            <a href="{{ route('admin.staff.index') }}" class="text-xs text-funaab-700 font-bold hover:underline">View all →</a>
        </div>
        <div class="overflow-x-auto">
        <table class="wf-table">
            <thead>
                <tr><th>NAME</th><th>FILE NO.</th><th>DEPARTMENT</th><th>CATEGORY</th><th>DATE ADDED</th><th>STATUS</th></tr>
            </thead>
            <tbody>
                @forelse($recentStaff as $s)
                    @php
                        $onLeaveNow = in_array($s->id, $onLeaveIds);
                        $status     = $onLeaveNow ? 'On Leave' : ($s->staffStatus?->status ?? 'Not set');
                        $badge      = $onLeaveNow ? 'badge-leave' : (in_array($status, ['Active', 'Sabbatical']) ? 'badge-active' : 'badge-inactive');
                    @endphp
                    <tr>
                        <td class="col-name">
                            <a href="{{ route('admin.staff.show', $s) }}" class="hover:underline">
                                {{ $s->title?->title }} {{ $s->surname }}, {{ $s->firstname }}
                            </a>
                        </td>
                        <td class="col-mono">{{ $s->staff_no }}</td>
                        <td>{{ $s->employment?->department?->department_name ?? '—' }}</td>
                        <td>{{ $s->employment?->staffCategory?->category ?? '—' }}</td>
                        <td>{{ $s->created_at?->format('d M Y') ?? '—' }}</td>
                        <td><span class="badge {{ $badge }}">{{ $status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-8 text-gray-400">No staff records yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

</x-admin-layout>
