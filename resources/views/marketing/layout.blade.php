<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="light">

    {{-- SEO --}}
    <title>{{ $title ?? 'Kharcha · A household ledger, kept by hand' }}</title>
    <meta name="description" content="{{ $description ?? 'Kharcha is the calm household expense tracker for Pakistani families. Snap a receipt, split with everyone, log fuel, see where the month went.' }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="robots" content="index, follow">

    {{-- OpenGraph --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Kharcha">
    <meta property="og:title" content="{{ $title ?? 'Kharcha · A household ledger, kept by hand' }}">
    <meta property="og:description" content="{{ $description ?? 'Snap a receipt, split with everyone, log fuel, see where the month went. The calm expense tracker for Pakistani households.' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ url('/og.svg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="en_PK">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? 'Kharcha · A household ledger, kept by hand' }}">
    <meta name="twitter:description" content="{{ $description ?? 'Snap a receipt, split with everyone, log fuel, see where the month went.' }}">
    <meta name="twitter:image" content="{{ url('/og.svg') }}">

    {{-- Theme colour for mobile chrome --}}
    <meta name="theme-color" content="#fdf8ee">

    {{-- Favicon as inline SVG: the Mark B logo --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml;utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 96 96'%3E%3Crect x='2' y='2' width='92' height='92' rx='16' fill='%232b1f12'/%3E%3Cpath d='M14 64 L 28 44 L 40 56 L 54 30 L 68 50 L 82 38' fill='none' stroke='%23fdf8ee' stroke-width='2.6' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ccircle cx='28' cy='44' r='2.8' fill='%23c9621f'/%3E%3Ccircle cx='54' cy='30' r='2.8' fill='%23c9621f'/%3E%3Ccircle cx='82' cy='38' r='2.8' fill='%23c9621f'/%3E%3C/svg%3E">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;1,6..72,400;1,6..72,500;1,6..72,600;1,6..72,700&family=Geist:wght@300;400;500;600&family=Geist+Mono:wght@400;500&display=swap">

    {{-- JSON-LD: Organization + SoftwareApplication. Built in PHP to keep "@context" out of Blade's parser. --}}
    @php
        $ldOrigin = url('/');
        $ldOg = url('/og.svg');
        $jsonLd = json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Organization',
                    '@id'   => $ldOrigin.'#org',
                    'name'  => 'Kharcha',
                    'url'   => $ldOrigin,
                    'logo'  => $ldOg,
                ],
                [
                    '@type'              => 'SoftwareApplication',
                    'name'               => 'Kharcha',
                    'applicationCategory'=> 'FinanceApplication',
                    'operatingSystem'    => 'Android, Web',
                    'description'        => 'Household expense tracker with AI receipt scanning, split expenses, and fuel logging.',
                    'offers' => [
                        ['@type' => 'Offer', 'price' => '0',     'priceCurrency' => 'USD', 'name' => 'Free'],
                        ['@type' => 'Offer', 'price' => '1.99',  'priceCurrency' => 'USD', 'name' => 'Pro Monthly'],
                        ['@type' => 'Offer', 'price' => '29.99', 'priceCurrency' => 'USD', 'name' => 'Pro Lifetime'],
                    ],
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    @endphp
    <script type="application/ld+json">{!! $jsonLd !!}</script>

    <style>
        :root {
            /* Colour, all OKLCH, tinted toward the warm brand hue. */
            --bg:           oklch(0.975 0.018 80);
            --bg-deep:      oklch(0.930 0.040 80);
            --card:         oklch(0.990 0.014 85);
            --card-warm:    oklch(0.965 0.030 75);
            --ink:          oklch(0.220 0.024 60);
            --ink-soft:     oklch(0.430 0.030 70);
            --ink-faint:    oklch(0.560 0.038 72);
            --rule:         oklch(0.880 0.038 80);
            --rule-strong:  oklch(0.810 0.058 80);
            --accent:       oklch(0.620 0.160 45);
            --accent-deep:  oklch(0.500 0.160 45);
            --accent-soft:  oklch(0.880 0.060 70);
            --alarm:        oklch(0.500 0.180 35);
            --ok:           oklch(0.520 0.130 130);

            /* Spacing. */
            --s-1: 4px;  --s-2: 8px;  --s-3: 12px; --s-4: 16px;
            --s-5: 20px; --s-6: 24px; --s-8: 32px; --s-10: 40px;
            --s-14: 56px; --s-20: 80px; --s-28: 112px;

            /* Type. */
            --serif: 'Newsreader', 'Iowan Old Style', Georgia, serif;
            --sans:  'Geist', 'Helvetica Neue', Arial, sans-serif;
            --mono:  'Geist Mono', 'JetBrains Mono', ui-monospace, monospace;

            /* Motion, strong curves, not browser defaults. */
            --ease-out:    cubic-bezier(0.23, 1, 0.32, 1);
            --ease-in-out: cubic-bezier(0.77, 0, 0.175, 1);
        }

        * { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }
        html { scroll-behavior: smooth; }

        body {
            font-family: var(--sans);
            color: var(--ink);
            background: var(--bg);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            line-height: 1.55;
            overflow-x: hidden;
        }

        ::selection { background: var(--accent); color: var(--card); }

        /* Page atmosphere. */
        .paper {
            position: fixed; inset: 0;
            background:
                radial-gradient(120% 80% at 12% -10%, oklch(0.620 0.160 45 / 0.10), transparent 55%),
                radial-gradient(80% 60% at 100% 100%, oklch(0.500 0.160 45 / 0.06), transparent 60%),
                linear-gradient(180deg, var(--bg) 0%, var(--bg-deep) 100%);
            z-index: -2;
            pointer-events: none;
        }
        .grain {
            position: fixed; inset: 0;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='180' height='180'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2' stitchTiles='stitch'/><feColorMatrix values='0 0 0 0 0.17 0 0 0 0 0.12 0 0 0 0 0.07 0 0 0 0.4 0'/></filter><rect width='100%25' height='100%25' filter='url(%23n)' opacity='0.55'/></svg>");
            opacity: 0.35;
            mix-blend-mode: multiply;
            z-index: -1;
            pointer-events: none;
        }

        /* Focus ring. */
        :focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 3px;
            border-radius: 2px;
        }

        /* Container. */
        .wrap { max-width: 1180px; margin: 0 auto; padding: 0 clamp(20px, 4vw, 56px); }
        .wrap-narrow { max-width: 760px; margin: 0 auto; padding: 0 clamp(20px, 4vw, 40px); }

        /* Navigation. */
        .nav {
            padding-top: var(--s-6);
            padding-bottom: var(--s-6);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: var(--s-6);
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: var(--s-3);
            text-decoration: none;
            color: var(--ink);
            padding: var(--s-1) var(--s-2);
            margin: calc(var(--s-1) * -1) calc(var(--s-2) * -1);
            border-radius: 4px;
        }
        .brand svg { display: block; }
        .wordmark {
            font-family: var(--serif);
            font-style: italic;
            font-weight: 500;
            font-size: 22px;
            line-height: 1;
            letter-spacing: -0.01em;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: var(--s-6);
            font-family: var(--sans);
            font-size: 14px;
        }
        .nav-links a {
            color: var(--ink-soft);
            text-decoration: none;
            transition: color 180ms var(--ease-out);
            padding: var(--s-2) 0;
        }
        @media (hover: hover) and (pointer: fine) {
            .nav-links a:hover { color: var(--accent); }
        }
        .nav-links a.here { color: var(--ink); }
        .nav-cta {
            background: var(--ink);
            color: var(--card);
            padding: var(--s-2) var(--s-4);
            border-radius: 2px;
            font-weight: 500;
            min-height: 38px;
            display: inline-flex;
            align-items: center;
            transition: background 200ms var(--ease-out), transform 100ms var(--ease-out);
        }
        @media (hover: hover) and (pointer: fine) {
            .nav-links .nav-cta:hover { background: var(--accent-deep); color: var(--card); }
        }
        .nav-cta:active { transform: scale(0.97); }

        @media (max-width: 720px) {
            .nav-links a:not(.nav-cta) { display: none; }
        }

        /* Buttons. */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: var(--s-3);
            padding: var(--s-4) var(--s-5);
            border-radius: 2px;
            font-family: var(--sans);
            font-weight: 500;
            font-size: 14px;
            letter-spacing: 0.02em;
            text-decoration: none;
            border: 0;
            cursor: pointer;
            min-height: 48px;
            transition: background 200ms var(--ease-out), color 200ms var(--ease-out), transform 100ms var(--ease-out);
        }
        .btn:active { transform: scale(0.97); }
        .btn .arrow {
            font-family: var(--mono);
            font-size: 13px;
            transition: transform 220ms var(--ease-out);
        }
        @media (hover: hover) and (pointer: fine) {
            .btn:hover .arrow { transform: translateX(3px); }
        }

        .btn-primary {
            background: var(--ink);
            color: var(--card);
        }
        @media (hover: hover) and (pointer: fine) {
            .btn-primary:hover { background: var(--accent-deep); }
        }
        .btn-accent { background: var(--accent); color: oklch(0.99 0.005 80); }
        @media (hover: hover) and (pointer: fine) {
            .btn-accent:hover { background: var(--accent-deep); }
        }
        .btn-ghost {
            background: transparent;
            color: var(--ink);
            border: 1px solid var(--rule-strong);
        }
        @media (hover: hover) and (pointer: fine) {
            .btn-ghost:hover { border-color: var(--ink); color: var(--accent-deep); }
        }

        /* Section eyebrows. */
        .eyebrow {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: var(--accent-deep);
            display: inline-flex;
            align-items: center;
            gap: var(--s-3);
            margin: 0 0 var(--s-5);
        }
        .eyebrow .line {
            display: inline-block;
            width: 32px; height: 1px;
            background: var(--accent);
            opacity: 0.6;
        }

        /* Display & body type. */
        .display {
            font-family: var(--serif);
            font-style: italic;
            font-weight: 500;
            font-size: clamp(48px, 7vw, 96px);
            line-height: 0.94;
            letter-spacing: -0.03em;
            color: var(--ink);
            margin: 0;
        }
        .display .accent { color: var(--accent); font-weight: 600; }
        .display .roman { font-style: normal; }

        .h2 {
            font-family: var(--serif);
            font-style: italic;
            font-weight: 500;
            font-size: clamp(34px, 4.5vw, 56px);
            line-height: 1.05;
            letter-spacing: -0.025em;
            margin: 0 0 var(--s-4);
        }
        .h2 .accent { color: var(--accent); }

        .h3 {
            font-family: var(--serif);
            font-style: italic;
            font-weight: 500;
            font-size: clamp(22px, 2vw, 28px);
            line-height: 1.2;
            letter-spacing: -0.02em;
            margin: 0 0 var(--s-3);
        }

        .lede {
            font-family: var(--serif);
            font-size: clamp(18px, 1.5vw, 21px);
            line-height: 1.55;
            color: var(--ink-soft);
            max-width: 56ch;
            margin: 0 0 var(--s-8);
        }
        .lede em { color: var(--accent-deep); font-style: italic; }

        p { max-width: 70ch; }

        /* Reveal-on-scroll. */
        .reveal {
            opacity: 0;
            transform: translateY(14px);
            transition: opacity 700ms var(--ease-out), transform 700ms var(--ease-out);
            will-change: opacity, transform;
        }
        .reveal.in {
            opacity: 1;
            transform: translateY(0);
        }

        /* Hero rise on load, used by home page. */
        .rise {
            opacity: 0;
            transform: translateY(14px);
            animation: rise 900ms var(--ease-out) forwards;
        }
        .rise.d-1 { animation-delay:  60ms; }
        .rise.d-2 { animation-delay: 140ms; }
        .rise.d-3 { animation-delay: 220ms; }
        .rise.d-4 { animation-delay: 300ms; }
        .rise.d-5 { animation-delay: 380ms; }
        @keyframes rise {
            to { opacity: 1; transform: translateY(0); }
        }

        /* Footer. */
        footer.site {
            padding: var(--s-14) 0 var(--s-10);
            border-top: 1px solid var(--rule);
            margin-top: var(--s-28);
        }
        footer.site .cols {
            display: grid;
            grid-template-columns: 1.6fr 1fr 1fr 1fr;
            gap: var(--s-10);
            margin-bottom: var(--s-10);
        }
        footer.site .col h4 {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--ink-faint);
            margin: 0 0 var(--s-4);
            font-weight: 500;
        }
        footer.site .col ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: var(--s-2);
        }
        footer.site .col a {
            color: var(--ink-soft);
            text-decoration: none;
            font-size: 14px;
            transition: color 180ms var(--ease-out);
        }
        @media (hover: hover) and (pointer: fine) {
            footer.site .col a:hover { color: var(--accent); }
        }
        footer.site .colophon {
            font-family: var(--serif);
            font-style: italic;
            font-size: 15px;
            color: var(--ink-soft);
            max-width: 28ch;
            margin-top: var(--s-4);
        }
        footer.site .bottom {
            border-top: 1px dashed var(--rule);
            padding-top: var(--s-5);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: var(--s-6);
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--ink-faint);
        }

        @media (max-width: 880px) {
            footer.site .cols { grid-template-columns: 1fr 1fr; gap: var(--s-8); }
            footer.site .bottom { flex-direction: column; align-items: flex-start; gap: var(--s-3); }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 1ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 1ms !important;
                scroll-behavior: auto !important;
            }
            .rise, .reveal { opacity: 1 !important; transform: none !important; }
        }

        @yield('head-style')
    </style>

    @yield('head-extra')
</head>
<body>
    <div class="paper" aria-hidden="true"></div>
    <div class="grain" aria-hidden="true"></div>

    <header class="wrap">
        <nav class="nav" aria-label="Primary">
            <a class="brand" href="/" aria-label="Kharcha home">
                <svg width="30" height="30" viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                    <rect x="2" y="2" width="92" height="92" rx="16" fill="#2b1f12"/>
                    <line x1="14" y1="74" x2="82" y2="74" stroke="#c9621f" stroke-width="1" opacity="0.35"/>
                    <path d="M14 64 L 28 44 L 40 56 L 54 30 L 68 50 L 82 38" fill="none" stroke="#fdf8ee" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="28" cy="44" r="2.8" fill="#c9621f"/>
                    <circle cx="54" cy="30" r="2.8" fill="#c9621f"/>
                    <circle cx="82" cy="38" r="2.8" fill="#c9621f"/>
                </svg>
                <span class="wordmark">kharcha</span>
            </a>
            <div class="nav-links">
                <a href="/pricing" @if(($active ?? '') === 'pricing') class="here" aria-current="page" @endif>Pricing</a>
                <a href="/#features">Features</a>
                <a href="/#faq">FAQ</a>
                <a href="/admin/login">Sign in</a>
                <a href="/admin/login" class="nav-cta">Start free</a>
            </div>
        </nav>
    </header>

    <main id="main">
        @yield('content')
    </main>

    <footer class="site">
        <div class="wrap">
            <div class="cols">
                <div class="col">
                    <a class="brand" href="/" aria-label="Kharcha home">
                        <svg width="28" height="28" viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                            <rect x="2" y="2" width="92" height="92" rx="16" fill="#2b1f12"/>
                            <path d="M14 64 L 28 44 L 40 56 L 54 30 L 68 50 L 82 38" fill="none" stroke="#fdf8ee" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="28" cy="44" r="2.8" fill="#c9621f"/>
                            <circle cx="54" cy="30" r="2.8" fill="#c9621f"/>
                            <circle cx="82" cy="38" r="2.8" fill="#c9621f"/>
                        </svg>
                        <span class="wordmark">kharcha</span>
                    </a>
                    <p class="colophon">A household ledger, kept by hand. Made in Pakistan for Pakistani families.</p>
                </div>
                <div class="col">
                    <h4>Product</h4>
                    <ul>
                        <li><a href="/#features">Features</a></li>
                        <li><a href="/pricing">Pricing</a></li>
                        <li><a href="/#faq">FAQ</a></li>
                    </ul>
                </div>
                <div class="col">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="/about">About</a></li>
                        <li><a href="/contact">Contact</a></li>
                        <li><a href="/admin/login">Sign in</a></li>
                    </ul>
                </div>
                <div class="col">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="/privacy">Privacy</a></li>
                        <li><a href="/terms">Terms</a></li>
                    </ul>
                </div>
            </div>
            <div class="bottom">
                <span>© {{ date('Y') }} Kharcha · iukhan.tech</span>
                <span>Made in Pakistan</span>
            </div>
        </div>
    </footer>

    <script>
        // Reveal-on-scroll. One-shot IntersectionObserver, opacity + tiny lift.
        (function () {
            if (!('IntersectionObserver' in window)) {
                document.querySelectorAll('.reveal').forEach((el) => el.classList.add('in'));
                return;
            }
            const io = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('in');
                        io.unobserve(entry.target);
                    }
                });
            }, { rootMargin: '0px 0px -10% 0px', threshold: 0.05 });
            document.querySelectorAll('.reveal').forEach((el) => io.observe(el));
        })();
    </script>

    @yield('scripts')
</body>
</html>
