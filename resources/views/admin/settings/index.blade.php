<x-admin-layout title="Settings">

    <div class="pg-hd mb-5" style="display:flex;justify-content:space-between;align-items:flex-start;">
        <div>
            <h1 class="pg-title">Settings</h1>
            <p class="pg-sub">Manage all reference data, lookups, and system configuration</p>
        </div>
    </div>

    @php
    $groups = [
        [
            'title' => 'Staff & HR Reference',
            'icon'  => 'ti-users',
            'color' => 'funaab',
            'items' => [
                ['label' => 'Employment Types',  'icon' => 'ti-briefcase',     'route' => 'admin.employment_types.index',  'count' => $counts['employment_types'],  'desc' => 'Full-time, Part-time, Contract…'],
                ['label' => 'Staff Titles',      'icon' => 'ti-tag',           'route' => 'admin.staff_titles.index',      'count' => $counts['staff_titles'],      'desc' => 'Dr., Prof., Mr., Mrs…'],
                ['label' => 'Staff Categories',  'icon' => 'ti-category',      'route' => 'admin.staff_categories.index',  'count' => $counts['staff_categories'],  'desc' => 'Academic, Senior Non-Teaching…'],
                ['label' => 'Staff Statuses',    'icon' => 'ti-toggle-right',  'route' => 'admin.staff_statuses.index',    'count' => $counts['staff_statuses'],    'desc' => 'Active, Retired, Suspended…'],
                ['label' => 'Genders',           'icon' => 'ti-gender-bigender','route' => 'admin.gender.index',           'count' => $counts['genders'],           'desc' => 'Male, Female, Other'],
                ['label' => 'Marital Statuses',  'icon' => 'ti-heart',         'route' => 'admin.marital_statuses.index',  'count' => $counts['marital_statuses'],  'desc' => 'Single, Married, Divorced…'],
                ['label' => 'Contact Types',     'icon' => 'ti-phone',         'route' => 'admin.contact_types.index',     'count' => $counts['contact_types'],     'desc' => 'Mobile, Office, Email…'],
            ],
        ],
        [
            'title' => 'Geography',
            'icon'  => 'ti-map',
            'color' => 'blue',
            'items' => [
                ['label' => 'Geo-Political Zones',    'icon' => 'ti-map',           'route' => 'admin.geo_pol_zones.index',        'count' => $counts['geo_pol_zones'],        'desc' => 'SW, SE, NC, NE, NW, SS'],
                ['label' => 'States',                 'icon' => 'ti-map-pin',       'route' => 'admin.states.index',               'count' => $counts['states'],               'desc' => '36 states + FCT'],
                ['label' => 'LGAs',                   'icon' => 'ti-location',      'route' => 'admin.lgas.index',                 'count' => $counts['lgas'],                 'desc' => 'Local Government Areas'],
                ['label' => 'Senatorial Districts',   'icon' => 'ti-building-arch', 'route' => 'admin.geo_pol_zones.index',        'count' => $counts['senatorial_districts'], 'desc' => '109 districts'],
            ],
        ],
        [
            'title' => 'Academic & Qualifications',
            'icon'  => 'ti-school',
            'color' => 'gold',
            'items' => [
                ['label' => 'Degrees',           'icon' => 'ti-school',        'route' => 'admin.degrees.index',            'count' => $counts['degrees'],        'desc' => 'B.Sc., M.Sc., Ph.D…'],
                ['label' => 'Classes of Degree', 'icon' => 'ti-certificate-2', 'route' => 'admin.classes_of_degrees.index', 'count' => $counts['degree_classes'], 'desc' => 'First Class, Second Class…'],
            ],
        ],
        [
            'title' => 'Organisation Structure',
            'icon'  => 'ti-hierarchy-2',
            'color' => 'purple',
            'items' => [
                ['label' => 'Division Types', 'icon' => 'ti-layout-distribute-vertical', 'route' => 'admin.division_types.index', 'count' => $counts['division_types'], 'desc' => 'College, Faculty, Admin…'],
                ['label' => 'Divisions',      'icon' => 'ti-hierarchy-2',               'route' => 'admin.divisions.index',      'count' => $counts['divisions'],      'desc' => 'Colleges and faculties'],
                ['label' => 'Departments',    'icon' => 'ti-building',                  'route' => 'admin.departments.index',    'count' => $counts['departments'],    'desc' => 'Academic & admin departments'],
            ],
        ],
        [
            'title' => 'Deployment',
            'icon'  => 'ti-arrows-exchange',
            'color' => 'red',
            'items' => [
                ['label' => 'Deployment Posts', 'icon' => 'ti-map-2', 'route' => 'admin.deployment_posts.index', 'count' => $counts['deployment_posts'], 'desc' => 'HOD Acting, Advisor, Protocol…'],
            ],
        ],
    ];

    $colorMap = [
        'funaab' => ['bg' => 'bg-funaab-50',  'text' => 'text-funaab-700',  'border' => 'border-funaab-100',  'count_bg' => 'bg-funaab-100',  'count_text' => 'text-funaab-800'],
        'blue'   => ['bg' => 'bg-blue-50',    'text' => 'text-blue-700',    'border' => 'border-blue-100',    'count_bg' => 'bg-blue-100',    'count_text' => 'text-blue-800'],
        'gold'   => ['bg' => 'bg-gold-50',    'text' => 'text-gold-700',    'border' => 'border-gold-100',    'count_bg' => 'bg-gold-100',    'count_text' => 'text-gold-800'],
        'purple' => ['bg' => 'bg-purple-50',  'text' => 'text-purple-700',  'border' => 'border-purple-100',  'count_bg' => 'bg-purple-100',  'count_text' => 'text-purple-800'],
        'red'    => ['bg' => 'bg-red-50',     'text' => 'text-red-700',     'border' => 'border-red-100',     'count_bg' => 'bg-red-100',     'count_text' => 'text-red-800'],
    ];
    @endphp

    <div class="space-y-6">
        @foreach($groups as $group)
        @php $c = $colorMap[$group['color']]; @endphp
        <div>
            {{-- Group header --}}
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-md {{ $c['bg'] }} {{ $c['text'] }} flex items-center justify-center">
                    <i class="ti {{ $group['icon'] }} text-base"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-700">{{ $group['title'] }}</h2>
                <div class="flex-1 h-px bg-gray-100 ml-1"></div>
            </div>

            {{-- Items grid --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($group['items'] as $item)
                @php
                    try { $href = route($item['route']); } catch(\Exception $e) { $href = '#'; }
                @endphp
                <a href="{{ $href }}"
                   class="wf-card group flex items-start gap-3 hover:border-gray-300
                          hover:shadow-sm transition-all duration-150 !py-3 !px-4">
                    <div class="w-8 h-8 rounded-md {{ $c['bg'] }} {{ $c['text'] }} flex items-center
                                justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                        <i class="ti {{ $item['icon'] }} text-base"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-1">
                            <span class="text-xs font-bold text-gray-800 truncate">{{ $item['label'] }}</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full flex-shrink-0
                                         {{ $c['count_bg'] }} {{ $c['count_text'] }}">
                                {{ number_format($item['count']) }}
                            </span>
                        </div>
                        <p class="text-[10.5px] text-gray-400 mt-0.5 truncate">{{ $item['desc'] }}</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

</x-admin-layout>
