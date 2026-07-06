@php
    use Carbon\Carbon;

    $sections = $config['sections'] ?? [
        'hero',
        'message',
        'details',
        'gifts',
        'countdown',
        'quote',
        'rsvp',
        'music',
        'footer',
    ];

    $eventName = $event->name ?? 'Josefina & Emilio';
    $names = preg_split('/\s*&\s*/', $eventName);
    $firstName = $config['couple']['first_name'] ?? ($names[0] ?? 'Josefina');
    $secondName = $config['couple']['second_name'] ?? ($names[1] ?? 'Emilio');

    $eventDate = $event->event_date ?? $event->date ?? null;
    $eventTime = $event->event_time ?? $event->time ?? '09:30:00';

    $eventDateObj = $eventDate instanceof \Carbon\CarbonInterface
        ? $eventDate->copy()
        : Carbon::parse($eventDate ?? '2026-09-13');

    $timeText = substr((string) $eventTime, 0, 5);
    $eventDateTimeIso = $eventDateObj->format('Y-m-d') . 'T' . $timeText . ':00';

    $formattedDate = $event->formatted_date ?? $eventDateObj->locale('es')->translatedFormat('l d \d\e F');
    $formattedTime = $event->formatted_time ?? $timeText . ' hs';

    $monthName = ucfirst($eventDateObj->locale('es')->translatedFormat('F'));
    $dayNumber = $eventDateObj->format('d');
    $yearNumber = $eventDateObj->format('Y');
    $dayName = ucfirst($eventDateObj->locale('es')->translatedFormat('l'));

    $heroText = $config['texts']['hero']
        ?? 'Queremos que seas parte de este día tan especial, rodeados de las personas que queremos y que forman parte de nuestra historia.';

    $messageTitle = $config['texts']['message_title'] ?? 'Nos encantaría compartir este día contigo';
    $messageText = $event->description
        ?? $config['texts']['message']
        ?? 'Después de tantos momentos vividos y sueños compartidos, llegó el día de celebrar nuestra unión. Queremos hacerlo rodeados de las personas que queremos, por eso nos haría muy felices contar con tu presencia.';

    $quote = $config['texts']['quote'] ?? 'El amor no mira con los ojos, sino con el alma.';
    $rsvpDeadline = $config['rsvp']['deadline'] ?? null;
    $audioPath = $config['audio']['src'] ?? asset('assets/audio/song.mp3');

    $details = $config['details'] ?? [
        [
            'icon' => '✦',
            'title' => 'Ceremonia Religiosa',
            'name' => $event->location ?? 'Iglesia de Areguá',
            'description' => $dayName . ' ' . $eventDateObj->format('d') . ' de ' . $eventDateObj->locale('es')->translatedFormat('F') . ' · ' . $formattedTime,
            'map_url' => $event->location_url ?? null,
            'button' => 'Ver ubicación',
        ],
        [
            'icon' => '❀',
            'title' => 'Recepción',
            'name' => $config['reception']['name'] ?? 'Quinta Tio Toño',
            'description' => $config['reception']['description'] ?? 'Brindis, almuerzo y baile.',
            'extra' => $config['reception']['dress_code'] ?? 'Dress code: Tipo Cóctel',
            'map_url' => $config['reception']['location_url'] ?? null,
            'button' => 'Ver ubicación',
        ],
    ];

    $theme = $config['colors'] ?? [];

    $giftTitle = $config['texts']['gift_title'] ?? 'Un detalle especial';
    $giftText = $config['texts']['gift_text']
        ?? 'Tu presencia es nuestro mejor regalo. Pero si deseás tener un detalle con nosotros, podés hacerlo mediante una transferencia bancaria.';

    $bankData = $config['bank'] ?? [
        'bank_name' => 'Banco Ejemplo',
        'account_holder' => 'Josefina Bogado',
        'document' => '0000000',
        'account_type' => 'Caja de ahorro',
        'account_number' => '0000000000',
        'currency' => 'Guaraníes',
        'alias' => null,
    ];


@endphp
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="{{ $theme['background'] ?? '#B2CF9D' }}">

    <title>Invitación de Boda</title>
    <meta name="description" content="Invitación de boda con detalles del evento, cuenta regresiva y confirmación de asistencia.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Playfair+Display:wght@500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    {{-- Si ya usás Vite en el proyecto, podés dejarlo activo. --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg: {{ $theme['background'] ?? '#B2CF9D' }};
            --bg-soft: {{ $theme['secondary'] ?? '#C9DDBB' }};
            --green-deep: {{ $theme['text'] ?? '#2F4425' }};
            --green-muted: rgba(47, 68, 37, 0.68);
            --paper: rgba(255, 255, 255, 0.58);
            --paper-strong: rgba(255, 255, 255, 0.78);
            --line: rgba(47, 68, 37, 0.16);
            --gold: {{ $theme['primary'] ?? '#7A5C12' }};
            --gold-soft: rgba(122, 92, 18, 0.18);
            --shadow: 0 20px 55px rgba(38, 58, 28, 0.16);
            --radius: 28px;
            --title: "Playfair Display", serif;
            --script: "Great Vibes", cursive;
            --body: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; scroll-padding-top: 92px; }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--green-deep);
            font-family: var(--body);
            background:
                radial-gradient(circle at 12% 8%, rgba(255, 255, 255, .46), transparent 28%),
                radial-gradient(circle at 90% 22%, rgba(255, 255, 255, .25), transparent 32%),
                linear-gradient(145deg, var(--bg), #A4C88D 58%, var(--bg-soft));
            overflow-x: hidden;
        }

        body::before,
        body::after {
            content: "";
            position: fixed;
            z-index: -1;
            width: 260px;
            height: 260px;
            border: 1px solid rgba(122, 92, 18, .12);
            border-radius: 46% 54% 52% 48%;
            pointer-events: none;
        }

        body::before { top: -80px; left: -70px; transform: rotate(18deg); }
        body::after { right: -90px; bottom: 6%; transform: rotate(-22deg); }

        .page-bg {
            position: fixed;
            inset: 0;
            z-index: -2;
            opacity: .08;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='260' height='260'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='260' height='260' filter='url(%23n)' opacity='.55'/%3E%3C/svg%3E");
            mix-blend-mode: multiply;
        }

        img, iframe { max-width: 100%; }
        a { color: inherit; text-decoration: none; }
        p { line-height: 1.75; }

        .container { width: min(520px, calc(100% - 28px)); margin-inline: auto; }

        .section { padding: 54px 0; }
        .section-pad { padding: 42px 0 38px; }
        .hero { min-height: auto; display: grid; align-items: center; text-align: center; }
        .hero__grid { display: grid; grid-template-columns: 1fr; gap: 22px; align-items: center; }

        .eyebrow, .section-kicker {
            display: inline-flex; margin: 0 0 12px; color: var(--gold); font-weight: 700;
            font-size: 13px; letter-spacing: .12em; text-transform: uppercase;
        }

        .couple-name { margin: 0; font-family: var(--script); font-size: clamp(72px, 11vw, 145px); font-weight: 400; line-height: .84; text-wrap: balance; }
        .couple-name small { display: block; margin: 10px 0; font-family: var(--title); font-size: clamp(32px, 5vw, 56px); color: var(--gold); }
        .hero__text { max-width: 420px; margin: 20px auto 0; color: var(--green-muted); font-size: clamp(15px, 4vw, 18px); }
        .hero__actions { display: grid; gap: 12px; margin-top: 24px; }

        .btn {
            min-height: 46px; display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 12px 18px; border: 1px solid rgba(122, 92, 18, .35); border-radius: 999px;
            color: var(--green-deep); font-weight: 700;
            background: linear-gradient(180deg, rgba(255,255,255,.8), rgba(255,255,255,.44));
            box-shadow: 0 10px 24px rgba(47, 68, 37, .13);
            transition: transform .2s ease, box-shadow .2s ease;
            cursor: pointer;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 14px 28px rgba(47, 68, 37, .18); }
        .btn--soft { background: rgba(255,255,255,.32); border-color: var(--line); color: var(--green-muted); }
        .btn--full { width: 100%; }

        .date-card, .intro-card, .detail-card, .form-card, .quote-card {
            border: 1px solid var(--line); border-radius: var(--radius); background: var(--paper);
            box-shadow: var(--shadow); backdrop-filter: blur(14px);
        }

        .date-card {
            position: relative; overflow: hidden; min-height: 310px; display: grid; place-items: center;
            align-content: center; padding: 30px 22px; text-align: center;
        }
        .date-card::before, .date-card::after { content: "✦"; position: absolute; color: rgba(122, 92, 18, .22); font-size: 58px; }
        .date-card::before { top: 24px; left: 24px; }
        .date-card::after { right: 24px; bottom: 24px; }
        .date-card__month, .date-card__year { font-family: var(--title); font-size: 24px; }
        .date-card__day { margin: 8px 0; font-family: var(--title); font-size: clamp(76px, 22vw, 104px); line-height: .9; color: var(--gold); }
        .date-card__line { width: 86px; height: 1px; margin: 22px auto 10px; background: rgba(122, 92, 18, .35); }
        .date-card p { margin: 4px 0; color: var(--green-muted); }

        .intro-card { position: relative; padding: clamp(28px, 5vw, 52px); text-align: center; }
        .ornament { color: var(--gold); font-size: 34px; }
        .intro-card h2, .section-head h2 { margin: 0 0 12px; font-family: var(--title); font-size: clamp(32px, 5vw, 54px); line-height: 1.08; }
        .intro-card p, .section-head p { max-width: 720px; margin: 0 auto; color: var(--green-muted); }
        .section-head { margin-bottom: 26px; }
        .section-head--center { text-align: center; }

        .details-grid, .rsvp-grid { display: grid; grid-template-columns: 1fr; gap: 18px; }
        .detail-card, .form-card { padding: 24px; }
        .detail-card { display: flex; flex-direction: column; gap: 12px; }
        .detail-card .btn { margin-top: auto; }
        .detail-card__icon { width: 42px; height: 42px; display: grid; place-items: center; margin-bottom: 4px; border-radius: 50%; color: var(--gold); background: var(--gold-soft); }
        .detail-card h3 { margin: 0 0 10px; font-family: var(--title); font-size: 24px; }
        .detail-card p { margin: 8px 0; color: var(--green-muted); }
        .detail-card strong { color: var(--green-deep); }

        .section--glass { border-block: 1px solid var(--line); background: rgba(255,255,255,.18); }
        .countdown { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
        .time-box {
            min-height: 132px; display: grid; place-items: center; align-content: center; gap: 8px; padding: 20px 12px;
            text-align: center; border: 1px solid rgba(122, 92, 18, .18); border-radius: 24px;
            background: var(--paper-strong); box-shadow: 0 14px 30px rgba(47, 68, 37, .1);
        }
        .time-box strong { font-family: var(--title); font-size: clamp(34px, 6vw, 58px); line-height: 1; }
        .time-box span { color: var(--green-muted); font-size: 12px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
        .countdown-msg { margin: 18px 0 0; text-align: center; color: var(--green-muted); }

        .quote-card { padding: clamp(28px, 5vw, 52px); text-align: center; }
        .quote-card span { display: block; height: 44px; color: var(--gold); font-family: var(--title); font-size: 86px; line-height: .8; }
        .quote-card p { margin: 0; font-family: var(--title); font-size: clamp(26px, 4vw, 44px); line-height: 1.25; }

        .clean-list { margin: 0; padding-left: 18px; color: var(--green-muted); line-height: 1.9; }
        .form-card form { display: grid; gap: 18px; }
        .rsvp-buttons { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .rsvp-btn {
            width: 100%; min-height: 48px; padding: 12px 14px; border: 1px solid rgba(122, 92, 18, .26);
            border-radius: 18px; background: rgba(255,255,255,.28); color: var(--green-muted); font-weight: 700;
            cursor: pointer; transition: all .2s ease;
        }
        .rsvp-btn.is-selected-yes { background: var(--paper-strong); color: var(--green-deep); border-color: rgba(122, 92, 18, .5); }
        .rsvp-btn.is-selected-no { background: rgba(185, 28, 28, .10); color: #8f1d1d; border-color: rgba(185, 28, 28, .25); }
        .form-group { display: grid; gap: 8px; }
        .form-label { color: var(--gold); font-size: 12px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
        .form-input {
            width: 100%; border: 1px solid var(--line); border-radius: 18px; padding: 13px 15px;
            background: rgba(255,255,255,.42); color: var(--green-deep); font: inherit; outline: none;
        }
        .form-input:focus { border-color: rgba(122, 92, 18, .45); background: rgba(255,255,255,.62); }
        textarea.form-input { min-height: 96px; resize: vertical; }
        .form-error { padding: 14px 16px; border-radius: 18px; background: rgba(185, 28, 28, .10); color: #8f1d1d; border: 1px solid rgba(185, 28, 28, .18); }
        .rsvp-status { text-align: center; padding: 22px 8px; }
        .rsvp-status__icon { font-size: 44px; margin-bottom: 12px; }
        .rsvp-status h3 { margin: 0 0 8px; font-family: var(--title); font-size: 28px; color: var(--gold); }
        .rsvp-status p { margin: 0; color: var(--green-muted); }
        .is-hidden { display: none !important; }

        .music-player {
            position: fixed; right: 18px; bottom: 18px; z-index: 40; display: flex; align-items: center; gap: 12px;
            max-width: calc(100% - 36px); padding: 10px 10px 10px 16px; border: 1px solid var(--line); border-radius: 999px;
            background: rgba(255,255,255,.68); backdrop-filter: blur(16px); box-shadow: 0 14px 34px rgba(47, 68, 37, .16);
        }
        .music-player div { display: grid; }
        .music-player strong { font-size: 13px; }
        .music-player span { color: var(--green-muted); font-size: 12px; }
        .music-btn { width: 42px; height: 42px; border: 0; border-radius: 50%; color: #fff; background: var(--gold); cursor: pointer; }

        .footer { padding: 34px 0 90px; }
        .footer__inner { display: flex; justify-content: space-between; gap: 18px; padding-top: 20px; border-top: 1px solid var(--line); color: var(--green-muted); }
        .footer p { margin: 0; }

        /* Invitado personalizado, con presencia discreta */
        .guest-pill {
            width: fit-content;
            max-width: 100%;
            margin: 22px auto 0;
            padding: 12px 18px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: rgba(255,255,255,.42);
            text-align: center;
            box-shadow: 0 10px 24px rgba(47, 68, 37, .10);
            backdrop-filter: blur(12px);
        }
        .guest-pill span {
            display: block;
            font-size: 10px;
            color: var(--gold);
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
        }
        .guest-pill strong {
            display: block;
            margin-top: 3px;
            font-family: var(--title);
            font-size: clamp(20px, 6vw, 28px);
            color: var(--green-deep);
        }
        .hero-confirm-btn { margin-top: 4px; }
        .map-btn { margin-top: auto; }

        .qr-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
        }
        .qr-wrap a {
            display: block;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--line);
            box-shadow: 0 6px 18px rgba(47, 68, 37, .10);
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .qr-wrap a:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(47, 68, 37, .18); }
        .qr-wrap img { display: block; width: 150px; height: 150px; }
        .qr-wrap span {
            font-size: 11px;
            color: var(--green-muted);
            font-weight: 600;
            letter-spacing: .06em;
        }

        .reveal { opacity: 0; transform: translateY(16px); transition: opacity .75s ease, transform .75s ease; }
        .reveal.is-visible { opacity: 1; transform: translateY(0); }

        .gift-card {
            border: 1px solid var(--line);
            border-radius: var(--radius);
            background: var(--paper);
            box-shadow: var(--shadow);
            backdrop-filter: blur(14px);
            padding: clamp(28px, 6vw, 48px);
            text-align: center;
        }

        .gift-icon {
            width: 54px;
            height: 54px;
            display: grid;
            place-items: center;
            margin: 0 auto 14px;
            border-radius: 50%;
            color: var(--gold);
            background: var(--gold-soft);
            font-size: 28px;
        }

        .gift-card h2 {
            margin: 0 0 12px;
            font-family: var(--title);
            font-size: clamp(32px, 8vw, 48px);
            line-height: 1.08;
        }

        .gift-card p {
            max-width: 620px;
            margin: 0 auto 22px;
            color: var(--green-muted);
        }

        .gift-btn {
            margin-top: 4px;
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: grid;
            place-items: center;
            padding: 18px;
            background: rgba(28, 42, 21, 0.45);
            backdrop-filter: blur(8px);
        }

        .gift-modal {
            position: relative;
            width: min(100%, 430px);
            max-height: calc(100svh - 36px);
            overflow-y: auto;
            border: 1px solid var(--line);
            border-radius: 28px;
            background: rgba(245, 250, 240, 0.96);
            box-shadow: 0 24px 70px rgba(28, 42, 21, 0.28);
            padding: 26px 20px 22px;
            color: var(--green-deep);
        }

        .gift-modal__close {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 38px;
            height: 38px;
            border: 1px solid var(--line);
            border-radius: 50%;
            background: rgba(255,255,255,.58);
            color: var(--green-deep);
            font-size: 26px;
            line-height: 1;
            cursor: pointer;
        }

        .gift-modal__header {
            text-align: center;
            padding: 10px 12px 18px;
        }

        .gift-modal__icon {
            width: 48px;
            height: 48px;
            display: grid;
            place-items: center;
            margin: 0 auto 12px;
            border-radius: 50%;
            color: var(--gold);
            background: var(--gold-soft);
            font-size: 26px;
        }

        .gift-modal__header h3 {
            margin: 0 0 8px;
            font-family: var(--title);
            font-size: 32px;
        }

        .gift-modal__header p {
            margin: 0;
            color: var(--green-muted);
            font-size: 14px;
        }

        .bank-data {
            display: grid;
            gap: 10px;
            margin-bottom: 18px;
        }

        .bank-row {
            display: grid;
            gap: 4px;
            padding: 13px 14px;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: rgba(255,255,255,.48);
        }

        .bank-row span {
            color: var(--gold);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .bank-row strong {
            color: var(--green-deep);
            font-size: 15px;
            overflow-wrap: anywhere;
        }

        .copy-status {
            margin: 12px 0 0;
            text-align: center;
            color: var(--gold);
            font-weight: 700;
            font-size: 14px;
        }



        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { animation: none !important; transition: none !important; }
            .reveal { opacity: 1; transform: none; }
        }

        @media (max-width: 560px) {
            .container { width: min(100% - 24px, 520px); }
            .section { padding: 46px 0; }
            .section-pad { padding: 34px 0 34px; }
            .couple-name { font-size: clamp(64px, 22vw, 94px); }
            .hero__actions .btn { width: 100%; }
            .date-card { min-height: 286px; }
            .countdown { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .time-box { min-height: 112px; border-radius: 20px; }
            .detail-card, .form-card { padding: 20px; }
            .rsvp-buttons { grid-template-columns: 1fr; }
            .music-player { right: 12px; bottom: 12px; left: 12px; justify-content: space-between; }
            .footer__inner { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>
<div class="page-bg" aria-hidden="true"></div>

<main>

    <div class="container">
        <div class="guest-pill reveal">
            <span>Invitación para</span>
            <strong>{{ $invitation->guest_name }}</strong>
        </div>
    </div>

    @if(in_array('hero', $sections))
        <section class="hero section-pad">
            <div class="container hero__grid">
                <div class="hero__content reveal">
                    <p class="eyebrow">{{ $config['texts']['eyebrow'] ?? 'Tenemos el honor de invitarte' }}</p>

                    <h1 class="couple-name">
                        <span>{{ $firstName }}</span>
                        <small>&</small>
                        <span>{{ $secondName }}</span>
                    </h1>

                    <p class="hero__text">{{ $heroText }}</p>

                    <div class="hero__actions">
                        @if(in_array('rsvp', $sections))
                            <a class="btn btn--full hero-confirm-btn" href="#confirmacion">Confirmar asistencia</a>
                        @endif
                        @if(in_array('details', $sections))
                            <a class="btn btn--full btn--soft" href="#detalles">Ver detalles</a>
                        @endif
                    </div>
                </div>

                <aside class="date-card reveal" aria-label="Fecha del evento">
                    <span class="date-card__month">{{ $monthName }}</span>
                    <strong class="date-card__day">{{ $dayNumber }}</strong>
                    <span class="date-card__year">{{ $yearNumber }}</span>
                    <div class="date-card__line"></div>
                    <p>{{ $dayName }} · {{ $formattedTime }}</p>
                    @if($event->location ?? null)
                        <p>{{ $event->location }}</p>
                    @endif
                </aside>
            </div>
        </section>
    @endif

    @if(in_array('message', $sections) && $messageText)
        <section class="section container intro-card reveal" id="historia">
            <span class="ornament">❦</span>
            <h2>{{ $messageTitle }}</h2>
            <p>{{ $messageText }}</p>
        </section>
    @endif

    @if(in_array('details', $sections))
        <section class="section container" id="detalles">
            <div class="section-head reveal">
                <span class="section-kicker">Información importante</span>
                <h2>Detalles del evento</h2>
            </div>

            <div class="details-grid">
                @foreach($details as $detail)
                    <article class="detail-card reveal">
                        <span class="detail-card__icon">{{ $detail['icon'] ?? '✦' }}</span>
                        <h3>{{ $detail['title'] ?? 'Detalle' }}</h3>

                        @if(!empty($detail['name']))
                            <p><strong>{{ $detail['name'] }}</strong></p>
                        @endif

                        @if(!empty($detail['description']))
                            <p>{{ $detail['description'] }}</p>
                        @endif

                        @if(!empty($detail['extra']))
                            <p>{{ $detail['extra'] }}</p>
                        @endif

                        @php
                            $mapUrl = $detail['map_url']
                                ?? $detail['google_maps_url']
                                ?? $detail['location_url']
                                ?? $detail['url']
                                ?? null;
                        @endphp

                        @if(!empty($mapUrl))
                            <div class="qr-wrap">
                                <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Abrir ubicación en Google Maps">
                                    <img
                                        src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=8&data={{ urlencode($mapUrl) }}"
                                        alt="QR de ubicación"
                                        width="150"
                                        height="150"
                                        loading="lazy"
                                    >
                                </a>
                                <span>Escanéalo o tocá para abrir en Maps</span>
                            </div>
                            <a class="btn btn--full btn--soft map-btn" href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer">
                                {{ $detail['button'] ?? 'Ver ubicación' }}
                            </a>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    @if(in_array('gifts', $sections))
        <section class="section container" id="obsequios">
            <div class="gift-card reveal">
                <span class="gift-icon">♡</span>

                <span class="section-kicker">Regalo</span>
                <h2>{{ $giftTitle }}</h2>

                <p>{{ $giftText }}</p>

                <button type="button" class="btn gift-btn" id="openGiftModal">
                    Ver datos bancarios
                </button>
            </div>
        </section>
    @endif

    @if(in_array('countdown', $sections))
        <section class="section section--glass" id="cuenta-regresiva">
            <div class="container">
                <div class="section-head section-head--center reveal">
                    <span class="section-kicker">Falta poco</span>
                    <h2>Cuenta regresiva</h2>
                </div>

                <div class="countdown reveal" role="timer" aria-live="polite">
                    <div class="time-box"><strong id="days">00</strong><span>Días</span></div>
                    <div class="time-box"><strong id="hours">00</strong><span>Horas</span></div>
                    <div class="time-box"><strong id="minutes">00</strong><span>Min</span></div>
                    <div class="time-box"><strong id="seconds">00</strong><span>Seg</span></div>
                </div>

                <p class="countdown-msg reveal" id="countdownMsg">Nos vemos muy pronto ✨</p>
            </div>
        </section>
    @endif

    @if(in_array('quote', $sections))
        <section class="section container">
            <div class="quote-card reveal">
                <span>“</span>
                <p>{{ $quote }}</p>
            </div>
        </section>
    @endif

    @if(in_array('rsvp', $sections))
        <section class="section container" id="confirmacion">
            <div class="section-head reveal">
                <span class="section-kicker">RSVP</span>
                <h2>Confirmá tu asistencia</h2>
                <p>Tu confirmación nos ayuda a organizar mejor cada detalle.</p>
            </div>

            <div class="rsvp-grid">
                <article class="detail-card reveal">
                    <h3>Antes de confirmar</h3>
                    <ul class="clean-list">
                        @if($rsvpDeadline)
                            <li>Confirmar antes del <strong>{{ $rsvpDeadline }}</strong>.</li>
                        @endif

                        @if(($invitation->allowed_guests ?? 1) == 1)
                            <li>Esta invitación es personal e intransferible.</li>
                        @else
                            <li>
                                Esta invitación es válida para
                                <strong>{{ $invitation->allowed_guests }}</strong> personas.
                            </li>
                        @endif

                        <li>
                            Para que todos puedan disfrutar plenamente de la celebración,
                            hemos decidido que este será un evento exclusivo para adultos.
                            Agradecemos mucho tu comprensión y cariño.
                        </li>
                    </ul>
                </article>

                <article class="form-card reveal">
                    @if($invitation->confirmed ?? false)
                        <div class="rsvp-status">
                            <div class="rsvp-status__icon">🥂</div>
                            <h3>¡Hasta pronto!</h3>
                            <p>
                                Ya confirmaste tu asistencia con {{ $invitation->confirmed_guests }}
                                {{ $invitation->confirmed_guests === 1 ? 'persona' : 'personas' }}.
                            </p>
                            @if($invitation->confirmed_at ?? null)
                                <p>{{ $invitation->confirmed_at?->format('d/m/Y') }}</p>
                            @endif
                        </div>
                    @elseif(session('rsvp_confirmed'))
                        <div class="rsvp-status">
                            <div class="rsvp-status__icon">🎊</div>
                            <h3>¡Confirmado!</h3>
                            <p>Nos vemos el {{ $formattedDate }}.</p>
                        </div>
                    @elseif(session('rsvp_declined'))
                        <div class="rsvp-status">
                            <div class="rsvp-status__icon">💐</div>
                            <h3>Gracias por avisarnos</h3>
                            <p>Lamentamos que no puedas acompañarnos.</p>
                        </div>
                    @else
                        @if($errors->any())
                            <div class="form-error">
                                @foreach($errors->all() as $error)
                                    <p>· {{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('invitation.rsvp', $invitation->token) }}" id="rsvpForm">
                            @csrf

                            <div class="rsvp-buttons">
                                <button type="button" class="rsvp-btn" data-rsvp-value="1">✦ Asistiré</button>
                                <button type="button" class="rsvp-btn" data-rsvp-value="0">No podré asistir</button>
                            </div>
                            <input type="hidden" name="confirmed" id="confirmedInput" value="{{ old('confirmed') }}">

                            <div id="attendingFields" class="is-hidden">
                                <div class="form-group">
                                    <label class="form-label">¿Cuántos asistirán? Máx. {{ $invitation->allowed_guests ?? 1 }}</label>
                                    <input class="form-input" type="number" name="confirmed_guests" min="1" max="{{ $invitation->allowed_guests ?? 1 }}" value="{{ old('confirmed_guests', 1) }}">
                                </div>

                                @if(in_array('dietary', $sections))
                                    <div class="form-group">
                                        <label class="form-label">Restricciones alimentarias</label>
                                        <input class="form-input" type="text" name="dietary_restrictions" value="{{ old('dietary_restrictions') }}" placeholder="Vegetariano, celíaco, alérgico a...">
                                    </div>
                                @endif

                                @if(in_array('song', $sections))
                                    <div class="form-group">
                                        <label class="form-label">Sugerí una canción</label>
                                        <input class="form-input" type="text" name="song_suggestion" value="{{ old('song_suggestion') }}" placeholder="Artista — Canción">
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label class="form-label">Mensaje para los anfitriones</label>
                                    <textarea class="form-input" name="message" placeholder="Opcional...">{{ old('message') }}</textarea>
                                </div>
                            </div>

                            <button type="submit" class="btn btn--full" id="submitRsvpBtn" disabled>Enviar confirmación</button>
                        </form>
                    @endif
                </article>
            </div>
        </section>
    @endif

    @if(in_array('music', $sections))
        <section class="music-player" aria-label="Música de fondo">
            <div>
                <strong>Música</strong>
                <span>Activar canción</span>
            </div>
            <button class="music-btn" id="audioToggle" type="button">▶</button>
            <audio id="bgAudio" preload="metadata" loop>
                <source src="{{ $audioPath }}" type="audio/mpeg">
            </audio>
        </section>
    @endif
</main>

@if(in_array('footer', $sections))
    <footer class="footer">
        <div class="container footer__inner">
            <p>{{ $firstName }} &amp; {{ $secondName }}</p>
            <a href="#">Volver arriba ↑</a>
        </div>
    </footer>
@endif

@if(in_array('gifts', $sections))
    <div class="modal-backdrop is-hidden" id="giftModal" aria-hidden="true">
        <div class="gift-modal" role="dialog" aria-modal="true" aria-labelledby="giftModalTitle">
            <button type="button" class="gift-modal__close" id="closeGiftModal" aria-label="Cerrar">
                ×
            </button>

            <div class="gift-modal__header">
                <span class="gift-modal__icon">♡</span>
                <h3 id="giftModalTitle">Datos bancarios</h3>
                <p>Gracias por querer tener un detalle con nosotros.</p>
            </div>

            <div class="bank-data">
                @if(!empty($bankData['bank_name']))
                    <div class="bank-row">
                        <span>Banco</span>
                        <strong>{{ $bankData['bank_name'] }}</strong>
                    </div>
                @endif

                @if(!empty($bankData['account_holder']))
                    <div class="bank-row">
                        <span>Titular</span>
                        <strong>{{ $bankData['account_holder'] }}</strong>
                    </div>
                @endif

                @if(!empty($bankData['document']))
                    <div class="bank-row">
                        <span>Documento</span>
                        <strong>{{ $bankData['document'] }}</strong>
                    </div>
                @endif

                @if(!empty($bankData['account_type']))
                    <div class="bank-row">
                        <span>Tipo de cuenta</span>
                        <strong>{{ $bankData['account_type'] }}</strong>
                    </div>
                @endif

                @if(!empty($bankData['account_number']))
                    <div class="bank-row">
                        <span>Nro. de cuenta</span>
                        <strong>{{ $bankData['account_number'] }}</strong>
                    </div>
                @endif

                @if(!empty($bankData['currency']))
                    <div class="bank-row">
                        <span>Moneda</span>
                        <strong>{{ $bankData['currency'] }}</strong>
                    </div>
                @endif

                @if(!empty($bankData['alias']))
                    <div class="bank-row">
                        <span>Alias</span>
                        <strong>{{ $bankData['alias'] }}</strong>
                    </div>
                @endif
            </div>

            <button type="button" class="btn btn--full btn--soft" id="copyBankData">
                Copiar datos
            </button>

            <p class="copy-status is-hidden" id="copyStatus">
                Datos copiados ✨
            </p>
        </div>
    </div>
@endif

<script>

    const wedding = {
        dateTime: @json($eventDateTimeIso),
    };

    const $ = (selector) => document.querySelector(selector);
    const $$ = (selector) => document.querySelectorAll(selector);

    // — Modal de datos bancarios —
    const giftModal    = $('#giftModal');
    const openGiftModal  = $('#openGiftModal');
    const closeGiftModal = $('#closeGiftModal');
    const copyBankData   = $('#copyBankData');
    const copyStatus     = $('#copyStatus');

    function showGiftModal() {
        if (!giftModal) return;
        giftModal.classList.remove('is-hidden');
        giftModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function hideGiftModal() {
        if (!giftModal) return;
        giftModal.classList.add('is-hidden');
        giftModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    openGiftModal?.addEventListener('click', showGiftModal);
    closeGiftModal?.addEventListener('click', hideGiftModal);

    giftModal?.addEventListener('click', (event) => {
        if (event.target === giftModal) hideGiftModal();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') hideGiftModal();
    });

    copyBankData?.addEventListener('click', async () => {
        const bankText = [
            'Banco: {{ $bankData['bank_name'] ?? '' }}',
            'Titular: {{ $bankData['account_holder'] ?? '' }}',
            'Documento: {{ $bankData['document'] ?? '' }}',
            'Tipo de cuenta: {{ $bankData['account_type'] ?? '' }}',
            'Nro. de cuenta: {{ $bankData['account_number'] ?? '' }}',
            'Moneda: {{ $bankData['currency'] ?? '' }}',
            'Alias: {{ $bankData['alias'] ?? '' }}',
        ].filter(line => !line.endsWith(': ')).join('\n');

        try {
            await navigator.clipboard.writeText(bankText);
            copyStatus?.classList.remove('is-hidden');
            setTimeout(() => copyStatus?.classList.add('is-hidden'), 2200);
        } catch (error) {
            console.warn('No se pudieron copiar los datos bancarios:', error);
        }
    });

    const revealObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("is-visible");
                    revealObserver.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.14 }
    );

    $$(".reveal").forEach((el) => revealObserver.observe(el));

    const countdownEls = {
        days: $("#days"),
        hours: $("#hours"),
        minutes: $("#minutes"),
        seconds: $("#seconds"),
        message: $("#countdownMsg"),
    };

    const pad = (value) => String(value).padStart(2, "0");

    function updateCountdown() {
        if (!countdownEls.days) return;

        const target = new Date(wedding.dateTime).getTime();
        const now = Date.now();
        const diff = target - now;

        if (diff <= 0) {
            countdownEls.days.textContent = "00";
            countdownEls.hours.textContent = "00";
            countdownEls.minutes.textContent = "00";
            countdownEls.seconds.textContent = "00";
            countdownEls.message.textContent = "¡Hoy es el gran día! 🎉";
            return;
        }

        const secondsTotal = Math.floor(diff / 1000);
        const days = Math.floor(secondsTotal / 86400);
        const hours = Math.floor((secondsTotal % 86400) / 3600);
        const minutes = Math.floor((secondsTotal % 3600) / 60);
        const seconds = secondsTotal % 60;

        countdownEls.days.textContent = pad(days);
        countdownEls.hours.textContent = pad(hours);
        countdownEls.minutes.textContent = pad(minutes);
        countdownEls.seconds.textContent = pad(seconds);
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);

    const audio = $("#bgAudio");
    const audioToggle = $("#audioToggle");
    let isPlaying = false;

    function setAudioButton() {
        if (!audioToggle) return;
        audioToggle.textContent = isPlaying ? "Ⅱ" : "▶";
        audioToggle.setAttribute("aria-label", isPlaying ? "Pausar música" : "Reproducir música");
    }

    async function toggleAudio() {
        if (!audio) return;

        try {
            if (!isPlaying) {
                audio.volume = 0.7;
                await audio.play();
                isPlaying = true;
            } else {
                audio.pause();
                isPlaying = false;
            }
            setAudioButton();
        } catch (error) {
            console.warn("No se pudo reproducir el audio:", error);
        }
    }

    audioToggle?.addEventListener("click", toggleAudio);
    setAudioButton();

    const rsvpButtons = $$('[data-rsvp-value]');
    const confirmedInput = $('#confirmedInput');
    const attendingFields = $('#attendingFields');
    const submitRsvpBtn = $('#submitRsvpBtn');

    function setRsvpValue(value) {
        if (!confirmedInput) return;

        confirmedInput.value = value;
        submitRsvpBtn.disabled = false;

        rsvpButtons.forEach((button) => {
            button.classList.remove('is-selected-yes', 'is-selected-no');
        });

        const selectedButton = document.querySelector(`[data-rsvp-value="${value}"]`);

        if (value === '1') {
            selectedButton?.classList.add('is-selected-yes');
            attendingFields?.classList.remove('is-hidden');
        } else {
            selectedButton?.classList.add('is-selected-no');
            attendingFields?.classList.add('is-hidden');
        }
    }

    rsvpButtons.forEach((button) => {
        button.addEventListener('click', () => setRsvpValue(button.dataset.rsvpValue));
    });

    if (confirmedInput?.value === '1' || confirmedInput?.value === '0') {
        setRsvpValue(confirmedInput.value);
    }
</script>
</body>
</html>
