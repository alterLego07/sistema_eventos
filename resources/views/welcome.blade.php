<!DOCTYPE html>
<html lang="es" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Planora — el ERP para planificar eventos: invitados, cronograma y presupuesto en un solo lugar.">
        <title>Planora — ERP para planificar eventos</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|outfit:600,700,800" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-surface-950 font-sans text-surface-100 antialiased">
        <div class="pointer-events-none fixed inset-0 overflow-hidden">
            <div class="absolute -top-40 left-1/4 h-96 w-96 rounded-full bg-brand-600/20 blur-3xl"></div>
            <div class="absolute top-1/3 -right-20 h-96 w-96 rounded-full bg-accent-600/20 blur-3xl"></div>
        </div>

        <!-- Nav -->
        <header class="sticky top-0 z-50 border-b border-white/5 glass-dark">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                <a href="/" class="flex items-center gap-2 font-display text-lg font-bold text-white">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg gradient-brand">
                        <svg class="h-4.5 w-4.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v4M16 2v4M3 10h18M5 6h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2Z"/></svg>
                    </span>
                    Planora
                </a>

                <div class="hidden items-center gap-8 text-sm font-medium text-surface-300 md:flex">
                    <a href="#features" class="transition hover:text-white">Características</a>
                    <a href="#how" class="transition hover:text-white">Cómo funciona</a>
                    <a href="#pricing" class="transition hover:text-white">Precios</a>
                    <a href="#faq" class="transition hover:text-white">Preguntas</a>
                </div>

                <div class="flex items-center gap-3">
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="hidden text-sm font-semibold text-surface-300 transition hover:text-white sm:block">
                            Iniciar sesión
                        </a>
                    @endif
                    <a href="#contact" class="rounded-lg gradient-brand px-4 py-2 text-sm font-semibold text-white shadow-glow transition hover:opacity-90">
                        Solicitar demo
                    </a>
                </div>
            </nav>
        </header>

        <main class="relative">
            <!-- Hero -->
            <section class="mx-auto max-w-7xl px-6 pt-20 pb-24 text-center sm:pt-28">
                <div class="mx-auto mb-6 inline-flex items-center gap-2 rounded-full glass px-4 py-1.5 text-xs font-medium text-surface-200 animate-fade-in">
                    <span class="h-1.5 w-1.5 rounded-full bg-success-500"></span>
                    Ya usado por empresas de eventos en Paraguay
                </div>

                <h1 class="mx-auto max-w-3xl font-display text-4xl font-extrabold leading-tight text-white sm:text-6xl animate-fade-in-up">
                    Planificá cada evento sin perder el control del
                    <span class="text-gradient">presupuesto</span>
                </h1>

                <p class="mx-auto mt-6 max-w-xl text-lg text-surface-300 animate-fade-in-up animate-delay-100">
                    Planora reúne invitados, cronograma y presupuesto estimado vs. real en un solo ERP,
                    para que tu equipo deje de organizar eventos a fuerza de planillas sueltas.
                </p>

                <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row animate-fade-in-up animate-delay-200">
                    <a href="#contact" class="w-full rounded-lg gradient-brand px-6 py-3 text-base font-semibold text-white shadow-glow transition hover:opacity-90 sm:w-auto">
                        Solicitar demo gratuita
                    </a>
                    <a href="#features" class="w-full rounded-lg glass px-6 py-3 text-base font-semibold text-white transition hover:bg-white/10 sm:w-auto">
                        Ver características
                    </a>
                </div>

                <!-- Hero visual: stat glass cards -->
                <div class="mx-auto mt-16 grid max-w-3xl grid-cols-1 gap-4 sm:grid-cols-3 animate-fade-in-up animate-delay-300">
                    <div class="rounded-2xl glass p-6 text-left">
                        <p class="text-3xl font-bold text-white">128</p>
                        <p class="mt-1 text-sm text-surface-400">Invitados confirmados</p>
                        <div class="progress-bar mt-3 bg-white/10"><div class="progress-bar-fill" style="width:72%"></div></div>
                    </div>
                    <div class="rounded-2xl glass p-6 text-left">
                        <p class="text-3xl font-bold text-white">₲ 4.2M</p>
                        <p class="mt-1 text-sm text-surface-400">Presupuesto real vs. estimado</p>
                        <span class="badge badge-success mt-3">Dentro de rango</span>
                    </div>
                    <div class="rounded-2xl glass p-6 text-left">
                        <p class="text-3xl font-bold text-white">14</p>
                        <p class="mt-1 text-sm text-surface-400">Tareas del cronograma</p>
                        <span class="badge badge-info mt-3">A tiempo</span>
                    </div>
                </div>
            </section>

            <!-- Features (bento grid) -->
            <section id="features" class="mx-auto max-w-7xl px-6 py-24">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="font-display text-3xl font-bold text-white sm:text-4xl">Todo lo que necesitás para organizar un evento</h2>
                    <p class="mt-4 text-surface-400">Sin planillas dispersas ni WhatsApp perdido. Un solo lugar por evento.</p>
                </div>

                <div class="mt-14 grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div class="rounded-2xl glass p-8 transition hover:-translate-y-1">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-600/20 text-brand-400">
                            <svg class="h-5.5 w-5.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-semibold text-white">Invitados y RSVP</h3>
                        <p class="mt-2 text-sm leading-relaxed text-surface-400">
                            Generá invitaciones digitales, seguí confirmaciones en tiempo real y organizá listas por mesa o categoría.
                        </p>
                    </div>

                    <div class="rounded-2xl glass p-8 transition hover:-translate-y-1">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-accent-600/20 text-accent-400">
                            <svg class="h-5.5 w-5.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-semibold text-white">Planificación del evento</h3>
                        <p class="mt-2 text-sm leading-relaxed text-surface-400">
                            Cronograma de tareas, proveedores y responsables, con estados y fechas límite claras para todo el equipo.
                        </p>
                    </div>

                    <div class="rounded-2xl glass p-8 transition hover:-translate-y-1">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-success-500/20 text-success-500">
                            <svg class="h-5.5 w-5.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-semibold text-white">Control de presupuesto</h3>
                        <p class="mt-2 text-sm leading-relaxed text-surface-400">
                            Comparación en vivo de gasto estimado vs. real por rubro, con alertas antes de pasarte del presupuesto.
                        </p>
                    </div>

                    <div class="rounded-2xl glass p-8 transition hover:-translate-y-1 md:col-span-2">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-warning-500/20 text-warning-500">
                            <svg class="h-5.5 w-5.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-semibold text-white">Multi-empresa y roles</h3>
                        <p class="mt-2 text-sm leading-relaxed text-surface-400">
                            Cada empresa organizadora gestiona sus propios eventos y usuarios, con permisos por rol (admin, organizador).
                        </p>
                    </div>

                    <div class="rounded-2xl glass p-8 transition hover:-translate-y-1">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-danger-500/20 text-danger-500">
                            <svg class="h-5.5 w-5.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-semibold text-white">Reportes por evento</h3>
                        <p class="mt-2 text-sm leading-relaxed text-surface-400">
                            Estadísticas de asistencia y presupuesto listas para compartir con clientes o directivos.
                        </p>
                    </div>
                </div>
            </section>

            <!-- How it works -->
            <section id="how" class="mx-auto max-w-7xl px-6 py-24">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="font-display text-3xl font-bold text-white sm:text-4xl">Cómo funciona</h2>
                    <p class="mt-4 text-surface-400">De la idea al evento cerrado, en tres pasos.</p>
                </div>

                <div class="mt-14 grid grid-cols-1 gap-8 md:grid-cols-3">
                    @foreach ([
                        ['n' => '1', 'title' => 'Creá el evento', 'body' => 'Definí fecha, lugar y presupuesto estimado inicial por rubro.'],
                        ['n' => '2', 'title' => 'Invitá y planificá', 'body' => 'Enviá invitaciones, sumá tareas del cronograma y asigná responsables.'],
                        ['n' => '3', 'title' => 'Controlá en tiempo real', 'body' => 'Seguí RSVP y gasto real vs. estimado hasta el día del evento.'],
                    ] as $step)
                        <div class="text-center">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full gradient-brand font-display text-lg font-bold text-white shadow-glow">
                                {{ $step['n'] }}
                            </div>
                            <h3 class="mt-4 text-lg font-semibold text-white">{{ $step['title'] }}</h3>
                            <p class="mt-2 text-sm text-surface-400">{{ $step['body'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- Pricing -->
            <section id="pricing" class="mx-auto max-w-7xl px-6 py-24">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="font-display text-3xl font-bold text-white sm:text-4xl">Planes para cada tamaño de equipo</h2>
                    <p class="mt-4 text-surface-400">Precios simples. Sin costos ocultos por invitado.</p>
                </div>

                <div class="mt-14 grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div class="rounded-2xl glass p-8">
                        <h3 class="text-lg font-semibold text-white">Starter</h3>
                        <p class="mt-2 text-sm text-surface-400">Para organizadores independientes.</p>
                        <p class="mt-6"><span class="text-4xl font-bold text-white">Gs. 250.000</span><span class="text-surface-400">/mes</span></p>
                        <ul class="mt-6 space-y-3 text-sm text-surface-300">
                            <li class="flex gap-2"><svg class="h-5 w-5 shrink-0 text-success-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>Hasta 3 eventos activos</li>
                            <li class="flex gap-2"><svg class="h-5 w-5 shrink-0 text-success-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>Invitados y RSVP ilimitados</li>
                            <li class="flex gap-2"><svg class="h-5 w-5 shrink-0 text-success-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>Presupuesto por evento</li>
                        </ul>
                        <a href="#contact" class="mt-8 block rounded-lg glass px-4 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-white/10">Empezar</a>
                    </div>

                    <div class="relative rounded-2xl gradient-brand p-8 shadow-glow ring-1 ring-white/20">
                        <span class="absolute -top-3 right-8 rounded-full bg-white px-3 py-1 text-xs font-bold text-brand-700">Más elegido</span>
                        <h3 class="text-lg font-semibold text-white">Business</h3>
                        <p class="mt-2 text-sm text-white/80">Para empresas de eventos con equipo.</p>
                        <p class="mt-6"><span class="text-4xl font-bold text-white">Gs. 650.000</span><span class="text-white/80">/mes</span></p>
                        <ul class="mt-6 space-y-3 text-sm text-white/90">
                            <li class="flex gap-2"><svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>Eventos y usuarios ilimitados</li>
                            <li class="flex gap-2"><svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>Roles y permisos por organizador</li>
                            <li class="flex gap-2"><svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>Reportes y exportación</li>
                        </ul>
                        <a href="#contact" class="mt-8 block rounded-lg bg-white px-4 py-2.5 text-center text-sm font-semibold text-brand-700 transition hover:bg-white/90">Solicitar demo</a>
                    </div>

                    <div class="rounded-2xl glass p-8">
                        <h3 class="text-lg font-semibold text-white">Enterprise</h3>
                        <p class="mt-2 text-sm text-surface-400">Para empresas con múltiples marcas o sedes.</p>
                        <p class="mt-6"><span class="text-4xl font-bold text-white">A medida</span></p>
                        <ul class="mt-6 space-y-3 text-sm text-surface-300">
                            <li class="flex gap-2"><svg class="h-5 w-5 shrink-0 text-success-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>Multi-empresa</li>
                            <li class="flex gap-2"><svg class="h-5 w-5 shrink-0 text-success-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>Soporte prioritario</li>
                            <li class="flex gap-2"><svg class="h-5 w-5 shrink-0 text-success-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>Integraciones a medida</li>
                        </ul>
                        <a href="#contact" class="mt-8 block rounded-lg glass px-4 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-white/10">Hablar con ventas</a>
                    </div>
                </div>
            </section>

            <!-- FAQ (native disclosure, no JS) -->
            <section id="faq" class="mx-auto max-w-3xl px-6 py-24">
                <h2 class="text-center font-display text-3xl font-bold text-white sm:text-4xl">Preguntas frecuentes</h2>

                <div class="mt-10 space-y-3">
                    @foreach ([
                        ['q' => '¿Cómo cargamos los invitados de un evento?', 'a' => 'Podés importarlos o agregarlos manualmente por evento; cada invitado recibe un enlace único de invitación con confirmación de asistencia (RSVP).'],
                        ['q' => '¿Cómo funciona el control de presupuesto?', 'a' => 'Definís rubros con un monto estimado y vas registrando los gastos reales y pagos; Planora muestra el desvío en tiempo real por evento.'],
                        ['q' => '¿Varias personas pueden trabajar en el mismo evento?', 'a' => 'Sí, cada empresa gestiona sus propios usuarios con roles (admin, organizador) y permisos sobre sus eventos.'],
                        ['q' => '¿Necesito instalar algo?', 'a' => 'No, Planora funciona 100% en el navegador, sin instalaciones.'],
                    ] as $item)
                        <details class="group rounded-xl glass p-5">
                            <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold text-white">
                                {{ $item['q'] }}
                                <svg class="h-5 w-5 shrink-0 text-surface-400 transition group-open:rotate-45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                            </summary>
                            <p class="mt-3 text-sm leading-relaxed text-surface-400">{{ $item['a'] }}</p>
                        </details>
                    @endforeach
                </div>
            </section>

            <!-- Final CTA / Contact -->
            <section id="contact" class="mx-auto max-w-7xl px-6 py-24">
                <div class="rounded-3xl glass px-8 py-14 text-center sm:px-16">
                    <h2 class="font-display text-3xl font-bold text-white sm:text-4xl">Llevá tus eventos a otro nivel</h2>
                    <p class="mx-auto mt-4 max-w-xl text-surface-300">
                        Contanos sobre tu empresa y te mostramos Planora funcionando con un evento real.
                    </p>
                    <a href="mailto:ventas@planora.app" class="mt-8 inline-block rounded-lg gradient-brand px-8 py-3 text-base font-semibold text-white shadow-glow transition hover:opacity-90">
                        Solicitar demo gratuita
                    </a>
                </div>
            </section>
        </main>

        <footer class="border-t border-white/5 px-6 py-10 text-center text-sm text-surface-500">
            © {{ date('Y') }} Planora. Todos los derechos reservados.
        </footer>
    </body>
</html>
