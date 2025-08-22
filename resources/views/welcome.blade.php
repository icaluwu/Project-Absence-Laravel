{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Vite (Tailwind full) -->
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-dvh bg-[#0c0c10] text-[#fafafc] font-sans overflow-x-hidden">
    {{-- Funky background layers --}}
    <div class="pointer-events-none fixed inset-0 -z-10">
        <div class="blob b1"></div>
        <div class="blob b2"></div>
        <div class="blob b3"></div>
        <div class="gridish"></div>
    </div>

    <header class="container mx-auto px-5 py-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="brand-mark animate-spin-slow"></div>
            <span class="text-lg sm:text-xl font-semibold tracking-tight hidden sm:inline">{{ config('app.name', 'Laravel') }}</span>
        </div>

        {{-- CTA tunggal: Login (atau Dashboard jika sudah login) --}}
        @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-funky" aria-label="Buka dashboard">
                    Dashboard
                    <span class="spark" aria-hidden="true"></span>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-funky" aria-label="Masuk ke aplikasi">
                    Login
                    <span class="spark" aria-hidden="true"></span>
                </a>
            @endauth
        @endif
    </header>

    <main class="container mx-auto px-5 py-12 sm:py-16 grid place-items-center">
        <section class="w-full max-w-3xl glass-card text-center rounded-2xl sm:rounded-3xl p-8 sm:p-10">
            <div class="inline-flex items-center gap-2 kicker mb-3">
                <span class="dot"></span>
                <span class="text-sm text-white/80">Absence at its best.</span>
            </div>

            <h1 class="text-3xl sm:text-5xl font-semibold leading-tight tracking-tight">
                Absensi yang <span class="text-gradient">cepat</span>, <span class="text-gradient">intuitif</span>, dan <span class="text-gradient">seru</span>.
            </h1>

            <p class="mt-4 sm:mt-5 text-base sm:text-lg text-white/70 max-w-prose mx-auto">
                Kelola check-in/out, izin &amp; lembur, serta slip gaji di satu tempat.
                Desain playful, performa enterprise.
            </p>

            <div class="mt-7">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-funky" aria-label="Buka dashboard sekarang">
                            Buka Dashboard
                            <span class="spark" aria-hidden="true"></span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-funky" aria-label="Masuk ke aplikasi sekarang">
                            Login
                            <span class="spark" aria-hidden="true"></span>
                        </a>
                    @endauth
                @endif
            </div>

            <div class="mt-4 text-xs text-white/60">
                Built by IcalUwU • {{ now()->format('Y') }}
            </div>
        </section>
    </main>
</body>
</html>
