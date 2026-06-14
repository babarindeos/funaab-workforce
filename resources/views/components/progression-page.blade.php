@props(['title', 'subtitle' => '', 'activeTab'])

@php
$tabs = [
    ['key' => 'promotions',      'label' => 'Promotion',       'icon' => 'ti-award',      'route' => 'admin.promotions.index'],
    ['key' => 'step_increments', 'label' => 'Step Increment',  'icon' => 'ti-step-into',  'route' => 'admin.step_increments.index'],
    ['key' => 'cadres',          'label' => 'Cadre',           'icon' => 'ti-hierarchy-2','route' => 'admin.cadres.index'],
    ['key' => 'cadre_ranks',     'label' => 'Rank',            'icon' => 'ti-certificate','route' => 'admin.cadre_ranks.index'],
    ['key' => 'designations',    'label' => 'Designation',     'icon' => 'ti-id-badge',   'route' => 'admin.designations.index'],
    ['key' => 'salary_structures','label' => 'Salary Structure','icon' => 'ti-receipt',   'route' => 'admin.salary_structures.index'],
    ['key' => 'salary_levels',   'label' => 'Salary Level',    'icon' => 'ti-chart-bar',  'route' => 'admin.salary_levels.index'],
    ['key' => 'salary_steps',    'label' => 'Salary Step',     'icon' => 'ti-stairs',     'route' => 'admin.salary_steps.index'],
];
@endphp

<x-admin-layout :title="$title">

    <div class="mb-5">
        <h1 class="pg-title">{{ $title }}</h1>
        @if($subtitle)<p class="pg-sub">{{ $subtitle }}</p>@endif
    </div>

    {{-- Tab navigation --}}
    <div class="wf-card !p-0 mb-5 overflow-hidden">
        <div class="flex overflow-x-auto border-b border-gray-100 bg-gray-50 scrollbar-thin">
            @foreach($tabs as $tab)
                @php
                    try { $href = route($tab['route']); } catch(\Exception $e) { $href = '#'; }
                @endphp
                <a href="{{ $href }}"
                   class="flex items-center gap-1.5 px-4 py-3 text-xs font-bold whitespace-nowrap
                          border-b-2 transition-colors flex-shrink-0
                          {{ $activeTab === $tab['key']
                              ? 'border-funaab-600 text-funaab-700 bg-white'
                              : 'border-transparent text-gray-400 hover:text-gray-700 hover:bg-white' }}">
                    <i class="ti {{ $tab['icon'] }} text-sm"></i>
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </div>
        <div class="p-5">
            {{ $slot }}
        </div>
    </div>

</x-admin-layout>
