<x-staff-layout title="Circles">

    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">Circles</h1>
            <p class="pg-sub">Staff members in your department — {{ $department?->department_name ?? 'Your Department' }}</p>
        </div>
    </div>

    @if($staffMembers->isEmpty())
        <div class="wf-card flex flex-col items-center justify-center py-16 text-center">
            <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                <i class="ti ti-users text-2xl text-gray-400"></i>
            </div>
            <p class="text-sm font-bold text-gray-600 mb-1">No staff found</p>
            <p class="text-xs text-gray-400">No other staff members are currently in your department.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($staffMembers as $member)
            
                @php
                    $initials = strtoupper(substr($member->surname ?? 'S', 0, 1)).strtoupper(substr($member->firstname ?? 'S', 0, 1));
                    $avatar   = $member->user?->profile?->avatar;
                    $designation = $member->employment?->designation?->cadreRank?->name
                                ?? $member->user?->profile?->designation
                                ?? '—';
                @endphp
                <a href="{{ route('staff.profile.email_user_profile', $member->user?->email) }}"
                   class="wf-card flex flex-col items-center text-center p-5 hover:shadow-md
                          transition-shadow cursor-pointer group">

                    {{-- Avatar --}}
                    @if($avatar)
                        <img src="{{ asset('storage/avatars/'.$avatar) }}"
                             alt="{{ $member->surname }}"
                             class="w-20 h-20 rounded-full object-cover mb-3 ring-2 ring-gray-100
                                    group-hover:ring-funaab-200 transition-all" />
                    @else
                        <div class="w-20 h-20 rounded-full bg-funaab-600 flex items-center justify-center
                                    mb-3 ring-2 ring-gray-100 group-hover:ring-funaab-200 transition-all">
                            <span class="text-xl font-bold text-white">{{ $initials }}</span>
                        </div>
                    @endif

                    {{-- Name --}}
                    <p class="text-sm font-bold text-gray-800 group-hover:text-funaab-700 transition-colors leading-tight mb-1">
                        {{ $member->surname }}, {{ $member->firstname }}
                        @if($member->othernames) {{ $member->othernames }} @endif
                    </p>

                    {{-- Designation --}}
                    <p class="text-[11px] text-gray-400 font-semibold">{{ $designation }}</p>

                </a>
            @endforeach
        </div>
    @endif

</x-staff-layout>
