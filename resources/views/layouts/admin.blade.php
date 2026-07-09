<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Panel' }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important;}</style>
</head>
<body class="font-sans antialiased bg-surface-50 text-surface-800">

<div class="min-h-screen flex"
     x-data="{
         sidebarOpen: false,
         collapsed: localStorage.getItem('sidebar-collapsed') === 'true',
         toggleCollapse() {
             this.collapsed = !this.collapsed;
             localStorage.setItem('sidebar-collapsed', this.collapsed);
         }
     }">

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-black/60 lg:hidden"
         style="display: none;"></div>

    {{-- =========================================================
         SIDEBAR
    ========================================================= --}}
    <aside :class="[
               sidebarOpen ? 'translate-x-0' : '-translate-x-full',
               collapsed   ? 'w-20'          : 'w-72'
           ]"
           class="admin-sidebar fixed inset-y-0 left-0 z-50 flex flex-col
                  transition-all duration-300 ease-in-out
                  lg:translate-x-0 lg:static lg:z-auto flex-shrink-0">

        {{-- Logo / Brand --}}
        <div class="flex items-center h-16 px-4 border-b border-white/5 overflow-hidden">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="w-9 h-9 rounded-xl gradient-brand flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div x-show="!collapsed"
                     x-transition:enter="transition-all duration-200 delay-100"
                     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-all duration-100"
                     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="min-w-0">
                    <p class="text-white font-bold text-sm leading-tight truncate">Invitaciones</p>
                    <p class="text-surface-400 text-[0.7rem] truncate">Panel de Gestión</p>
                </div>
            </div>

            {{-- Collapse toggle (desktop only) --}}
            <button @click="toggleCollapse()"
                    class="hidden lg:flex items-center justify-center w-7 h-7 rounded-lg
                           text-surface-400 hover:text-white hover:bg-white/10 transition-colors flex-shrink-0"
                    :title="collapsed ? 'Expandir menú' : 'Colapsar menú'">
                <svg class="w-4 h-4 transition-transform duration-300" :class="collapsed ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-5 space-y-0.5 overflow-y-auto overflow-x-hidden">

            {{-- Section: Principal --}}
            <div x-show="!collapsed" class="px-3 pb-2">
                <p class="text-[0.62rem] font-bold uppercase tracking-[0.15em] text-surface-500">Principal</p>
            </div>
            <div x-show="collapsed" class="pb-2">
                <div class="border-t border-white/10"></div>
            </div>

            @php
                $navLink = fn(string $route, string $label, string $svg) =>
                    ['route' => $route, 'label' => $label, 'svg' => $svg];

                $mainLinks = [
                    ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'pattern' => 'admin.dashboard', 'icon' => '
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5z
                                 M14 5a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1V5z
                                 M4 15a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3z
                                 M14 13a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1h-4a1 1 0 01-1-1v-5z"/>
                    '],
                ];
            @endphp

            <a href="{{ route('admin.dashboard') }}"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all
                      {{ request()->routeIs('admin.dashboard') ? 'active text-white' : 'text-surface-300 hover:text-white' }}"
               :class="{ 'justify-center px-0': collapsed }"
               title="Dashboard">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 13a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1h-4a1 1 0 01-1-1v-5z"/>
                </svg>
                <span x-show="!collapsed" x-transition:enter="transition-opacity duration-200 delay-75"
                      x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                      x-transition:leave="transition-opacity duration-75"
                      x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                      class="whitespace-nowrap">Dashboard</span>
            </a>

            {{-- Section: Gestión --}}
            <div class="pt-3">
                <div x-show="!collapsed" class="px-3 pb-2">
                    <p class="text-[0.62rem] font-bold uppercase tracking-[0.15em] text-surface-500">Gestión</p>
                </div>
                <div x-show="collapsed" class="pb-2">
                    <div class="border-t border-white/10"></div>
                </div>
            </div>

            <a href="{{ route('admin.events.index') }}"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all
                      {{ request()->routeIs('admin.events.*') ? 'active text-white' : 'text-surface-300 hover:text-white' }}"
               :class="{ 'justify-center px-0': collapsed }"
               title="Eventos">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span x-show="!collapsed"
                      x-transition:enter="transition-opacity duration-200 delay-75"
                      x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                      x-transition:leave="transition-opacity duration-75"
                      x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                      class="whitespace-nowrap">Eventos</span>
            </a>

            <a href="{{ route('admin.templates.index') }}"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all
                      {{ request()->routeIs('admin.templates.*') ? 'active text-white' : 'text-surface-300 hover:text-white' }}"
               :class="{ 'justify-center px-0': collapsed }"
               title="Plantillas">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                </svg>
                <span x-show="!collapsed"
                      x-transition:enter="transition-opacity duration-200 delay-75"
                      x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                      x-transition:leave="transition-opacity duration-75"
                      x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                      class="whitespace-nowrap">Plantillas</span>
            </a>

            @canany(['users.view'])
            <a href="{{ route('admin.users.index') }}"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all
                      {{ request()->routeIs('admin.users.*') ? 'active text-white' : 'text-surface-300 hover:text-white' }}"
               :class="{ 'justify-center px-0': collapsed }"
               title="Usuarios">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span x-show="!collapsed"
                      x-transition:enter="transition-opacity duration-200 delay-75"
                      x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                      x-transition:leave="transition-opacity duration-75"
                      x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                      class="whitespace-nowrap">Usuarios</span>
            </a>
            @endcanany

            @role('super-admin')
            {{-- Section: Plataforma --}}
            <div class="pt-3">
                <div x-show="!collapsed" class="px-3 pb-2">
                    <p class="text-[0.62rem] font-bold uppercase tracking-[0.15em] text-surface-500">Plataforma</p>
                </div>
                <div x-show="collapsed" class="pb-2">
                    <div class="border-t border-white/10"></div>
                </div>
            </div>

            <a href="{{ route('admin.companies.index') }}"
               class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all
                      {{ request()->routeIs('admin.companies.*') ? 'active text-white' : 'text-surface-300 hover:text-white' }}"
               :class="{ 'justify-center px-0': collapsed }"
               title="Empresas">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-6 0H3m2 0h4M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5"/>
                </svg>
                <span x-show="!collapsed"
                      x-transition:enter="transition-opacity duration-200 delay-75"
                      x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                      x-transition:leave="transition-opacity duration-75"
                      x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                      class="whitespace-nowrap">Empresas</span>
            </a>
            @endrole
        </nav>

        {{-- User Profile --}}
        <div class="px-3 py-4 border-t border-white/5">
            <div class="flex items-center gap-3 overflow-hidden"
                 :class="{ 'justify-center': collapsed }">
                <div class="w-8 h-8 rounded-full gradient-brand flex items-center justify-center
                            text-white font-bold text-xs flex-shrink-0">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div x-show="!collapsed"
                     x-transition:enter="transition-opacity duration-200 delay-75"
                     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity duration-75"
                     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[0.68rem] text-surface-400 truncate">
                        {{ Auth::user()->hasRole('super-admin') ? 'Super Admin · Plataforma' : (Auth::user()->company?->name ?? Auth::user()->email) }}
                    </p>
                </div>
                <form x-show="!collapsed" method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                    @csrf
                    <button type="submit"
                            class="text-surface-400 hover:text-white transition-colors"
                            title="Cerrar sesión">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- =========================================================
         MAIN CONTENT
    ========================================================= --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Top bar --}}
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-lg border-b border-surface-100 flex-shrink-0">
            <div class="flex items-center h-16 px-4 sm:px-6 gap-4">
                {{-- Mobile hamburger --}}
                <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden btn-ghost btn-icon flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                {{-- Breadcrumb --}}
                <div class="flex-1 flex items-center gap-2 text-sm min-w-0">
                    @isset($breadcrumbs)
                        {{ $breadcrumbs }}
                    @else
                        <span class="text-surface-400 text-sm">{{ $title ?? 'Panel' }}</span>
                    @endisset
                </div>

                {{-- Actions slot --}}
                @isset($actions)
                    <div class="flex items-center gap-2 flex-shrink-0">
                        {{ $actions }}
                    </div>
                @endisset
            </div>
        </header>

        {{-- Flash messages --}}
        @foreach (['success' => 'bg-success-50 text-success-600 border-success-400/20',
                   'error'   => 'bg-danger-50 text-danger-600 border-danger-400/20'] as $type => $classes)
            @if(session($type))
                <div class="mx-4 sm:mx-6 mt-4"
                     x-data="{ show: true }" x-show="show"
                     x-init="setTimeout(() => show = false, 5000)"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2">
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border {{ $classes }}">
                        @if($type === 'success')
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @else
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @endif
                        <p class="text-sm font-medium">{{ session($type) }}</p>
                        <button @click="show = false" class="ml-auto opacity-60 hover:opacity-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif
        @endforeach

        {{-- Page content --}}
        <main class="flex-1 px-4 sm:px-6 py-6">
            @isset($header)
                <div class="mb-6">{{ $header }}</div>
            @endisset
            {{ $slot }}
        </main>
    </div>
</div>

</body>
</html>
