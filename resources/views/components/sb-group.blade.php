@props(['label', 'icon', 'group'])

@php
    // Check if any child route is active by looking at current URL
    $groupOpen = str_contains(request()->path(), $group) || session('sb_open_' . $group, false);
@endphp

<div x-data="{ open: {{ $groupOpen ? 'true' : 'false' }} }">

    {{-- Group header button --}}
    <button @click="open = !open; $dispatch('sb-group-toggle', { group: '{{ $group }}', open: open })"
            class="sb-item w-full"
            :title="collapsed ? '{{ $label }}' : ''">
        <i class="{{ $icon }} text-base flex-shrink-0"></i>
        <span x-show="!collapsed" x-transition.opacity class="flex-1 text-left truncate">{{ $label }}</span>
        <i class="ti ti-chevron-right text-xs text-gray-400 flex-shrink-0 transition-transform duration-200"
           x-show="!collapsed"
           :class="open ? 'rotate-90' : ''"
           x-transition.opacity></i>
    </button>

    {{-- Sub items --}}
    <div x-show="open && !collapsed"
         x-collapse
         class="overflow-hidden">
        {{ $slot }}
    </div>

</div>
