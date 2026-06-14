<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} — FUNAAB WorkForce</title>

    {{-- Tabler Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />

    {{-- Vite: Tailwind + App JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans antialiased text-gray-700"
      x-data="adminLayout()"
      x-init="init()">

{{-- ═══════════════════════════════════════════ --}}
{{-- TOPBAR                                       --}}
{{-- ═══════════════════════════════════════════ --}}
<header class="fixed top-0 left-0 right-0 z-30 h-14 bg-funaab-800 flex items-center justify-between px-4 shadow-md">

    {{-- Left: toggle + brand --}}
    <div class="flex items-center gap-3 min-w-0">

        {{-- Sidebar toggle --}}
        <button @click="toggleSidebar()"
                class="w-8 h-8 flex items-center justify-center rounded-md bg-white/10 hover:bg-white/20 text-white transition-colors flex-shrink-0">
            <i class="ti ti-menu-2 text-lg"></i>
        </button>

        {{-- Logo --}}
        <a href="{{ route('admin.dashboard.index') }}" class="flex items-center gap-2.5 min-w-0">
            <img src="{{ asset('images/logo.png') }}"
                 alt="FUNAAB Logo"
                 class="w-9 h-9 rounded-full object-contain bg-white p-0.5 flex-shrink-0" />
            <div class="min-w-0 hidden sm:block">
                <div class="text-white font-bold text-[15px] leading-tight tracking-wide whitespace-nowrap">
                    FUNAAB WorkForce
                </div>
                <div class="text-white/55 text-[9.5px] tracking-[0.07em] uppercase whitespace-nowrap">
                    Staff Resource Management Service
                </div>
            </div>
        </a>
    </div>

    {{-- Right: portal switcher + notifications + user --}}
    <div class="flex items-center gap-3 flex-shrink-0">

        {{-- Portal switcher --}}
        <div class="hidden md:flex items-center bg-black/25 rounded-md p-0.5 gap-0.5">
            <span class="topbar-tab active">Admin Portal</span>
            <a href="{{ route('welcome') }}" class="topbar-tab">Staff Portal</a>
        </div>

        {{-- Notification bell --}}
        <div class="relative cursor-pointer text-white/70 hover:text-white transition-colors">
            <i class="ti ti-bell text-lg"></i>
            <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-gold-500 rounded-full text-[8px] font-bold flex items-center justify-center text-funaab-900">3</span>
        </div>

        {{-- User menu --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                    class="flex items-center gap-2 px-2 py-1 rounded-md hover:bg-white/10 transition-colors">
                <div class="w-7 h-7 rounded-full bg-gold-500 flex items-center justify-center text-[10.5px] font-bold text-funaab-900">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 2)) }}
                </div>
                <span class="text-white/80 text-xs hidden sm:block">{{ Auth::user()->name ?? 'Admin' }}</span>
                <i class="ti ti-chevron-down text-xs text-white/50"></i>
            </button>

            {{-- Dropdown --}}
            <div x-show="open"
                 @click.outside="open = false"
                 x-transition
                 class="absolute right-0 top-full mt-1 w-44 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50">
                <div class="px-3 py-2 border-b border-gray-100">
                    <div class="text-xs font-bold text-gray-700">{{ Auth::user()->name ?? 'Administrator' }}</div>
                    <div class="text-[10.5px] text-gray-400">{{ Auth::user()->email ?? '' }}</div>
                </div>
                <a href="#" class="flex items-center gap-2 px-3 py-2 text-xs text-gray-600 hover:bg-gray-50">
                    <i class="ti ti-settings text-sm"></i> Account Settings
                </a>
                <form action="{{ route('admin.auth.logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-2 w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-red-50">
                        <i class="ti ti-logout text-sm"></i> Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>


{{-- ═══════════════════════════════════════════ --}}
{{-- SIDEBAR                                      --}}
{{-- ═══════════════════════════════════════════ --}}
<aside id="sidebar"
       :class="collapsed ? 'w-[52px]' : 'w-[230px]'"
       class="sidebar-transition fixed top-14 left-0 bottom-0 z-20 bg-white border-r border-gray-100 shadow-sm flex flex-col overflow-hidden">

    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-2">

        {{-- ── OVERVIEW ── --}}
        <div class="sb-section-label" x-show="!collapsed">OVERVIEW</div>

        <x-sb-item route="admin.dashboard.index" icon="ti-layout-dashboard" label="Dashboard" :collapsed="false" />
        <x-sb-item route="admin.staff.index" icon="ti-users" label="Staff Management" badge="1,247" :collapsed="false" />

        {{-- ── ORGANISATION ── --}}
        <div class="sb-section-label mt-1" x-show="!collapsed">ORGANISATION</div>

        <x-sb-item route="admin.departments.index" icon="ti-building" label="Departments" :collapsed="false" />
        <x-sb-item route="admin.divisions.index"   icon="ti-hierarchy-2" label="Divisions" :collapsed="false" />

        {{-- ── HR OPERATIONS ── --}}
        <div class="sb-section-label mt-1" x-show="!collapsed">HR OPERATIONS</div>

        {{-- Leave Management group --}}
        <x-sb-group label="Leave Management" icon="ti-calendar-off" group="leave" :collapsed="false">
            <x-sb-sub-item route="admin.leave_types.index"     icon="ti-tags"           label="Leave Types" />
            <x-sb-sub-item route="admin.leaves.index"          icon="ti-list-details"   label="Leave Records" />
            <x-sb-sub-item route="admin.leave_applications.index" icon="ti-clipboard-text" label="Applications" />
        </x-sb-group>

        {{-- Progression group --}}
        <x-sb-group label="Progression" icon="ti-arrow-up-circle" group="progression" :collapsed="false">
            <x-sb-sub-item route="admin.promotions.index"       icon="ti-award"         label="Promotion" />
            <x-sb-sub-item route="admin.step_increments.index"  icon="ti-step-into"     label="Step Increment" />
            <x-sb-sub-item route="admin.cadres.index"           icon="ti-hierarchy-2"   label="Cadre" />
            <x-sb-sub-item route="admin.cadre_ranks.index"      icon="ti-certificate"   label="Rank" />
            <x-sb-sub-item route="admin.designations.index"     icon="ti-id-badge"      label="Designation" />
            <x-sb-sub-item route="admin.salary_structures.index" icon="ti-receipt"      label="Salary Structure" />
            <x-sb-sub-item route="admin.salary_levels.index"    icon="ti-chart-bar"     label="Salary Level" />
            <x-sb-sub-item route="admin.salary_steps.index"     icon="ti-stairs"        label="Salary Step" />
        </x-sb-group>

        <x-sb-item route="admin.aper.index" icon="ti-file-certificate" label="APER Evaluation" :collapsed="false" />

        {{-- ── CONFIGURATION ── --}}
        <div class="sb-section-label mt-1" x-show="!collapsed">CONFIGURATION</div>

        <x-sb-item route="admin.settings.index" icon="ti-settings"  label="Settings" :collapsed="false" />
        <x-sb-item route="admin.analytics.index" icon="ti-chart-bar" label="Analytics" :collapsed="false" />

    </nav>

    {{-- Sign out at bottom --}}
    <div class="border-t border-gray-100 py-2">
        <form action="{{ route('admin.auth.logout') }}" method="POST">
            @csrf
            <button type="submit"
                    class="sb-item w-full text-red-500 hover:bg-red-50 hover:text-red-600"
                    :title="collapsed ? 'Sign Out' : ''">
                <i class="ti ti-logout text-base flex-shrink-0"></i>
                <span class="sb-text" x-show="!collapsed" x-transition.opacity>Sign Out</span>
            </button>
        </form>
    </div>
</aside>


{{-- ═══════════════════════════════════════════ --}}
{{-- MAIN CONTENT                                 --}}
{{-- ═══════════════════════════════════════════ --}}
<main :class="collapsed ? 'ml-[52px]' : 'ml-[230px]'"
      class="sidebar-transition mt-14 min-h-[calc(100vh-3.5rem)] flex flex-col">

    {{-- Flash messages --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="mx-5 mt-4 flex items-center gap-2 px-4 py-3 bg-funaab-50 border border-funaab-200 rounded-lg text-funaab-800 text-sm">
            <i class="ti ti-circle-check text-funaab-600 text-base"></i>
            {{ session('success') }}
            <button @click="show = false" class="ml-auto text-funaab-400 hover:text-funaab-600"><i class="ti ti-x text-xs"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="mx-5 mt-4 flex items-center gap-2 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            <i class="ti ti-alert-circle text-red-500 text-base"></i>
            {{ session('error') }}
            <button @click="show = false" class="ml-auto text-red-400 hover:text-red-600"><i class="ti ti-x text-xs"></i></button>
        </div>
    @endif

    {{-- Page content --}}
    <div class="flex-1 p-5">
        {{ $slot }}
    </div>

    {{-- Footer --}}
    <footer class="px-5 py-3 border-t border-gray-100 text-[11px] text-gray-400 flex justify-between items-center">
        <span>© {{ date('Y') }} Federal University of Agriculture, Abeokuta. All rights reserved.</span>
        <span>FUNAAB WorkForce v1.0</span>
    </footer>
</main>


{{-- Alpine.js component --}}
<script>
function adminLayout() {
    return {
        collapsed: false,

        init() {
            // Restore sidebar state from localStorage
            const saved = localStorage.getItem('wf_sidebar_collapsed');
            if (saved !== null) {
                this.collapsed = saved === 'true';
            }
        },

        toggleSidebar() {
            this.collapsed = !this.collapsed;
            localStorage.setItem('wf_sidebar_collapsed', this.collapsed);
        },
    }
}
</script>

</body>
</html>
