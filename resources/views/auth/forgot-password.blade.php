<x-guest-layout>
<section class="flex w-full min-h-[70vh] items-center justify-center px-4">
    <form action="{{ route('password.email') }}" method="POST" class="w-full max-w-md bg-white border border-gray-200 rounded-xl p-6 space-y-4">
        @csrf
        <div>
            <h2 class="text-xl font-semibold">Forgot your password?</h2>
            <p class="text-sm text-gray-600 mt-1">Enter your Staff No. or e-mail address and we will e-mail you a link to set a new password.</p>
        </div>

        @if (session('success'))
            <div class="rounded-md bg-green-50 border border-green-200 px-3 py-2 text-sm text-green-800">{{ session('success') }}</div>
        @endif

        <div>
            <input type="text" name="login" value="{{ old('login') }}" required autofocus
                   placeholder="Staff No. or e-mail address"
                   class="w-full border border-gray-400 bg-gray-50 p-3 rounded-md focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-100">
            @error('login')<span class="text-red-700 text-sm">{{ $message }}</span>@enderror
        </div>

        <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white py-3 rounded-md">Send reset link</button>

        <div class="text-sm text-right"><a href="{{ route('welcome') }}" class="hover:underline">Back to sign in</a></div>
    </form>
</section>
</x-guest-layout>
