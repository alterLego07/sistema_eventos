<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-surface-900 mb-1">Bienvenido de vuelta</h1>
        <p class="text-surface-500 text-sm">Ingresá tus credenciales para acceder al panel.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="form-label">Correo electrónico</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="form-input pl-10 @error('email') border-danger-500 @enderror"
                       placeholder="admin@ejemplo.com"
                       required autofocus autocomplete="username">
            </div>
            @error('email')
                <p class="mt-1.5 text-xs text-danger-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div x-data="{ show: false }">
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="form-label mb-0">Contraseña</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-xs text-brand-600 hover:text-brand-700 font-medium transition-colors">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input id="password" :type="show ? 'text' : 'password'" name="password"
                       class="form-input pl-10 pr-10 @error('password') border-danger-500 @enderror"
                       placeholder="••••••••"
                       required autocomplete="current-password">
                <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-surface-400 hover:text-surface-600 transition-colors">
                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs text-danger-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember me --}}
        <div class="flex items-center">
            <input id="remember_me" type="checkbox" name="remember"
                   class="w-4 h-4 rounded border-surface-300 text-brand-600 focus:ring-brand-500 cursor-pointer">
            <label for="remember_me" class="ml-2.5 text-sm text-surface-600 cursor-pointer select-none">
                Recordar sesión
            </label>
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="btn btn-primary w-full justify-center py-3 text-sm font-semibold">
            Iniciar sesión
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </button>
    </form>

    @if (Route::has('register'))
        <p class="mt-6 text-center text-sm text-surface-500">
            ¿No tenés cuenta?
            <a href="{{ route('register') }}" class="text-brand-600 font-semibold hover:text-brand-700 transition-colors">
                Registrarse
            </a>
        </p>
    @endif
</x-guest-layout>
