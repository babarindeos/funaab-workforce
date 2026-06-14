@props(['route', 'icon', 'label'])

@php
    // Safe route check — route may not exist yet during development
    try {
        $href = route($route);
        $isActive = request()->routeIs($route) || request()->routeIs(rtrim($route, 'index') . '*');
    } catch (\Exception $e) {
        $href = '#';
        $isActive = false;
    }
@endphp

<a href="{{ $href }}"
   class="sb-item sub {{ $isActive ? 'active' : '' }}">
    <i class="{{ $icon }} text-sm flex-shrink-0"></i>
    <span x-show="!collapsed" x-transition.opacity class="truncate">{{ $label }}</span>
</a>
