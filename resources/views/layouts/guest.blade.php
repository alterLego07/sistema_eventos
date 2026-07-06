<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">

<div class="min-h-screen flex">

    {{-- ── Left panel: branding ───────────────────────────────────── --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden gradient-dark flex-col justify-between p-12">

        {{-- Decorative blobs --}}
        <div class="absolute top-[-8rem] left-[-8rem] w-96 h-96 rounded-full opacity-20"
             style="background: radial-gradient(circle, var(--color-brand-600), transparent)"></div>
        <div class="absolute bottom-[-6rem] right-[-4rem] w-80 h-80 rounded-full opacity-15"
             style="background: radial-gradient(circle, var(--color-accent-600), transparent)"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 rounded-full opacity-5"
             style="background: radial-gradient(circle, var(--color-brand-400), transparent)"></div>

        {{-- Logo --}}
        <div class="relative z-10 flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl gradient-brand flex items-center justify-center shadow-lg">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-white font-bold text-lg tracking-tight">{{ config('app.name') }}</span>
        </div>

        {{-- Center illustration --}}
        <div class="relative z-10 flex flex-col items-center text-center">
            <div class="w-24 h-24 rounded-3xl gradient-brand flex items-center justify-center mb-8 shadow-2xl"
                 style="box-shadow: 0 20px 60px rgba(76, 110, 245, 0.35)">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-white leading-tight mb-4">
                Invitaciones<br>digitales elegantes
            </h2>
            <p class="text-surface-400 text-base max-w-sm leading-relaxed">
                Crea, personaliza y gestiona invitaciones para tus eventos con confirmación de asistencia en tiempo real.
            </p>

            {{-- Feature pills --}}
            <div class="flex flex-wrap justify-center gap-2 mt-8">
                @foreach(['RSVP en tiempo real', 'Plantillas elegantes', 'Estadísticas', 'Cuenta regresiva'] as $feat)
                    <span class="px-3 py-1.5 rounded-full text-xs font-medium text-surface-300 border border-white/10 glass">
                        {{ $feat }}
                    </span>
                @endforeach
            </div>
        </div>

        {{-- Footer --}}
        <div class="relative z-10 text-surface-500 text-xs text-center">
            © {{ date('Y') }} {{ config('app.name') }}
        </div>
    </div>

    {{-- ── Right panel: form ───────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col justify-center px-6 sm:px-12 lg:px-16 bg-surface-50">

        {{-- Mobile logo --}}
        <div class="lg:hidden flex items-center gap-3 mb-10">
            <div class="w-9 h-9 rounded-xl gradient-brand flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="font-bold text-surface-800 text-lg">{{ config('app.name') }}</span>
        </div>

        <div class="w-full max-w-md mx-auto">
            {{ $slot }}
        </div>
    </div>
</div>

</body>
</html>
