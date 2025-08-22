<x-guest-layout>
    <!-- Background funky -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute w-[480px] h-[480px] bg-pink-500/40 rounded-full blur-3xl -top-32 -left-32"></div>
        <div class="absolute w-[520px] h-[520px] bg-yellow-400/40 rounded-full blur-3xl bottom-0 -right-32"></div>
        <div class="absolute w-[400px] h-[400px] bg-purple-600/40 rounded-full blur-3xl top-1/4 left-1/3"></div>
    </div>

    <div class="flex flex-col items-center mb-6 text-center">
        <div class="mb-3">
            
        </div>
        <h1 class="mt-2 text-3xl font-bold tracking-tight bg-gradient-to-r from-indigo-500 via-pink-500 to-yellow-400 bg-clip-text text-transparent">
            Selamat Datang di Absensi App
        </h1>
        <p class="text-gray-300">Silakan masuk untuk melanjutkan</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="glass-card rounded-2xl p-6 sm:p-8 space-y-5 w-full max-w-md mx-auto">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-gray-200" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-pink-400" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" class="text-gray-200" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-pink-400" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-200">{{ __('Remember me') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="underline text-sm text-indigo-300 hover:text-indigo-400" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <button class="w-full px-4 py-2 rounded-lg font-semibold text-white bg-gradient-to-r from-indigo-500 via-pink-500 to-yellow-400 shadow-lg hover:scale-[1.02] transition-transform">
            Masuk
        </button>
    </form>
</x-guest-layout>
