<x-staff-layout title="Create Profile">

    <div class="flex items-start justify-between mb-5">
        <div>
            <h1 class="pg-title">Create Profile</h1>
            <p class="pg-sub">Complete your profile to get started on the Staff Portal</p>
        </div>
    </div>

    @if(session('error'))
        <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700
                    text-xs font-semibold px-4 py-3 rounded-lg mb-4">
            <i class="ti ti-alert-circle text-base"></i> {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('staff.profile.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            {{-- ── Avatar Upload ─────────────────────────────────── --}}
            <div class="lg:col-span-1">
                <div class="wf-card flex flex-col items-center text-center">
                    <div class="wf-card-title w-full">
                        <i class="ti ti-camera text-gray-400"></i> Profile Photo
                    </div>

                    {{-- Preview --}}
                    <div class="mb-4 mt-2">
                        @if($profile?->avatar)
                            <img id="avatar-preview"
                                 src="{{ asset('storage/avatars/'.$profile->avatar) }}"
                                 class="w-32 h-32 rounded-full object-cover ring-4 ring-funaab-50" />
                        @else
                            <div id="avatar-placeholder"
                                 class="w-32 h-32 rounded-full bg-funaab-600 flex items-center
                                        justify-center ring-4 ring-funaab-50">
                                <i class="ti ti-user text-4xl text-white"></i>
                            </div>
                            <img id="avatar-preview"
                                 src="" alt="Preview"
                                 class="w-32 h-32 rounded-full object-cover ring-4 ring-funaab-50 hidden" />
                        @endif
                    </div>

                    <label for="avatar" class="btn-wf-ghost cursor-pointer mb-3">
                        <i class="ti ti-upload text-sm"></i> Choose Photo
                    </label>
                    <input type="file" name="avatar" id="avatar"
                           class="hidden"
                           accept=".jpg,.jpeg,.png"
                           onchange="previewAvatar(this)"
                           required />

                    <p class="text-[10.5px] text-gray-400 leading-relaxed">
                        JPG or PNG only.<br>Max size: 1MB.<br>Will be cropped to a square.
                    </p>

                    @error('avatar')
                        <p class="text-red-500 text-[11px] mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- ── Profile Fields ────────────────────────────────── --}}
            @php
                    $inp = 'w-full px-3 py-2 border border-gray-400 rounded-lg text-sm focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50';
                    $lbl = 'block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5';
            @endphp
            <div class="lg:col-span-2 space-y-4">

                {{-- Contact Info --}}
                <div class="wf-card">
                    <div class="wf-card-title">
                        <i class="ti ti-address-book text-gray-400"></i> Contact Information
                    </div>

                    <div class="mb-4">
                        <label class="wf-label">Phone Number <span class="text-red-500">*</span></label>
                        <input type="text" name="phone"
                               value="{{ old('phone', $profile?->phone) }}"
                               placeholder="e.g. 08012345678"
                               class="{{ $inp }} @error('phone') border-red-400 @enderror"
                               required />
                        @error('phone')
                            <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="wf-label">Permanent Home Address <span class="text-red-500">*</span></label>
                        <textarea name="home_address" rows="3"
                                  placeholder="Enter your permanent home address"
                                  class="{{ $inp }} w-full resize-none @error('home_address') border-red-400 @enderror"
                                  required>{{ old('home_address', $profile?->home_address ?? '') }}</textarea>
                        @error('home_address')
                            <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Info Notice --}}
                <div class="flex items-start gap-3 bg-funaab-50 border border-funaab-100
                            rounded-lg px-4 py-3">
                    <i class="ti ti-info-circle text-funaab-600 text-base flex-shrink-0 mt-0.5"></i>
                    <p class="text-[11px] text-funaab-700 leading-relaxed">
                        Your <strong>designation</strong> will be automatically set from your employment record.
                        Other profile details can be completed after your account is set up.
                    </p>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between">
                    @if($profile !== null)
                        <a href="{{ route('staff.dashboard.index') }}" class="btn-wf-ghost">
                            <i class="ti ti-arrow-right text-sm"></i> Skip &amp; Continue
                        </a>
                    @else
                        <div></div>
                    @endif

                    <button type="submit" class="btn-wf-primary">
                        <i class="ti ti-device-floppy text-sm"></i> Save Profile
                    </button>
                </div>

            </div>
        </div>

    </form>

    <script>
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview     = document.getElementById('avatar-preview');
                    const placeholder = document.getElementById('avatar-placeholder');
                    if (preview) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                    }
                    if (placeholder) placeholder.classList.add('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

</x-staff-layout>
