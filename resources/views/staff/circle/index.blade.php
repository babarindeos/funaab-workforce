<x-staff-layout title="Circle">

    <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
        <div>
            <h1 class="pg-title">Circle</h1>
            <p class="pg-sub">
                Colleagues in {{ $division?->division_name ?? 'your division' }}
                @if($division?->short_name) ({{ $division->short_name }}) @endif
            </p>
        </div>
        <form method="GET" action="{{ route('staff.circle.index') }}" class="relative w-full sm:w-64">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="q" value="{{ $search }}" placeholder="Search by name…"
                   class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-funaab-500 bg-white" />
        </form>
    </div>

    @if(!$division)
        <div class="wf-card flex flex-col items-center justify-center py-16 text-center">
            <i class="ti ti-building text-3xl text-gray-300 mb-3"></i>
            <p class="text-sm font-bold text-gray-600">Your division has not been recorded yet.</p>
            <p class="text-xs text-gray-400 mt-1">Please contact Registry to update your employment details.</p>
        </div>
    @else
        <div class="text-[11px] text-gray-400 font-semibold mb-3">
            {{ $members->total() }} {{ \Illuminate\Support\Str::plural('member', $members->total()) }}
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($members as $m)
                @php
                    $avatar   = $m->user?->profile?->avatar;
                    $initials = strtoupper(mb_substr($m->surname ?? '', 0, 1) . mb_substr($m->firstname ?? '', 0, 1));
                    $email    = $m->user?->email;
                    $name     = trim(($m->title?->title ? $m->title->title . ' ' : '') . $m->surname . ', ' . $m->firstname . ' ' . $m->othernames);
                    $designation = $m->employment?->designation_name;
                @endphp
                <div class="wf-card flex flex-col items-center text-center py-5 {{ $m->id === $me->id ? 'ring-1 ring-funaab-200' : '' }}">
                    @if($avatar)
                        <img src="{{ asset('storage/avatars/' . $avatar) }}" alt="{{ $name }}"
                             class="w-20 h-20 rounded-full object-cover border border-gray-200 mb-3" loading="lazy" />
                    @else
                        <div class="w-20 h-20 rounded-full bg-funaab-50 text-funaab-700 flex items-center justify-center text-xl font-bold border border-funaab-100 mb-3">
                            {{ $initials }}
                        </div>
                    @endif
                    <p class="text-sm font-bold text-gray-800 leading-tight">{{ $name }}</p>
                    @if($m->id === $me->id)<span class="text-[10px] text-funaab-600 font-semibold">(You)</span>@endif
                    <p class="text-xs text-gray-500 mt-1">{{ $designation ?: '—' }}</p>
                    @if($email)
                        <a href="mailto:{{ $email }}" class="text-[11px] text-funaab-700 hover:underline mt-2 break-all">
                            <i class="ti ti-mail text-xs"></i> {{ $email }}
                        </a>
                    @endif
                </div>
            @empty
                <div class="wf-card col-span-full text-center py-12 text-gray-400">
                    <i class="ti ti-users text-3xl block mb-2"></i>
                    {{ $search !== '' ? 'No colleague matches your search.' : 'No colleagues found in your division.' }}
                </div>
            @endforelse
        </div>

        @if($members->hasPages())
            <div class="mt-4">{{ $members->links() }}</div>
        @endif
    @endif

</x-staff-layout>
