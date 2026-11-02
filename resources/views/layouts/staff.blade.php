<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'FUNAAB WorkForce') }} — Staff Portal</title>

    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans antialiased" x-data="staffLayout()" x-init="init()">

{{-- ══ TOPBAR ══ --}}
<header class="fixed top-0 left-0 right-0 z-30 h-14 bg-funaab-800 flex items-center justify-between px-4 shadow-md">

    {{-- Left: hamburger + brand --}}
    <div class="flex items-center gap-3 min-w-0">
        <button @click="sidebarOpen = !sidebarOpen; saveSidebar()"
                class="w-8 h-8 flex-shrink-0 flex items-center justify-center rounded-md
                       bg-white/10 hover:bg-white/20 text-white transition-colors lg:hidden">
            <i class="ti ti-menu-2 text-lg"></i>
        </button>
        <button @click="sidebarCollapsed = !sidebarCollapsed; saveSidebar()"
                class="w-8 h-8 flex-shrink-0 items-center justify-center rounded-md
                       bg-white/10 hover:bg-white/20 text-white transition-colors hidden lg:flex">
            <i class="ti ti-menu-2 text-lg"></i>
        </button>
        <a href="{{ route('staff.dashboard.index') }}" class="flex items-center gap-2.5 min-w-0">
            <img src="{{ asset('images/logo.png') }}" alt="FUNAAB"
                 class="w-9 h-9 rounded-full object-contain bg-white p-0.5 flex-shrink-0" />
            <div class="hidden sm:block min-w-0">
                <div class="text-white font-bold text-[15px] leading-tight tracking-wide whitespace-nowrap">
                    FUNAAB WorkForce
                </div>
                <div class="text-white/55 text-[9.5px] tracking-[0.07em] uppercase whitespace-nowrap">
                    Staff Self-Service Portal
                </div>
            </div>
        </a>
    </div>

    {{-- Right: notifications + user --}}
    <div class="flex items-center gap-3 flex-shrink-0">

        {{-- Notifications --}}
        <div class="relative cursor-pointer text-white/70 hover:text-white transition-colors">
            <i class="ti ti-bell text-lg"></i>
            <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-gold-500 rounded-full
                         text-[8px] font-bold flex items-center justify-center text-funaab-900">
                2
            </span>
        </div>

        {{-- User dropdown --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                    class="flex items-center gap-2 px-2 py-1 rounded-md hover:bg-white/10 transition-colors">
                @if(Auth::user()->profile?->avatar)
                    <img src="{{ asset('storage/avatars/'.Auth::user()->profile->avatar) }}"
                         class="w-7 h-7 rounded-full object-cover flex-shrink-0" />
                @else
                    <div class="w-7 h-7 rounded-full bg-gold-500 flex items-center justify-center
                                text-[11px] font-bold text-funaab-900 flex-shrink-0">
                        {{ strtoupper(substr(Auth::user()->surname ?? 'S', 0, 1)) }}{{ strtoupper(substr(Auth::user()->firstname ?? 'S', 0, 1)) }}
                    </div>
                @endif
                <span class="text-white/80 text-xs hidden sm:block">
                    {{ Auth::user()->surname ?? 'Staff' }}
                </span>
                <i class="ti ti-chevron-down text-xs text-white/50"></i>
            </button>

            <div x-show="open" @click.outside="open = false" x-transition
                 class="absolute right-0 top-full mt-1 w-52 bg-white rounded-lg shadow-xl
                        border border-gray-100 py-1 z-50">
                <div class="px-3 py-2.5 border-b border-gray-100">
                    <div class="text-xs font-bold text-gray-800">
                        {{ Auth::user()->surname ?? '' }}, {{ Auth::user()->firstname ?? '' }}
                    </div>
                    <div class="text-[10.5px] text-gray-400 mt-0.5 font-mono">
                        {{ Auth::user()->fileno ?? '' }}
                    </div>
                </div>
                <a href="{{ route('staff.profile.myprofile') }}"
                   class="flex items-center gap-2 px-3 py-2 text-xs text-gray-600 hover:bg-gray-50">
                    <i class="ti ti-user text-sm text-gray-400"></i> My Profile
                </a>
                <a href="{{ route('staff.profile.change_password') }}"
                   class="flex items-center gap-2 px-3 py-2 text-xs text-gray-600 hover:bg-gray-50">
                    <i class="ti ti-lock text-sm text-gray-400"></i> Change Password
                </a>
                <form action="{{ route('staff.auth.logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-2 w-full text-left px-3 py-2
                                   text-xs text-red-600 hover:bg-red-50">
                        <i class="ti ti-logout text-sm"></i> Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

{{-- ══ SIDEBAR (desktop) ══ --}}
<aside :class="sidebarCollapsed ? 'w-[52px]' : 'w-[220px]'"
       class="sidebar-transition fixed top-14 left-0 bottom-0 z-20 bg-white
              border-r border-gray-100 shadow-sm hidden lg:flex flex-col overflow-hidden">
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-2">

        <p class="sb-section-label" x-show="!sidebarCollapsed" x-transition.opacity>MY PORTAL</p>

        <a href="{{ route('staff.dashboard.index') }}"
           class="sb-item {{ request()->routeIs('staff.dashboard.index') ? 'active' : '' }}"
           :title="sidebarCollapsed ? 'Dashboard' : ''">
            <i class="ti ti-home text-base flex-shrink-0"></i>
            <span x-show="!sidebarCollapsed" x-transition.opacity class="truncate">Dashboard</span>
        </a>

        <a href="{{ route('staff.profile.myprofile') }}"
           class="sb-item {{ request()->routeIs('staff.profile.*') ? 'active' : '' }}"
           :title="sidebarCollapsed ? 'My Profile' : ''">
            <i class="ti ti-user text-base flex-shrink-0"></i>
            <span x-show="!sidebarCollapsed" x-transition.opacity class="truncate">My Profile</span>
        </a>

        <p class="sb-section-label mt-1" x-show="!sidebarCollapsed" x-transition.opacity>HR SERVICES</p>

        <a href="#"
           class="sb-item {{ request()->routeIs('staff.leave.*') ? 'active' : '' }}"
           :title="sidebarCollapsed ? 'Leave' : ''">
            <i class="ti ti-calendar-off text-base flex-shrink-0"></i>
            <span x-show="!sidebarCollapsed" x-transition.opacity class="truncate">Leave</span>
        </a>

        {{-- APER Form group --}}
        @php
            $authStaff    = \App\Models\Staff::where('staff_no', Auth::user()->fileno)->first();
            $isSupervisor = $authStaff && \App\Models\AperSupervisor::where('staff_id', $authStaff->id)->where('is_current', true)->exists();
            $isHod        = $authStaff && \App\Models\AperHod::where('staff_id', $authStaff->id)->where('is_current', true)->exists();
            $isChairman   = $authStaff && \App\Models\AperChairmanDapc::where('staff_id', $authStaff->id)->where('is_current', true)->exists();
        @endphp

        <div x-data="{ open: {{ request()->is('*/aper*', '*/supervisor*', '*/hod*', '*/chairman*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="sb-item w-full"
                    :title="sidebarCollapsed ? 'APER Form' : ''">
                <i class="ti ti-clipboard-text text-base flex-shrink-0"></i>
                <span x-show="!sidebarCollapsed" x-transition.opacity class="flex-1 text-left truncate">APER Form</span>
                <i class="ti ti-chevron-right text-xs text-gray-400 flex-shrink-0 transition-transform duration-200"
                   x-show="!sidebarCollapsed" x-transition.opacity
                   :class="open ? 'rotate-90' : ''"></i>
            </button>
            <div x-show="open && !sidebarCollapsed" x-collapse>

                <a href="{{ route('staff.aper.index') }}"
                   class="sb-item sub {{ request()->routeIs('staff.aper.index') ? 'active' : '' }}">
                    <i class="ti ti-forms text-sm flex-shrink-0"></i>
                    <span x-show="!sidebarCollapsed" x-transition.opacity>My APER Forms</span>
                </a>

                @if($isSupervisor)
                    <a href="{{ route('staff.supervisor.index') }}"
                       class="sb-item sub {{ request()->routeIs('staff.supervisor.*') ? 'active' : '' }}">
                        <i class="ti ti-user-check text-sm flex-shrink-0"></i>
                        <span x-show="!sidebarCollapsed" x-transition.opacity>Supervisor</span>
                    </a>
                @endif

                @if($isHod)
                    <a href="{{ route('staff.hod.index') }}"
                       class="sb-item sub {{ request()->routeIs('staff.hod.*') ? 'active' : '' }}">
                        <i class="ti ti-user-star text-sm flex-shrink-0"></i>
                        <span x-show="!sidebarCollapsed" x-transition.opacity>HOD</span>
                    </a>
                @endif

                @if($isChairman)
                    <a href="{{ route('staff.chairman.index') }}"
                       class="sb-item sub {{ request()->routeIs('staff.chairman.*') ? 'active' : '' }}">
                        <i class="ti ti-building-community text-sm flex-shrink-0"></i>
                        <span x-show="!sidebarCollapsed" x-transition.opacity>Chairman DAPC</span>
                    </a>
                @endif

            </div>
        </div>

        <a href="#"
           class="sb-item {{ request()->routeIs('staff.promotion.*') ? 'active' : '' }}"
           :title="sidebarCollapsed ? 'Promotion' : ''">
            <i class="ti ti-award text-base flex-shrink-0"></i>
            <span x-show="!sidebarCollapsed" x-transition.opacity class="truncate">Promotion</span>
        </a>



    </nav>

    {{-- Sign out --}}
    <div class="border-t border-gray-100 py-2 flex-shrink-0">
        <form action="{{ route('staff.auth.logout') }}" method="POST">
            @csrf
            <button type="submit"
                    class="sb-item w-full text-red-500 hover:bg-red-50 hover:text-red-600 border-transparent"
                    :title="sidebarCollapsed ? 'Sign Out' : ''">
                <i class="ti ti-logout text-base flex-shrink-0"></i>
                <span x-show="!sidebarCollapsed" x-transition.opacity>Sign Out</span>
            </button>
        </form>
    </div>
</aside>

{{-- Mobile sidebar overlay --}}
<div x-show="sidebarOpen" @click="sidebarOpen = false"
     class="fixed inset-0 bg-black/40 z-10 lg:hidden" x-transition.opacity></div>

{{-- Mobile sidebar --}}
<aside x-show="sidebarOpen" x-transition:enter="transition transform duration-200"
       x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
       x-transition:leave="transition transform duration-200"
       x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
       class="fixed top-14 left-0 bottom-0 z-20 w-[220px] bg-white border-r border-gray-100
              shadow-lg flex flex-col overflow-hidden lg:hidden">
    <nav class="flex-1 overflow-y-auto py-2">
        <p class="sb-section-label">MY PORTAL</p>
        <a href="{{ route('staff.dashboard.index') }}" class="sb-item {{ request()->routeIs('staff.dashboard.index') ? 'active' : '' }}"><i class="ti ti-home text-base flex-shrink-0"></i><span class="truncate">Dashboard</span></a>
        <a href="{{ route('staff.profile.myprofile') }}" class="sb-item {{ request()->routeIs('staff.profile.*') ? 'active' : '' }}"><i class="ti ti-user text-base flex-shrink-0"></i><span class="truncate">My Profile</span></a>
        <p class="sb-section-label mt-1">HR SERVICES</p>
        <a href="#" class="sb-item"><i class="ti ti-calendar-off text-base flex-shrink-0"></i><span class="truncate">Leave</span></a>

        {{-- Mobile APER dropdown --}}
        <div x-data="{ open: {{ request()->is('*/aper*', '*/supervisor*', '*/hod*', '*/chairman*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="sb-item w-full">
                <i class="ti ti-clipboard-text text-base flex-shrink-0"></i>
                <span class="flex-1 text-left truncate">APER Form</span>
                <i class="ti ti-chevron-right text-xs text-gray-400 flex-shrink-0 transition-transform duration-200"
                   :class="open ? 'rotate-90' : ''"></i>
            </button>
            <div x-show="open" x-collapse>
                <a href="{{ route('staff.aper.index') }}" class="sb-item sub {{ request()->routeIs('staff.aper.index') ? 'active' : '' }}">
                    <i class="ti ti-forms text-sm flex-shrink-0"></i><span>My APER Forms</span>
                </a>
                @if($isSupervisor)
                    <a href="{{ route('staff.supervisor.index') }}" class="sb-item sub {{ request()->routeIs('staff.supervisor.*') ? 'active' : '' }}">
                        <i class="ti ti-user-check text-sm flex-shrink-0"></i><span>Supervisor</span>
                    </a>
                @endif
                @if($isHod)
                    <a href="{{ route('staff.hod.index') }}" class="sb-item sub {{ request()->routeIs('staff.hod.*') ? 'active' : '' }}">
                        <i class="ti ti-user-star text-sm flex-shrink-0"></i><span>HOD</span>
                    </a>
                @endif
                @if($isChairman)
                    <a href="{{ route('staff.chairman.index') }}" class="sb-item sub {{ request()->routeIs('staff.chairman.*') ? 'active' : '' }}">
                        <i class="ti ti-building-community text-sm flex-shrink-0"></i><span>Chairman DAPC</span>
                    </a>
                @endif
            </div>
        </div>

        <a href="#" class="sb-item"><i class="ti ti-award text-base flex-shrink-0"></i><span class="truncate">Promotion</span></a>
    </nav>
    <div class="border-t border-gray-100 py-2">
        <form action="{{ route('staff.auth.logout') }}" method="POST">
            @csrf
            <button type="submit" class="sb-item w-full text-red-500 hover:bg-red-50">
                <i class="ti ti-logout text-base flex-shrink-0"></i><span>Sign Out</span>
            </button>
        </form>
    </div>
</aside>

{{-- ══ MAIN CONTENT ══ --}}
<main :class="sidebarCollapsed ? 'lg:ml-[52px]' : 'lg:ml-[220px]'"
      class="sidebar-transition mt-14 min-h-[calc(100vh-3.5rem)] flex flex-col bg-gray-100">

    {{-- Flash messages --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="mx-5 mt-4 flex items-center gap-2 px-4 py-3 bg-funaab-50 border
                    border-funaab-200 rounded-lg text-funaab-800 text-sm shadow-sm">
            <i class="ti ti-circle-check text-funaab-600 flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
            <button @click="show = false" class="ml-auto text-funaab-400 hover:text-funaab-700">
                <i class="ti ti-x text-xs"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="mx-5 mt-4 flex items-center gap-2 px-4 py-3 bg-red-50 border
                    border-red-200 rounded-lg text-red-700 text-sm shadow-sm">
            <i class="ti ti-alert-circle text-red-500 flex-shrink-0"></i>
            <span>{{ session('error') }}</span>
            <button @click="show = false" class="ml-auto text-red-400 hover:text-red-600">
                <i class="ti ti-x text-xs"></i>
            </button>
        </div>
    @endif

    <div class="flex-1 p-5">
        {{ $slot }}
    </div>

    <footer class="px-5 py-3 border-t border-gray-200 bg-white text-[11px] text-gray-400
                   flex justify-between items-center flex-shrink-0">
        <span>&copy; {{ date('Y') }} Federal University of Agriculture, Abeokuta.</span>
        <span>FUNAAB WorkForce — Staff Portal</span>
    </footer>
</main>

<script>
function staffLayout() {
    return {
        sidebarCollapsed: false,
        sidebarOpen: false,

        init() {
            const saved = localStorage.getItem('wf_staff_sidebar');
            if (saved !== null) this.sidebarCollapsed = saved === 'true';
        },

        saveSidebar() {
            localStorage.setItem('wf_staff_sidebar', this.sidebarCollapsed);
        }
    }
}
</script>

</body>
</html>