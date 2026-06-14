@props(['route', 'icon', 'label', 'badge' => null])
 
@php
    try {
        $href = route($route);
        $isActive = request()->routeIs($route);
    } catch (\Exception $e) {
        $href = '#';
        $isActive = false;
    }
@endphp
 
<a href="{{ $href }}"
   class="sb-item {{ $isActive ? 'active' : '' }}"
   :title="collapsed ? '{{ $label }}' : ''">
    <i class="{{ $icon }} text-base flex-shrink-0"></i>
    <span x-show="!collapsed" x-transition.opacity class="flex-1 truncate">{{ $label }}</span>
    @if($badge)
        <span x-show="!collapsed" x-transition.opacity
              class="ml-auto text-[9.5px] bg-funaab-100 text-funaab-800 font-bold px-1.5 py-0.5 rounded-full">
            {{ $badge }}
        </span>
    @endif
</a>
 