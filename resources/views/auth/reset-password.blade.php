<x-guest-layout>
<section class="flex w-full min-h-[70vh] items-center justify-center px-4">
    <form action="{{ route('password.store') }}" method="POST" class="w-full max-w-md bg-white border border-gray-200 rounded-xl p-6 space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        @if(!empty($invite))<input type="hidden" name="broker" value="invites">@endif

        <h2 class="text-xl font-semibold">{{ !empty($invite) ? 'Choose your password' : 'Set a new password' }}</h2>
        @if(!empty($invite))<p class="text-sm text-gray-500">Welcome to FUNAAB Workforce. Choose a password, then sign in with your Staff No. or e-mail address.</p>@endif

        <div>
            <label class="block text-sm text-gray-600 mb-1">E-mail address</label>
            <input type="email" name="email" value="{{ old('email', $email) }}" required
                   class="w-full border border-gray-400 bg-gray-50 p-3 rounded-md">
            @error('email')<span class="text-red-700 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">New password (at least 8 characters)</label>
            <input type="password" name="password" required class="w-full border border-gray-400 bg-gray-50 p-3 rounded-md">
            @error('password')<span class="text-red-700 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Confirm new password</label>
            <input type="password" name="password_confirmation" required class="w-full border border-gray-400 bg-gray-50 p-3 rounded-md">
        </div>

        <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white py-3 rounded-md">{{ !empty($invite) ? 'Set password' : 'Reset password' }}</button>
    </form>
</section>
</x-guest-layout>
