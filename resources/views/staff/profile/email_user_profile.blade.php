<x-staff-layout title="Staff Profile">

    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">Staff Profile</h1>
            <p class="pg-sub">View staff member details</p>
        </div>
        <a href="{{ route('staff.circles.index') }}" class="btn-wf-ghost btn-wf-sm">
            <i class="ti ti-arrow-left text-sm"></i> Back to Circles
        </a>
    </div>

    @if($userprofile === null)
        <div class="wf-card flex flex-col items-center justify-center py-16 text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mb-4">
                <i class="ti ti-user-off text-2xl text-red-400"></i>
            </div>
            <p class="text-sm font-bold text-gray-600 mb-1">User not found</p>
            <p class="text-xs text-gray-400">Sorry, there is no such record.</p>
        </div>

    @elseif($userprofile->profile === null)
        <div class="wf-card flex flex-col items-center justify-center py-16 text-center">
            <div class="w-14 h-14 rounded-full bg-amber-50 flex items-center justify-center mb-4">
                <i class="ti ti-user-exclamation text-2xl text-amber-400"></i>
            </div>
            <p class="text-sm font-bold text-gray-600 mb-1">Profile not set up</p>
            <p class="text-xs text-gray-400">This staff member has not yet created their profile.</p>
        </div>

    @else
        @php
            $avatar      = $userprofile->profile->avatar;
            $initials    = strtoupper(substr($userprofile->surname ?? 'S', 0, 1))
                         . strtoupper(substr($userprofile->firstname ?? 'S', 0, 1));
            $designation = $userprofile->profile->designation ?? '—';
            $phone       = $userprofile->profile->phone ?? '—';
            $email       = $userprofile->email ?? '—';
            $fullname    = trim(($userprofile->staff->surname ?? '')
                         . ', ' . ($userprofile->staff->firstname ?? '')
                         . ' ' . ($userprofile->staff->othernames ?? ''));
        @endphp

        <div class="max-w-lg mx-auto">
            <div class="wf-card">

                {{-- Avatar --}}
                <div class="flex flex-col items-center py-6 border-b border-gray-100">
                    @if($avatar)
                        <img src="{{ asset('storage/avatars/'.$avatar) }}"
                             alt="{{ $fullname }}"
                             class="w-28 h-28 rounded-full object-cover ring-4 ring-funaab-50 mb-4" />
                    @else
                        <div class="w-28 h-28 rounded-full bg-funaab-600 flex items-center justify-center
                                    ring-4 ring-funaab-50 mb-4">
                            <span class="text-3xl font-bold text-white">{{ $initials }}</span>
                        </div>
                    @endif

                    <h2 class="text-base font-bold text-gray-800">{{ $fullname }}</h2>
                    <p class="text-xs text-gray-400 font-semibold mt-1">{{ $designation }}</p>
                </div>

                {{-- Contact Details --}}
                <div class="divide-y divide-gray-50 px-2">

                    <div class="flex items-center gap-4 py-3">
                        <div class="w-8 h-8 rounded-lg bg-funaab-50 flex items-center justify-center flex-shrink-0">
                            <i class="ti ti-mail text-funaab-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold tracking-wider text-gray-400 mb-0.5">EMAIL ADDRESS</p>
                            <p class="text-sm font-semibold text-gray-700">{{ $email }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 py-3">
                        <div class="w-8 h-8 rounded-lg bg-funaab-50 flex items-center justify-center flex-shrink-0">
                            <i class="ti ti-phone text-funaab-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold tracking-wider text-gray-400 mb-0.5">PHONE NUMBER</p>
                            <p class="text-sm font-semibold text-gray-700">{{ $phone }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 py-3">
                        <div class="w-8 h-8 rounded-lg bg-funaab-50 flex items-center justify-center flex-shrink-0">
                            <i class="ti ti-briefcase text-funaab-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold tracking-wider text-gray-400 mb-0.5">DESIGNATION</p>
                            <p class="text-sm font-semibold text-gray-700">{{ $designation }}</p>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    @endif

</x-staff-layout>
