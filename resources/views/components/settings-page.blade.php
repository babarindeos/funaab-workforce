{{-- Reusable wrapper for all settings CRUD pages --}}
{{-- Usage: <x-settings-page title="..." subtitle="..." back-route="admin.settings.index" add-label="Add Item" add-route="..."> --}}

@props([
    'title',
    'subtitle'  => '',
    'backRoute'  => 'admin.settings.index',
    'addLabel'   => null,
    'addRoute'   => null,
    'addOnclick' => null,
])

<x-admin-layout :title="$title">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-1.5 text-xs text-gray-400 mb-4">
        <a href="{{ route('admin.settings.index') }}" class="hover:text-funaab-700 transition-colors font-bold">Settings</a>
        <i class="ti ti-chevron-right text-xs"></i>
        <span class="text-gray-600 font-bold">{{ $title }}</span>
    </div>

    <div class="flex items-start justify-between mb-5">
        <div class="flex items-center gap-2">
            <a href="{{ route($backRoute) }}" class="text-gray-400 hover:text-funaab-700 transition-colors">
                <i class="ti ti-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="pg-title">{{ $title }}</h1>
                @if($subtitle)
                    <p class="pg-sub">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        @if($addLabel)
            @if($addOnclick)
                <button onclick="{{ $addOnclick }}" class="btn-wf-primary">
                    <i class="ti ti-plus text-sm"></i> {{ $addLabel }}
                </button>
            @elseif($addRoute)
                @php try { $addHref = route($addRoute); } catch(\Exception $e) { $addHref = '#'; } @endphp
                <a href="{{ $addHref }}" class="btn-wf-primary">
                    <i class="ti ti-plus text-sm"></i> {{ $addLabel }}
                </a>
            @endif
        @endif
    </div>

    {{ $slot }}

</x-admin-layout>
