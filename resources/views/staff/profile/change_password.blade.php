<x-staff-layout title="Change Password">

    <div class="flex items-center gap-2 mb-5">
        <a href="{{ route('staff.profile.myprofile') }}" class="text-gray-400 hover:text-funaab-700 transition-colors">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="pg-title">Change Password</h1>
            <p class="pg-sub">Update your account password</p>
        </div>
    </div>

    <div class="max-w-md">
        <div class="wf-card">
            <form action="{{ route('staff.profile.change_password.update') }}" method="POST" class="space-y-4">
                @csrf @method('PUT')
                @php
                    $inp = 'w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-funaab-500 focus:ring-2 focus:ring-funaab-50';
                    $lbl = 'block text-[10.5px] font-bold tracking-wider text-gray-500 mb-1.5';
                @endphp

                <div>
                    <label class="{{ $lbl }}">CURRENT PASSWORD <span class="text-red-500">*</span></label>
                    <input type="password" name="current_password" class="{{ $inp }}" required />
                    @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="{{ $lbl }}">NEW PASSWORD <span class="text-red-500">*</span></label>
                    <input type="password" name="password" class="{{ $inp }}" required />
                    <p class="text-[10.5px] text-gray-400 mt-1">Minimum 8 characters.</p>
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="{{ $lbl }}">CONFIRM NEW PASSWORD <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" class="{{ $inp }}" required />
                </div>

                <div class="pt-2 flex justify-end gap-2 border-t border-gray-100">
                    <a href="{{ route('staff.profile.myprofile') }}" class="btn-wf-ghost">Cancel</a>
                    <button type="submit" class="btn-wf-primary">
                        <i class="ti ti-lock text-sm"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-staff-layout>
