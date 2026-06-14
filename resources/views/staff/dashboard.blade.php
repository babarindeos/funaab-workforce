<x-staff-layout title="Dashboard">

    {{-- Welcome header --}}
    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">
                Welcome back, {{ Auth::user()->firstname ?? 'Staff' }}
            </h1>
            <p class="pg-sub">
                {{ $profile->designation ?? '—' }} ·
                File No: <span class="font-mono">{{ Auth::user()->fileno ?? '—' }}</span>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('staff.profile.myprofile') }}" class="btn-wf-ghost btn-wf-sm">
                <i class="ti ti-user text-sm"></i> My Profile
            </a>
            <a href="{{ route('staff.profile.change_password') }}" class="btn-wf-ghost btn-wf-sm">
                <i class="ti ti-lock text-sm"></i> Change Password
            </a>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-funaab-50 flex items-center justify-center">
                    <i class="ti ti-calendar-check text-funaab-700 text-sm"></i>
                </div>
                LEAVE BALANCE
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $leaveRemaining }} <span class="text-sm font-normal text-gray-400">days</span></div>
            <div class="text-[10.5px] text-gray-400 mt-1">{{ $leaveTaken }} days taken this year</div>
        </div>

        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-gold-50 flex items-center justify-center">
                    <i class="ti ti-chart-bar text-gold-600 text-sm"></i>
                </div>
                GRADE LEVEL
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $profile->salary_level ?? '—' }}</div>
            <div class="text-[10.5px] text-gray-400 mt-1">Step {{ $profile->salary_level_step ?? '—' }}</div>
        </div>

        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-blue-50 flex items-center justify-center">
                    <i class="ti ti-clock text-blue-600 text-sm"></i>
                </div>
                PENDING LEAVE
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $pendingLeaves }}</div>
            <div class="text-[10.5px] {{ $pendingLeaves > 0 ? 'text-yellow-600' : 'text-gray-400' }} mt-1">
                {{ $pendingLeaves > 0 ? 'Awaiting approval' : 'None pending' }}
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center gap-2 text-[10.5px] font-bold tracking-widest text-gray-400 mb-2">
                <div class="w-6 h-6 rounded bg-purple-50 flex items-center justify-center">
                    <i class="ti ti-receipt text-purple-600 text-sm"></i>
                </div>
                SALARY STRUCTURE
            </div>
            <div class="text-lg font-bold text-gray-800">{{ $profile->salary_structure ?? '—' }}</div>
            <div class="text-[10.5px] text-gray-400 mt-1">{{ $profile->staff_status ?? '—' }}</div>
        </div>
    </div>

    {{-- Notifications --}}
    @if(isset($notifications) && $notifications->count())
    <div class="wf-card mb-4">
        <div class="wf-card-title">
            <i class="ti ti-bell text-gray-400"></i> Notifications
            <span class="ml-auto text-[10px] font-bold text-funaab-700 bg-funaab-50 px-2 py-0.5 rounded-full">
                {{ $notifications->count() }} new
            </span>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($notifications as $note)
            <div class="flex items-start gap-3 py-2.5 {{ $loop->first ? '' : '' }}">
                {{-- Icon badge --}}
                <div class="mt-0.5 w-7 h-7 rounded-lg flex-shrink-0 flex items-center justify-center
                    {{ $note['type'] === 'leave'      ? 'bg-funaab-50 text-funaab-700'  :
                      ($note['type'] === 'promotion'  ? 'bg-gold-50 text-gold-700'       :
                      ($note['type'] === 'document'   ? 'bg-purple-50 text-purple-700'   :
                      ($note['type'] === 'alert'      ? 'bg-red-50 text-red-600'         :
                                                        'bg-blue-50 text-blue-700'))) }}">
                    <i class="ti
                        {{ $note['type'] === 'leave'      ? 'ti-calendar-event'    :
                          ($note['type'] === 'promotion'  ? 'ti-award'             :
                          ($note['type'] === 'document'   ? 'ti-file-description'  :
                          ($note['type'] === 'alert'      ? 'ti-alert-triangle'    :
                                                            'ti-info-circle'))) }} text-sm"></i>
                </div>
                {{-- Message --}}
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-700 leading-snug">{{ $note['message'] }}</p>
                    <p class="text-[10.5px] text-gray-400 mt-0.5">{{ $note['time'] }}</p>
                </div>
                {{-- Optional action link --}}
                @if(!empty($note['href']))
                <a href="{{ $note['href'] }}"
                   class="flex-shrink-0 text-[10.5px] font-bold text-funaab-700 hover:underline mt-0.5">
                    View <i class="ti ti-arrow-right text-[10px]"></i>
                </a>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Quick actions --}}
        <div class="lg:col-span-2 wf-card">
            <div class="wf-card-title">
                <i class="ti ti-apps text-gray-400"></i> Quick Actions
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @php
                $actions = [
                    ['label' => 'Apply for Leave',  'icon' => 'ti-calendar-plus',   'color' => 'bg-funaab-50 text-funaab-700',  'href' => '#'],
                    ['label' => 'APER Form',         'icon' => 'ti-file-certificate','color' => 'bg-gold-50 text-gold-700',      'href' => '#'],
                    ['label' => 'My Promotion',      'icon' => 'ti-award',           'color' => 'bg-blue-50 text-blue-700',      'href' => '#'],
                    ['label' => 'My Documents',      'icon' => 'ti-file-description','color' => 'bg-purple-50 text-purple-700',  'href' => route('staff.document.index')],
                    ['label' => 'Update Profile',    'icon' => 'ti-user-edit',       'color' => 'bg-green-50 text-green-700',    'href' => route('staff.profile.myprofile.edit')],
                    ['label' => 'Circles',           'icon' => 'ti-circle-dot',      'color' => 'bg-pink-50 text-pink-700',      'href' => route('staff.circles.index')],
                ];
                @endphp
                @foreach($actions as $action)
                <a href="{{ $action['href'] }}"
                   class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-100
                          hover:border-gray-200 hover:shadow-sm transition-all {{ $action['color'] }} bg-opacity-60">
                    <i class="ti {{ $action['icon'] }} text-2xl"></i>
                    <span class="text-xs font-bold text-center">{{ $action['label'] }}</span>
                </a>
                @endforeach
            </div>
        </div>

        {{-- Personal summary --}}
        <div class="wf-card">
            <div class="wf-card-title">
                <i class="ti ti-id-badge text-gray-400"></i> My Details
            </div>

            {{-- Avatar --}}
            <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-100">
                @if($profile->avatar)
                    <img src="{{ asset('storage/'.$profile->avatar) }}"
                         class="w-12 h-12 rounded-full object-cover border-2 border-funaab-100" />
                @else
                    <div class="w-12 h-12 rounded-full bg-funaab-50 border-2 border-funaab-100
                                flex items-center justify-center text-funaab-700 font-bold text-lg">
                        {{ strtoupper(substr(Auth::user()->surname ?? 'S', 0, 1)) }}{{ strtoupper(substr(Auth::user()->firstname ?? 'S', 0, 1)) }}
                    </div>
                @endif
                <div>
                    <div class="text-sm font-bold text-gray-800">
                        {{ Auth::user()->surname }}, {{ Auth::user()->firstname }}
                    </div>
                    <div class="text-xs text-gray-400 font-mono mt-0.5">{{ Auth::user()->fileno }}</div>
                </div>
            </div>

            @php $cell = 'mb-2.5'; $ck = 'text-[9.5px] font-bold tracking-wider text-gray-400 uppercase'; $cv = 'text-xs font-bold text-gray-700 mt-0.5'; @endphp
            <div class="{{ $cell }}"><div class="{{ $ck }}">Designation</div><div class="{{ $cv }}">{{ $profile->designation ?? '—' }}</div></div>
            <div class="{{ $cell }}"><div class="{{ $ck }}">Salary Structure</div><div class="{{ $cv }}">{{ $profile->salary_structure ?? '—' }}</div></div>
            <div class="{{ $cell }}"><div class="{{ $ck }}">Grade Level / Step</div><div class="{{ $cv }}">GL {{ $profile->salary_level ?? '—' }} / Step {{ $profile->salary_level_step ?? '—' }}</div></div>
            <div class="{{ $cell }}"><div class="{{ $ck }}">Staff Status</div><div class="{{ $cv }}">{{ $profile->staff_status ?? '—' }}</div></div>
            <div class="{{ $cell }}"><div class="{{ $ck }}">Phone</div><div class="{{ $cv }}">{{ $profile->phone ?? '—' }}</div></div>

            <div class="mt-3 pt-3 border-t border-gray-100">
                <a href="{{ route('staff.profile.myprofile') }}"
                   class="text-xs text-funaab-700 font-bold hover:underline flex items-center gap-1">
                    View full profile <i class="ti ti-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </div>

</x-staff-layout>
