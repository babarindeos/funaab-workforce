<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'FUNAAB WorkForce') }}</title>

    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">

    {{-- Tabler Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />

    {{-- Font Awesome (kept for backward compat) --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

    {{-- jQuery (kept for existing scripts) --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none !important}</style>
</head>

<body class="bg-gray-100 font-sans antialiased text-gray-700"
      x-data="adminLayout()"
      x-init="init()">

{{-- ══════════════════════════════════════════════════ --}}
{{--  TOPBAR                                            --}}
{{-- ══════════════════════════════════════════════════ --}}
<header class="fixed top-0 left-0 right-0 z-30 h-14 bg-funaab-800 flex items-center justify-between px-4 shadow-md gap-4">

    {{-- Left: hamburger + brand --}}
    <div class="flex items-center gap-3 min-w-0">

        <button @click="toggleSidebar()"
                class="w-8 h-8 flex-shrink-0 flex items-center justify-center rounded-md
                       bg-white/10 hover:bg-white/20 text-white transition-colors">
            <i class="ti ti-menu-2 text-lg"></i>
        </button>

        <a href="{{ route('admin.dashboard.index') }}" class="flex items-center gap-2.5 min-w-0">
            <img src="{{ asset('images/logo.png') }}"
                 alt="FUNAAB Logo"
                 class="w-9 h-9 rounded-full object-contain bg-white p-0.5 flex-shrink-0" />
            <div class="hidden sm:block min-w-0">
                <div class="text-white font-bold text-[15px] leading-tight tracking-wide whitespace-nowrap">
                    FUNAAB WorkForce
                </div>
                <div class="text-white/55 text-[9.5px] tracking-[0.07em] uppercase whitespace-nowrap">
                    Staff Resource Management Service
                </div>
            </div>
        </a>
    </div>

    {{-- Right: portal switcher + bell + user --}}
    <div class="flex items-center gap-3 flex-shrink-0">

        {{-- Portal switcher --}}
        <div class="hidden md:flex items-center bg-black/25 rounded-md p-0.5 gap-0.5">
            <span class="topbar-tab active cursor-default">Admin Portal</span>
            <a href="{{ route('welcome') }}" class="topbar-tab">Staff Portal</a>
        </div>

        {{-- Notification bell --}}
        <div class="relative cursor-pointer text-white/70 hover:text-white transition-colors">
            <i class="ti ti-bell text-lg"></i>
            <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-gold-500 rounded-full
                         text-[8px] font-bold flex items-center justify-center text-funaab-900">
                3
            </span>
        </div>

        {{-- User dropdown --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                    class="flex items-center gap-2 px-2 py-1 rounded-md hover:bg-white/10 transition-colors">
                <div class="w-7 h-7 rounded-full bg-gold-500 flex items-center justify-center
                            text-[11px] font-bold text-funaab-900">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 2)) }}
                </div>
                <span class="text-white/80 text-xs hidden sm:block">
                    {{ Auth::user()->name ?? 'Admin' }}
                </span>
                <i class="ti ti-chevron-down text-xs text-white/50"></i>
            </button>

            <div x-show="open"
                 @click.outside="open = false"
                 x-transition
                 class="absolute right-0 top-full mt-1 w-48 bg-white rounded-lg shadow-xl
                        border border-gray-100 py-1 z-50">
                <div class="px-3 py-2.5 border-b border-gray-100">
                    <div class="text-xs font-bold text-gray-800">{{ Auth::user()->name ?? 'Administrator' }}</div>
                    <div class="text-[10.5px] text-gray-400 mt-0.5">{{ Auth::user()->email ?? '' }}</div>
                </div>
                <a href="#" class="flex items-center gap-2 px-3 py-2 text-xs text-gray-600 hover:bg-gray-50">
                    <i class="ti ti-settings text-sm text-gray-400"></i> Account Settings
                </a>
                <form action="{{ route('admin.auth.logout') }}" method="POST">
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


{{-- ══════════════════════════════════════════════════ --}}
{{--  SIDEBAR                                           --}}
{{-- ══════════════════════════════════════════════════ --}}
<aside :class="collapsed ? 'w-[52px]' : 'w-[230px]'"
       class="sidebar-transition fixed top-14 left-0 bottom-0 z-20
              bg-white border-r border-gray-100 shadow-sm flex flex-col overflow-hidden">

    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-2 scrollbar-thin">

        {{-- OVERVIEW --}}
        <p class="sb-section-label" x-show="!collapsed" x-transition.opacity>OVERVIEW</p>

        @php
            // Guarded: a layout can be rendered more than once per request
            // (e.g. error pages), and PHP cannot declare a function twice.
            if (!function_exists('sbRoute')) {
                function sbRoute($name) {
                    try { return route($name); } catch(\Exception $e) { return '#'; }
                }
            }
            if (!function_exists('sbActive')) {
                function sbActive($name) {
                    try { return request()->routeIs($name); } catch(\Exception $e) { return false; }
                }
            }
        @endphp

        <a href="{{ sbRoute('admin.dashboard.index') }}"
           class="sb-item {{ sbActive('admin.dashboard.index') ? 'active' : '' }}"
           :title="collapsed ? 'Dashboard' : ''">
            <i class="ti ti-layout-dashboard text-base flex-shrink-0"></i>
            <span x-show="!collapsed" x-transition.opacity class="truncate">Dashboard</span>
        </a>

        <a href="{{ sbRoute('admin.staff.index') }}"
           class="sb-item {{ sbActive('admin.staff.index') ? 'active' : '' }}"
           :title="collapsed ? 'Staff Management' : ''">
            <i class="ti ti-users text-base flex-shrink-0"></i>
            <span x-show="!collapsed" x-transition.opacity class="flex-1 truncate">Staff Management</span>
            @php
                $sbInactive   = \App\Models\StaffStatus::whereIn('status', config('workforce.inactive_staff_statuses', []))->pluck('id');
                $sbStaffCount = \App\Models\Staff::when($sbInactive->isNotEmpty(), fn ($q) => $q->where(fn ($w) => $w->whereNull('staff_status_id')->orWhereNotIn('staff_status_id', $sbInactive)))->count();
            @endphp
            <span x-show="!collapsed" x-transition.opacity
                  class="text-[9.5px] bg-funaab-100 text-funaab-800 font-bold px-1.5 py-0.5 rounded-full flex-shrink-0">
                {{ number_format($sbStaffCount) }}
            </span>
        </a>

        {{-- ORGANISATION --}}
        <p class="sb-section-label mt-1" x-show="!collapsed" x-transition.opacity>ORGANISATION</p>

        <a href="{{ sbRoute('admin.departments.index') }}"
           class="sb-item {{ sbActive('admin.departments.index') ? 'active' : '' }}"
           :title="collapsed ? 'Departments' : ''">
            <i class="ti ti-building text-base flex-shrink-0"></i>
            <span x-show="!collapsed" x-transition.opacity class="truncate">Departments</span>
        </a>

        <a href="{{ sbRoute('admin.divisions.index') }}"
           class="sb-item {{ sbActive('admin.divisions.index') ? 'active' : '' }}"
           :title="collapsed ? 'Divisions' : ''">
            <i class="ti ti-hierarchy-2 text-base flex-shrink-0"></i>
            <span x-show="!collapsed" x-transition.opacity class="truncate">Divisions</span>
        </a>

        {{-- HR OPERATIONS --}}
        <p class="sb-section-label mt-1" x-show="!collapsed" x-transition.opacity>HR OPERATIONS</p>

        <a href="{{ sbRoute('admin.nominal_roll.index') }}"
           class="sb-item {{ sbActive('admin.nominal_roll.*') ? 'active' : '' }}"
           :title="collapsed ? 'Nominal Roll Import' : ''">
            <i class="ti ti-file-spreadsheet text-base flex-shrink-0"></i>
            <span x-show="!collapsed" x-transition.opacity class="truncate">Nominal Roll Import</span>
        </a>

        <a href="{{ sbRoute('admin.login_mails.index') }}"
           class="sb-item {{ sbActive('admin.login_mails.*') ? 'active' : '' }}"
           :title="collapsed ? 'Login Mails' : ''">
            <i class="ti ti-mail-forward text-base flex-shrink-0"></i>
            <span x-show="!collapsed" x-transition.opacity class="truncate">Login Mails</span>
        </a>

        @php $pendingDocs = \App\Models\StaffDocument::where('status', 'pending')->count(); @endphp
        <a href="{{ sbRoute('admin.staff_documents.index') }}"
           class="sb-item {{ sbActive('admin.staff_documents.*') || sbActive('admin.document_setup.*') ? 'active' : '' }}"
           :title="collapsed ? 'Staff Documents' : ''">
            <i class="ti ti-folders text-base flex-shrink-0"></i>
            <span x-show="!collapsed" x-transition.opacity class="truncate flex-1">Staff Documents</span>
            @if($pendingDocs)
                <span x-show="!collapsed" class="ml-auto text-[10px] font-bold bg-amber-100 text-amber-700 rounded-full px-1.5">{{ $pendingDocs }}</span>
            @endif
        </a>

        {{-- Leave Management group --}}
        <div x-data="{ open: {{ request()->is('*/leave*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="sb-item w-full"
                    :title="collapsed ? 'Leave Management' : ''">
                <i class="ti ti-calendar-off text-base flex-shrink-0"></i>
                <span x-show="!collapsed" x-transition.opacity class="flex-1 text-left truncate">Leave Management</span>
                <i class="ti ti-chevron-right text-xs text-gray-400 flex-shrink-0 transition-transform duration-200"
                   x-show="!collapsed" x-transition.opacity
                   :class="open ? 'rotate-90' : ''"></i>
            </button>
            <div x-show="open && !collapsed" x-collapse>
                <a href="{{ sbRoute('admin.leave_types.index') }}"   class="sb-item sub {{ sbActive('admin.leave_types.index') ? 'active' : '' }}"><i class="ti ti-tags text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Leave Types</span></a>
                <a href="{{ sbRoute('admin.leaves.index') }}"        class="sb-item sub {{ sbActive('admin.leaves.index') ? 'active' : '' }}"><i class="ti ti-list-details text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Leave Records</span></a>
                <a href="{{ sbRoute('admin.leave_applications.index') }}" class="sb-item sub {{ sbActive('admin.leave_applications.index') ? 'active' : '' }}"><i class="ti ti-clipboard-text text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Applications</span></a>
            </div>
        </div>

        {{-- Progression group --}}
        <div x-data="{ open: {{ request()->is('*/promotion*','*/step_increment*','*/cadre*','*/rank*','*/designation*','*/salary*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="sb-item w-full"
                    :title="collapsed ? 'Progression' : ''">
                <i class="ti ti-arrow-up-circle text-base flex-shrink-0"></i>
                <span x-show="!collapsed" x-transition.opacity class="flex-1 text-left truncate">Progression</span>
                <i class="ti ti-chevron-right text-xs text-gray-400 flex-shrink-0 transition-transform duration-200"
                   x-show="!collapsed" x-transition.opacity
                   :class="open ? 'rotate-90' : ''"></i>
            </button>
            <div x-show="open && !collapsed" x-collapse>
                <a href="{{ sbRoute('admin.promotions.index') }}"        class="sb-item sub {{ sbActive('admin.promotions.index') ? 'active' : '' }}"><i class="ti ti-award text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Promotion</span></a>
                <a href="{{ sbRoute('admin.step_increments.index') }}"   class="sb-item sub {{ sbActive('admin.step_increments.index') ? 'active' : '' }}"><i class="ti ti-step-into text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Step Increment</span></a>
                <a href="{{ sbRoute('admin.cadres.index') }}"            class="sb-item sub {{ sbActive('admin.cadres.index') ? 'active' : '' }}"><i class="ti ti-hierarchy-2 text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Cadre</span></a>
                <a href="{{ sbRoute('admin.cadre_ranks.index') }}"       class="sb-item sub {{ sbActive('admin.cadre_ranks.index') ? 'active' : '' }}"><i class="ti ti-certificate text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Rank</span></a>
                <a href="{{ sbRoute('admin.designations.index') }}"      class="sb-item sub {{ sbActive('admin.designations.index') ? 'active' : '' }}"><i class="ti ti-id-badge text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Designation</span></a>
                <a href="{{ sbRoute('admin.salary_structures.index') }}" class="sb-item sub {{ sbActive('admin.salary_structures.index') ? 'active' : '' }}"><i class="ti ti-receipt text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Salary Structure</span></a>
                <a href="{{ sbRoute('admin.salary_levels.index') }}"     class="sb-item sub {{ sbActive('admin.salary_levels.index') ? 'active' : '' }}"><i class="ti ti-chart-bar text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Salary Level</span></a>
                <a href="{{ sbRoute('admin.salary_steps.index') }}"      class="sb-item sub {{ sbActive('admin.salary_steps.index') ? 'active' : '' }}"><i class="ti ti-stairs text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Salary Step</span></a>
            </div>
        </div>

        {{--  
            <a href="{{ sbRoute('admin.aper.index') }}"
            class="sb-item {{ sbActive('admin.aper.index') ? 'active' : '' }}"
            :title="collapsed ? 'APER Evaluation' : ''">
                <i class="ti ti-file-certificate text-base flex-shrink-0"></i>
                <span x-show="!collapsed" x-transition.opacity class="truncate">APER Evaluation</span>
            </a>
        --}}

        {{-- APER Evaluation group --}}
        <div x-data="{ open: {{ request()->is('*/aper*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="sb-item w-full"
                    :title="collapsed ? 'APER Evaluation' : ''">
                <i class="ti ti-clipboard-text text-base flex-shrink-0"></i>
                <span x-show="!collapsed" x-transition.opacity class="flex-1 text-left truncate">APER Evaluation</span>
                <i class="ti ti-chevron-right text-xs text-gray-400 flex-shrink-0 transition-transform duration-200"
                x-show="!collapsed" x-transition.opacity
                :class="open ? 'rotate-90' : ''"></i>
            </button>
            <div x-show="open && !collapsed" x-collapse>
                <a href="{{ sbRoute('admin.aper.overview') }}"           class="sb-item sub {{ sbActive('admin.aper.overview*') ? 'active' : '' }}"><i class="ti ti-chart-bar text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Overview</span></a>
                <a href="{{ sbRoute('admin.aper.index') }}"              class="sb-item sub {{ sbActive('admin.aper.index') ? 'active' : '' }}"><i class="ti ti-forms text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>APER Forms</span></a>
                <a href="{{ sbRoute('admin.aper.supervisors.index') }}"  class="sb-item sub {{ sbActive('admin.aper.supervisors.*') ? 'active' : '' }}"><i class="ti ti-user-check text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Supervisors</span></a>
                <a href="{{ sbRoute('admin.aper.hods.index') }}"         class="sb-item sub {{ sbActive('admin.aper.hods.*') ? 'active' : '' }}"><i class="ti ti-user-star text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>HOD</span></a>
                <a href="{{ sbRoute('admin.aper.chairman_dapc.index') }}" class="sb-item sub {{ sbActive('admin.aper.chairman_dapc.*') ? 'active' : '' }}"><i class="ti ti-building-community text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Chairman DAPC</span></a>
            </div>
        </div>


        {{-- CONFIGURATION --}}
        <p class="sb-section-label mt-1" x-show="!collapsed" x-transition.opacity>CONFIGURATION</p>

        {{-- Settings group --}}
        <div x-data="{ open: {{ request()->is('*/division_types*','*/contact_types*','*/gender*','*/marital*','*/geo_pol*','*/states*','*/lgas*','*/employment_types*','*/staff_categories*','*/staff_titles*','*/staff_statuses*','*/degrees*','*/classes*','*/deployment_posts*','*/deployments*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="sb-item w-full"
                    :title="collapsed ? 'Settings' : ''">
                <i class="ti ti-settings text-base flex-shrink-0"></i>
                <span x-show="!collapsed" x-transition.opacity class="flex-1 text-left truncate">Settings</span>
                <i class="ti ti-chevron-right text-xs text-gray-400 flex-shrink-0 transition-transform duration-200"
                   x-show="!collapsed" x-transition.opacity
                   :class="open ? 'rotate-90' : ''"></i>
            </button>
            <div x-show="open && !collapsed" x-collapse>
                <a href="{{ sbRoute('admin.employment_types.index') }}"  class="sb-item sub {{ sbActive('admin.employment_types.index') ? 'active' : '' }}"><i class="ti ti-briefcase text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Employment Types</span></a>
                <a href="{{ sbRoute('admin.staff_categories.index') }}"  class="sb-item sub {{ sbActive('admin.staff_categories.index') ? 'active' : '' }}"><i class="ti ti-category text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Staff Categories</span></a>
                <a href="{{ sbRoute('admin.staff_titles.index') }}"      class="sb-item sub {{ sbActive('admin.staff_titles.index') ? 'active' : '' }}"><i class="ti ti-tag text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Staff Titles</span></a>
                <a href="{{ sbRoute('admin.staff_statuses.index') }}"    class="sb-item sub {{ sbActive('admin.staff_statuses.index') ? 'active' : '' }}"><i class="ti ti-toggle-right text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Staff Statuses</span></a>
                <a href="{{ sbRoute('admin.contact_types.index') }}"     class="sb-item sub {{ sbActive('admin.contact_types.index') ? 'active' : '' }}"><i class="ti ti-phone text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Contact Types</span></a>
                <a href="{{ sbRoute('admin.gender.index') }}"            class="sb-item sub {{ sbActive('admin.gender.index') ? 'active' : '' }}"><i class="ti ti-gender-bigender text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Gender</span></a>
                <a href="{{ sbRoute('admin.marital_statuses.index') }}"  class="sb-item sub {{ sbActive('admin.marital_statuses.index') ? 'active' : '' }}"><i class="ti ti-heart text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Marital Status</span></a>
                <a href="{{ sbRoute('admin.geo_pol_zones.index') }}"     class="sb-item sub {{ sbActive('admin.geo_pol_zones.index') ? 'active' : '' }}"><i class="ti ti-map text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Geo-Pol Zones</span></a>
                <a href="{{ sbRoute('admin.states.index') }}"            class="sb-item sub {{ sbActive('admin.states.index') ? 'active' : '' }}"><i class="ti ti-map-pin text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>States</span></a>
                <a href="{{ sbRoute('admin.lgas.index') }}"              class="sb-item sub {{ sbActive('admin.lgas.index') ? 'active' : '' }}"><i class="ti ti-location text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>LGAs</span></a>
                <a href="{{ sbRoute('admin.senatorial_districts.index') }}" class="sb-item sub {{ sbActive('admin.senatorial_districts.index') ? 'active' : '' }}"><i class="ti ti-map-pins text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Senatorial Districts</span></a>
                <a href="{{ sbRoute('admin.degrees.index') }}"           class="sb-item sub {{ sbActive('admin.degrees.index') ? 'active' : '' }}"><i class="ti ti-school text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Degrees</span></a>
                <a href="{{ sbRoute('admin.classes_of_degrees.index') }}" class="sb-item sub {{ sbActive('admin.classes_of_degrees.index') ? 'active' : '' }}"><i class="ti ti-certificate-2 text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Classes of Degree</span></a>
                <a href="{{ sbRoute('admin.division_types.index') }}"    class="sb-item sub {{ sbActive('admin.division_types.index') ? 'active' : '' }}"><i class="ti ti-layout-distribute-vertical text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Division Types</span></a>
                <a href="{{ sbRoute('admin.deployment_posts.index') }}"  class="sb-item sub {{ sbActive('admin.deployment_posts.index') ? 'active' : '' }}"><i class="ti ti-map-2 text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Deployment Posts</span></a>
                <a href="{{ sbRoute('admin.deployments.index') }}"       class="sb-item sub {{ sbActive('admin.deployments.index') ? 'active' : '' }}"><i class="ti ti-arrows-exchange text-sm flex-shrink-0"></i><span x-show="!collapsed" x-transition.opacity>Deployments</span></a>
            </div>
        </div>

        <a href="{{ sbRoute('admin.analytics.index') }}"
           class="sb-item {{ sbActive('admin.analytics.index') ? 'active' : '' }}"
           :title="collapsed ? 'Analytics' : ''">
            <i class="ti ti-chart-bar text-base flex-shrink-0"></i>
            <span x-show="!collapsed" x-transition.opacity class="truncate">Analytics</span>
        </a>

    </nav>

    {{-- Sign out --}}
    <div class="border-t border-gray-100 py-2 flex-shrink-0">
        <form action="{{ route('admin.auth.logout') }}" method="POST">
            @csrf
            <button type="submit"
                    class="sb-item w-full text-red-500 hover:bg-red-50 hover:text-red-600 border-transparent"
                    :title="collapsed ? 'Sign Out' : ''">
                <i class="ti ti-logout text-base flex-shrink-0"></i>
                <span x-show="!collapsed" x-transition.opacity>Sign Out</span>
            </button>
        </form>
    </div>
</aside>


{{-- ══════════════════════════════════════════════════ --}}
{{--  MAIN CONTENT                                      --}}
{{-- ══════════════════════════════════════════════════ --}}
<main :class="collapsed ? 'ml-[52px]' : 'ml-[230px]'"
      class="sidebar-transition mt-14 min-h-[calc(100vh-3.5rem)] flex flex-col bg-gray-100">

    {{-- Flash messages --}}
    @if(session('success'))
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 4000)"
             class="mx-5 mt-4 flex items-center gap-2 px-4 py-3 bg-funaab-50 border
                    border-funaab-200 rounded-lg text-funaab-800 text-sm shadow-sm">
            <i class="ti ti-circle-check text-funaab-600 text-base flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
            <button @click="show = false" class="ml-auto text-funaab-400 hover:text-funaab-700">
                <i class="ti ti-x text-xs"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 5000)"
             class="mx-5 mt-4 flex items-center gap-2 px-4 py-3 bg-red-50 border
                    border-red-200 rounded-lg text-red-700 text-sm shadow-sm">
            <i class="ti ti-alert-circle text-red-500 text-base flex-shrink-0"></i>
            <span>{{ session('error') }}</span>
            <button @click="show = false" class="ml-auto text-red-400 hover:text-red-600">
                <i class="ti ti-x text-xs"></i>
            </button>
        </div>
    @endif

    {{-- Page slot --}}
    <div class="flex-1 p-5">
        {{ $slot }}
    </div>

    {{-- Footer --}}
    <footer class="px-5 py-3 border-t border-gray-200 bg-white text-[11px] text-gray-400
                   flex justify-between items-center flex-shrink-0">
        <span>&copy; {{ date('Y') }} Federal University of Agriculture, Abeokuta.</span>
        <span>FUNAAB WorkForce v1.0</span>
    </footer>

</main>


{{-- Alpine.js sidebar logic --}}
<script>
function adminLayout() {
    return {
        collapsed: false,

        init() {
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