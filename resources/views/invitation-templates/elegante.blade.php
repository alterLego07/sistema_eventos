<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $event->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Cormorant+Garamond:wght@300;400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --gold:   {{ $config['colors']['primary']    ?? '#C9A96E' }};
            --gold2:  {{ $config['colors']['accent']     ?? '#D4AF37' }};
            --dark:   {{ $config['colors']['background'] ?? '#0f0f23' }};
            --dark2:  {{ $config['colors']['secondary']  ?? '#1a1a2e' }};
            --light:  {{ $config['colors']['text']       ?? '#f5f5f5' }};
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--dark);
            color: var(--light);
            font-family: 'Cormorant Garamond', Georgia, serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Starfield */
        .stars {
            position: fixed; inset: 0; z-index: 0; overflow: hidden; pointer-events: none;
        }
        .star {
            position: absolute; background: #fff; border-radius: 50%;
            animation: twinkle var(--dur) ease-in-out infinite;
        }
        @keyframes twinkle {
            0%,100% { opacity: 0.1; } 50% { opacity: 0.6; }
        }

        /* Gold line divider */
        .divider {
            display: flex; align-items: center; gap: 1rem; margin: 2rem 0;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }
        .divider-diamond {
            width: 8px; height: 8px; background: var(--gold);
            transform: rotate(45deg); flex-shrink: 0;
        }

        /* Countdown */
        .countdown-box {
            background: rgba(201,169,110,0.08);
            border: 1px solid rgba(201,169,110,0.2);
            border-radius: 1rem;
            padding: 0.75rem 1.25rem;
            text-align: center;
            min-width: 70px;
        }
        .countdown-number {
            font-family: 'Playfair Display', serif;
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--gold);
            line-height: 1;
        }
        .countdown-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            opacity: 0.5;
            margin-top: 0.25rem;
        }

        /* RSVP Card */
        .rsvp-card {
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(201,169,110,0.15);
            border-radius: 1.5rem;
            padding: 2.5rem 2rem;
        }

        .rsvp-btn {
            flex: 1; padding: 0.875rem 1rem;
            border: 1.5px solid rgba(201,169,110,0.3);
            border-radius: 0.75rem;
            background: transparent;
            color: var(--light);
            font-family: 'Cormorant Garamond', serif;
            font-size: 1rem; letter-spacing: 0.05em;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .rsvp-btn.selected-yes {
            background: rgba(201,169,110,0.15);
            border-color: var(--gold);
            color: var(--gold);
        }
        .rsvp-btn.selected-no {
            background: rgba(220,38,38,0.1);
            border-color: rgba(220,38,38,0.4);
            color: #fca5a5;
        }

        .gold-input {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(201,169,110,0.2);
            border-radius: 0.625rem;
            padding: 0.75rem 1rem;
            color: var(--light);
            font-family: 'Cormorant Garamond', serif;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.2s;
        }
        .gold-input::placeholder { opacity: 0.35; }
        .gold-input:focus { border-color: var(--gold); }

        .gold-label {
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--gold);
            opacity: 0.8;
            margin-bottom: 0.5rem;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--gold), var(--gold2));
            color: var(--dark);
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            border: none;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(201,169,110,0.35);
        }

        /* Sections */
        section { position: relative; z-index: 1; }
    </style>
</head>
<body x-data="invitacion()" x-init="init()">

    {{-- Starfield --}}
    <div class="stars" id="stars"></div>

    {{-- ── HERO ──────────────────────────────────────────────────────── --}}
    @if(in_array('hero', $config['sections'] ?? ['hero']))
    <section class="min-h-screen flex flex-col items-center justify-center text-center px-6 py-20">
        <p class="text-xs uppercase tracking-[0.3em] mb-6" style="color: var(--gold); opacity: 0.7">
            Tenés el honor de ser invitado/a
        </p>
        <h1 class="text-5xl sm:text-7xl font-bold mb-4 leading-tight"
            style="font-family: 'Playfair Display', serif; color: var(--gold)">
            {{ $event->name }}
        </h1>
        <div class="divider" style="width: min(400px, 80%)">
            <div class="divider-diamond"></div>
        </div>
        <p class="text-xl sm:text-2xl mb-2" style="font-family: 'Playfair Display', serif; font-style: italic">
            {{ $event->formatted_date }}
        </p>
        <p class="text-base opacity-60">{{ $event->formatted_time }}</p>
        @if($event->location)
            <p class="text-sm mt-3 opacity-50">{{ $event->location }}</p>
        @endif

        <div class="mt-10 px-6 py-3 rounded-full border text-sm tracking-widest uppercase"
             style="border-color: rgba(201,169,110,0.3); color: var(--gold)">
            {{ $invitation->guest_name }}
        </div>

        <a href="#rsvp" class="mt-16 animate-bounce opacity-40">
            <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 style="color: var(--gold)">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/>
            </svg>
        </a>
    </section>
    @endif

    {{-- ── MESSAGE ───────────────────────────────────────────────────── --}}
    @if(in_array('message', $config['sections'] ?? []) && $event->description)
    <section class="max-w-xl mx-auto px-6 py-16 text-center">
        <div class="divider"><div class="divider-diamond"></div></div>
        <p class="text-xl leading-relaxed opacity-80" style="font-style: italic">
            {{ $event->description }}
        </p>
        <div class="divider"><div class="divider-diamond"></div></div>
    </section>
    @endif

    {{-- ── COUNTDOWN ─────────────────────────────────────────────────── --}}
    @if(in_array('countdown', $config['sections'] ?? []))
    <section class="max-w-xl mx-auto px-6 py-12 text-center">
        <p class="text-xs uppercase tracking-[0.25em] mb-8 opacity-50">Faltan</p>
        <div class="flex justify-center gap-3 sm:gap-5" id="countdown-boxes">
            @foreach(['days' => 'Días', 'hours' => 'Horas', 'minutes' => 'Min', 'seconds' => 'Seg'] as $key => $label)
            <div class="countdown-box">
                <div class="countdown-number" id="cd-{{ $key }}">--</div>
                <div class="countdown-label">{{ $label }}</div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ── RSVP ──────────────────────────────────────────────────────── --}}
    @if(in_array('rsvp', $config['sections'] ?? ['rsvp']))
    <section id="rsvp" class="max-w-lg mx-auto px-6 py-16">
        <div class="rsvp-card">

            {{-- Already responded state --}}
            @if($invitation->confirmed_at)
                @if($invitation->confirmed)
                    <div class="text-center py-6">
                        <div class="text-5xl mb-4">🥂</div>
                        <h2 class="text-2xl font-bold mb-2" style="font-family:'Playfair Display',serif; color:var(--gold)">
                            ¡Hasta pronto!
                        </h2>
                        <p class="opacity-60 text-sm">
                            Ya confirmaste tu asistencia con {{ $invitation->confirmed_guests }}
                            {{ $invitation->confirmed_guests === 1 ? 'persona' : 'personas' }}.
                        </p>
                        <p class="opacity-40 text-xs mt-2">
                            {{ $invitation->confirmed_at->format('d/m/Y') }}
                        </p>
                    </div>
                @else
                    <div class="text-center py-6">
                        <div class="text-5xl mb-4">💐</div>
                        <h2 class="text-2xl font-bold mb-3" style="font-family:'Playfair Display',serif">
                            Gracias por avisarnos
                        </h2>
                        <p class="opacity-60">Lamentamos que no puedas acompañarnos.</p>
                    </div>
                @endif

            {{-- RSVP Form --}}
            @else
                <h2 class="text-center text-2xl font-bold mb-1" style="font-family:'Playfair Display',serif; color:var(--gold)">
                    Confirmación de asistencia
                </h2>
                <p class="text-center text-sm opacity-50 mb-8">{{ $invitation->guest_name }} · {{ $invitation->allowed_guests }} lugar(es)</p>

                @if($errors->any())
                    <div class="mb-6 p-4 rounded-xl text-sm" style="background: rgba(220,38,38,0.12); color: #fca5a5; border: 1px solid rgba(220,38,38,0.2)">
                        @foreach($errors->all() as $e) <p>· {{ $e }}</p> @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('invitation.rsvp', $invitation->token) }}"
                      x-data="{ attending: {{ old('confirmed') !== null ? old('confirmed') : 'null' }} }">
                    @csrf

                    {{-- Yes / No toggle --}}
                    <div class="flex gap-3 mb-8">
                        <button type="button"
                                @click="attending = 1"
                                :class="attending === 1 ? 'selected-yes' : ''"
                                class="rsvp-btn">
                            ✦ Asistiré con placer
                        </button>
                        <button type="button"
                                @click="attending = 0"
                                :class="attending === 0 ? 'selected-no' : ''"
                                class="rsvp-btn">
                            No podré asistir
                        </button>
                    </div>
                    <input type="hidden" name="confirmed" :value="attending">

                    {{-- Fields shown when attending --}}
                    <div x-show="attending === 1" x-transition class="space-y-5">
                        <div>
                            <label class="gold-label">¿Cuántos asistirán? (máx. {{ $invitation->allowed_guests }})</label>
                            <input type="number" name="confirmed_guests"
                                   min="1" max="{{ $invitation->allowed_guests }}"
                                   value="{{ old('confirmed_guests', 1) }}"
                                   class="gold-input">
                        </div>

                        @if(in_array('dietary', $config['sections'] ?? []))
                        <div>
                            <label class="gold-label">Restricciones alimentarias</label>
                            <input type="text" name="dietary_restrictions"
                                   value="{{ old('dietary_restrictions') }}"
                                   placeholder="Vegetariano, celíaco, alérgico a..."
                                   class="gold-input">
                        </div>
                        @endif

                        @if(in_array('song', $config['sections'] ?? []))
                        <div>
                            <label class="gold-label">🎵 Sugerí una canción</label>
                            <input type="text" name="song_suggestion"
                                   value="{{ old('song_suggestion') }}"
                                   placeholder="Artista — Canción"
                                   class="gold-input">
                        </div>
                        @endif

                        <div>
                            <label class="gold-label">Mensaje para los anfitriones</label>
                            <textarea name="message" rows="3"
                                      placeholder="Opcional..."
                                      class="gold-input" style="resize: none">{{ old('message') }}</textarea>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div x-show="attending !== null" x-transition class="mt-6">
                        <button type="submit" class="submit-btn">
                            Enviar confirmación
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </section>
    @endif

    {{-- ── LOCATION ──────────────────────────────────────────────────── --}}
    @if(in_array('location', $config['sections'] ?? []) && $event->location)
    <section class="max-w-lg mx-auto px-6 py-12 text-center">
        <div class="divider"><div class="divider-diamond"></div></div>
        <p class="text-xs uppercase tracking-[0.25em] mb-4 opacity-50">Dónde nos encontramos</p>
        <p class="text-xl mb-2" style="font-family:'Playfair Display',serif">{{ $event->location }}</p>
        @if($event->location_url)
            <a href="{{ $event->location_url }}" target="_blank"
               class="inline-flex items-center gap-2 text-sm mt-3 px-5 py-2.5 rounded-full border transition-all hover:bg-white/5"
               style="color: var(--gold); border-color: rgba(201,169,110,0.3)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Ver en Google Maps
            </a>
        @endif
        <div class="divider mt-8"><div class="divider-diamond"></div></div>
    </section>
    @endif

    {{-- ── FOOTER ────────────────────────────────────────────────────── --}}
    @if(in_array('footer', $config['sections'] ?? []))
    <footer class="text-center py-12 px-6 opacity-30 text-xs tracking-widest uppercase">
        <p>Con cariño · {{ $event->name }}</p>
    </footer>
    @endif

    <script>
    function invitacion() {
        return {
            init() {
                this.generarEstrellas();
                @if(in_array('countdown', $config['sections'] ?? []))
                this.iniciarCountdown();
                @endif
            },
            generarEstrellas() {
                const c = document.getElementById('stars');
                if (!c) return;
                for (let i = 0; i < 120; i++) {
                    const s = document.createElement('div');
                    const size = Math.random() * 2 + 1;
                    s.className = 'star';
                    s.style.cssText = `
                        left: ${Math.random()*100}%;
                        top: ${Math.random()*100}%;
                        width: ${size}px; height: ${size}px;
                        --dur: ${(Math.random()*3+2).toFixed(1)}s;
                        animation-delay: ${(Math.random()*4).toFixed(1)}s;
                    `;
                    c.appendChild(s);
                }
            },
            iniciarCountdown() {
                const target = new Date('{{ $event->event_date->format("Y-m-d") }}T{{ substr($event->event_time, 0, 5) }}:00');
                const tick = () => {
                    const now  = new Date();
                    const diff = target - now;
                    if (diff <= 0) {
                        ['days','hours','minutes','seconds'].forEach(k => {
                            const el = document.getElementById('cd-'+k);
                            if (el) el.textContent = '0';
                        });
                        return;
                    }
                    const d = Math.floor(diff / 86400000);
                    const h = Math.floor((diff % 86400000) / 3600000);
                    const m = Math.floor((diff % 3600000)  / 60000);
                    const s = Math.floor((diff % 60000)    / 1000);
                    [['days',d],['hours',h],['minutes',m],['seconds',s]].forEach(([k,v]) => {
                        const el = document.getElementById('cd-'+k);
                        if (el) el.textContent = String(v).padStart(2,'0');
                    });
                };
                tick();
                setInterval(tick, 1000);
            }
        };
    }
    </script>
</body>
</html>
