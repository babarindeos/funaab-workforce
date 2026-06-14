<x-admin-layout title="Dashboard">

    {{-- Page Header --}}
    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">Dashboard</h1>
            <p class="pg-sub">Federal University of Agriculture, Abeokuta — Admin Overview</p>
        </div>
        <div class="flex gap-2">
            <button class="btn-wf-ghost">
                <i class="ti ti-download text-sm"></i> Export
            </button>
            <a href="{{ route('admin.staff.create') }}" class="btn-wf-primary">
                <i class="ti ti-plus text-sm"></i> Add Staff
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">

        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-funaab-50 flex items-center justify-center">
                    <i class="ti ti-users text-funaab-700 text-sm"></i>
                </div>
                TOTAL STAFF
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $totalStaff ?? '1,247' }}</div>
            <div class="text-[10.5px] text-green-600 mt-1">↑ 23 added this month</div>
        </div>

        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-gold-50 flex items-center justify-center">
                    <i class="ti ti-calendar-off text-gold-600 text-sm"></i>
                </div>
                ON LEAVE
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $onLeave ?? '34' }}</div>
            <div class="text-[10.5px] text-gray-400 mt-1">12 resuming this week</div>
        </div>

        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-blue-50 flex items-center justify-center">
                    <i class="ti ti-award text-blue-600 text-sm"></i>
                </div>
                PENDING PROMOTION
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $pendingPromotions ?? '18' }}</div>
            <div class="text-[10.5px] text-red-500 mt-1">Awaiting approval</div>
        </div>

        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-purple-50 flex items-center justify-center">
                    <i class="ti ti-building text-purple-600 text-sm"></i>
                </div>
                DEPARTMENTS
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $totalDepartments ?? '48' }}</div>
            <div class="text-[10.5px] text-gray-400 mt-1">Across 9 colleges</div>
        </div>

    </div>

    {{-- Two-column section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">

        {{-- Recent Activity --}}
        <div class="wf-card">
            <div class="wf-card-title">
                <i class="ti ti-clock-hour-4 text-gray-400"></i> Recent Activity
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($recentActivity ?? [] as $activity)
                    <div class="flex gap-3 py-2">
                        <div class="w-2 h-2 rounded-full bg-funaab-500 mt-1.5 flex-shrink-0"></div>
                        <div>
                            <div class="text-xs text-gray-600">{{ $activity->description }}</div>
                            <div class="text-[10.5px] text-gray-400 mt-0.5">{{ $activity->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @endforeach

                {{-- Fallback placeholder rows --}}
                @if(empty($recentActivity) || count($recentActivity) === 0)
                    <div class="flex gap-3 py-2">
                        <div class="w-2 h-2 rounded-full bg-funaab-500 mt-1.5 flex-shrink-0"></div>
                        <div><div class="text-xs text-gray-600">New staff <strong>Adewale, O.B.</strong> onboarded — Registry</div><div class="text-[10.5px] text-gray-400 mt-0.5">Today, 10:14 AM</div></div>
                    </div>
                    <div class="flex gap-3 py-2">
                        <div class="w-2 h-2 rounded-full bg-gold-500 mt-1.5 flex-shrink-0"></div>
                        <div><div class="text-xs text-gray-600">Leave approved — <strong>Balogun, F.A.</strong></div><div class="text-[10.5px] text-gray-400 mt-0.5">Today, 9:02 AM</div></div>
                    </div>
                    <div class="flex gap-3 py-2">
                        <div class="w-2 h-2 rounded-full bg-blue-400 mt-1.5 flex-shrink-0"></div>
                        <div><div class="text-xs text-gray-600">Profile updated — <strong>Dr. Okonkwo, C.E.</strong></div><div class="text-[10.5px] text-gray-400 mt-0.5">Yesterday, 4:47 PM</div></div>
                    </div>
                    <div class="flex gap-3 py-2">
                        <div class="w-2 h-2 rounded-full bg-funaab-500 mt-1.5 flex-shrink-0"></div>
                        <div><div class="text-xs text-gray-600">Promotion confirmed — <strong>Suleiman, A.K.</strong> GL 12 → GL 13</div><div class="text-[10.5px] text-gray-400 mt-0.5">Yesterday, 2:15 PM</div></div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Staff by Category --}}
        <div class="wf-card">
            <div class="wf-card-title">
                <i class="ti ti-chart-donut text-gray-400"></i> Staff by Category
            </div>
            <div class="space-y-3">
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-500">Academic Staff</span>
                        <span class="font-bold text-gray-700">642 (51.5%)</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-funaab-500 rounded-full" style="width:51.5%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-500">Senior Non-Teaching</span>
                        <span class="font-bold text-gray-700">318 (25.5%)</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gold-500 rounded-full" style="width:25.5%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-500">Junior Non-Teaching</span>
                        <span class="font-bold text-gray-700">287 (23%)</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-400 rounded-full" style="width:23%"></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 mt-4 pt-4 border-t border-gray-100">
                <div class="bg-funaab-50 rounded-lg p-3">
                    <div class="text-[10px] text-funaab-700 font-bold tracking-wider mb-1">FULL-TIME</div>
                    <div class="text-xl font-bold text-funaab-800">1,089</div>
                </div>
                <div class="bg-gold-50 rounded-lg p-3">
                    <div class="text-[10px] text-gold-700 font-bold tracking-wider mb-1">PART-TIME / CONTRACT</div>
                    <div class="text-xl font-bold text-gold-800">158</div>
                </div>
            </div>
        </div>

    </div>

    {{-- Recent Staff Table --}}
    <div class="wf-card">
        <div class="flex items-center justify-between mb-3">
            <div class="wf-card-title mb-0">
                <i class="ti ti-users text-gray-400"></i> Recently Added Staff
            </div>
            <a href="{{ route('admin.staff.index') }}" class="text-xs text-funaab-700 font-bold hover:underline">
                View all →
            </a>
        </div>
        <table class="wf-table">
            <thead>
                <tr>
                    <th>NAME</th>
                    <th>FILE NO.</th>
                    <th>DEPARTMENT</th>
                    <th>CATEGORY</th>
                    <th>DATE ADDED</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentStaff ?? [] as $staff)
                    <tr>
                        <td class="col-name">{{ $staff->surname }}, {{ $staff->firstname }}</td>
                        <td class="col-mono">{{ $staff->staff_no }}</td>
                        <td>{{ $staff->employment->department->department_name ?? '—' }}</td>
                        <td>{{ $staff->employment->staffCategory->staff_category ?? '—' }}</td>
                        <td>{{ $staff->created_at->format('d M Y') }}</td>
                        <td><span class="badge badge-active">Active</span></td>
                    </tr>
                @empty
                    <tr><td class="col-name">Adewale, Oluwaseun B.</td><td class="col-mono">FUNAAB/001234</td><td>Registry</td><td>Senior Non-Teaching</td><td>06 Jun 2026</td><td><span class="badge badge-active">Active</span></td></tr>
                    <tr><td class="col-name">Dr. Okonkwo, Chidi E.</td><td class="col-mono">FUNAAB/000987</td><td>Crop Production</td><td>Academic</td><td>01 Jun 2026</td><td><span class="badge badge-active">Active</span></td></tr>
                    <tr><td class="col-name">Balogun, Fatima A.</td><td class="col-mono">FUNAAB/000812</td><td>Bursary</td><td>Senior Non-Teaching</td><td>28 May 2026</td><td><span class="badge badge-leave">On Leave</span></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-admin-layout>
