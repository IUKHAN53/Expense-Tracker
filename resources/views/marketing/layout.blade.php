<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="light">

    {{-- SEO --}}
    <title>{{ $title ?? 'Kharcha · A household ledger, kept by hand' }}</title>
    <meta name="description" content="{{ $description ?? 'Kharcha is the calm household expense tracker for families anywhere. Snap a receipt, split with everyone, log fuel, see where the month went. Multi-currency.' }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="robots" content="index, follow">

    {{-- OpenGraph --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Kharcha">
    <meta property="og:title" content="{{ $title ?? 'Kharcha · A household ledger, kept by hand' }}">
    <meta property="og:description" content="{{ $description ?? 'Snap a receipt, split with everyone, log fuel, see where the month went. A calm household expense tracker for families anywhere.' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ url('/og.png') }}">
    <meta property="og:image:width" content="1024">
    <meta property="og:image:height" content="500">
    <meta property="og:locale" content="en_PK">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? 'Kharcha · A household ledger, kept by hand' }}">
    <meta name="twitter:description" content="{{ $description ?? 'Snap a receipt, split with everyone, log fuel, see where the month went.' }}">
    <meta name="twitter:image" content="{{ url('/og.png') }}">

    {{-- Theme colour for mobile chrome --}}
    <meta name="theme-color" content="#FFFFFF">

    {{-- Favicon: the Kharcha coin --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml;utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 160 160'%3E%3Ccircle cx='80' cy='80' r='80' fill='%23F5C518'/%3E%3Crect x='39' y='37' width='20.4' height='86.4' rx='3' fill='%23111'/%3E%3Cpath d='M58.2 93.4L88.2 65.8H115.8L58.2 118.6Z' fill='%23111'/%3E%3Cpath d='M72.6 104.8L89.4 89.2L120.6 123.4H94.2Z' fill='%23111'/%3E%3C/svg%3E">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700;800&family=Geist+Mono:wght@400;500&display=swap">

    {{-- JSON-LD: Organization + SoftwareApplication. Built in PHP to keep "@context" out of Blade's parser. --}}
    @php
        $ldOrigin = url('/');
        $ldOg = url('/og.png');
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
                    'operatingSystem'    => 'Android',
                    'downloadUrl'        => 'https://play.google.com/store/apps/details?id=com.iukhan.kharcha',
                    'installUrl'         => 'https://play.google.com/store/apps/details?id=com.iukhan.kharcha',
                    'description'        => 'Private household expense tracker: no account, encrypted on your phone, with receipt scanning, split expenses and fuel logging.',
                    'offers' => [
                        ['@type' => 'Offer', 'price' => '0',     'priceCurrency' => 'USD', 'name' => 'Free'],
                        ['@type' => 'Offer', 'price' => '1.09', 'priceCurrency' => 'USD', 'name' => 'Pro Monthly'],
                        ['@type' => 'Offer', 'price' => '8.99', 'priceCurrency' => 'USD', 'name' => 'Pro Yearly'],
                    ],
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    @endphp
    <script type="application/ld+json">{!! $jsonLd !!}</script>

    <style>
        :root {
            /* Kharcha 2.0 signage palette, same as the app (src/theme.js). */
            --bg:           #FFFFFF;
            --bg-deep:      #F6F6F4;
            --card:         #FFFFFF;
            --card-warm:    #F6F6F4;
            --soft:         #F6F6F4;
            --ink:          #111111;
            --ink-soft:     #6B6B6B;
            --ink-faint:    #9A9A9A;
            --rule:         #EDEDED;
            --rule-strong:  #D9D9D9;
            --primary:      #F5C518;  /* fills only; never a text colour */
            --accent:       #111111;  /* page templates use this for marks and text */
            --accent-deep:  #111111;
            --accent-soft:  #F5C518;
            --alarm:        #C8372D;
            --ok:           #1F8A4C;

            /* Spacing. */
            --s-1: 4px;  --s-2: 8px;  --s-3: 12px; --s-4: 16px;
            --s-5: 20px; --s-6: 24px; --s-8: 32px; --s-10: 40px;
            --s-14: 56px; --s-20: 80px; --s-28: 112px;

            /* Type. */
            --serif: 'Geist', 'Helvetica Neue', Arial, sans-serif;
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

        ::selection { background: var(--primary); color: var(--ink); }

        /* No paper texture in 2.0: flat white, like the app. */
        .paper, .grain { display: none; }

        /* Focus ring. */
        :focus-visible {
            outline: 2.5px solid var(--ink);
            outline-offset: 3px;
            border-radius: 6px;
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
            font-family: var(--sans);
            font-weight: 800;
            font-size: 24px;
            line-height: 1;
            letter-spacing: -0.055em;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: var(--s-6);
            font-family: var(--sans);
            font-size: 14px;
        }
        /* Scoped so the .nav-cta button does not inherit muted link color
           or zero horizontal padding from the generic rule (which would
           otherwise win on specificity, .nav-links a being 0-1-1). */
        .nav-links a:not(.nav-cta) {
            color: var(--ink-soft);
            text-decoration: none;
            transition: color 180ms var(--ease-out);
            padding: var(--s-2) 0;
        }
        @media (hover: hover) and (pointer: fine) {
            .nav-links a:not(.nav-cta):hover { color: var(--ink); text-decoration: underline; text-underline-offset: 6px; }
        }
        .nav-links a.here:not(.nav-cta) { color: var(--ink); }
        .nav-cta {
            background: var(--ink);
            color: #fff;
            text-decoration: none;
            padding: 0 var(--s-5);
            border-radius: 999px;
            font-family: var(--sans);
            font-weight: 600;
            font-size: 14px;
            line-height: 1;
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            transition: background 200ms var(--ease-out), transform 100ms var(--ease-out);
        }
        @media (hover: hover) and (pointer: fine) {
            .nav-links .nav-cta:hover { background: #2a2a2a; color: #fff; }
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
            border-radius: 14px;
            font-family: var(--sans);
            font-weight: 700;
            font-size: 16px;
            letter-spacing: -0.01em;
            text-decoration: none;
            border: 0;
            cursor: pointer;
            min-height: 56px;
            transition: background 200ms var(--ease-out), color 200ms var(--ease-out), transform 100ms var(--ease-out);
        }
        .btn:active { transform: scale(0.97); }
        .btn .arrow {
            font-family: var(--mono);
            font-size: 13px;
            transition: transform 220ms var(--ease-out);
        }
        .btn .play-mark {
            font-size: 11px;
            line-height: 1;
            transform: translateY(0.5px);
        }
        @media (hover: hover) and (pointer: fine) {
            .btn:hover .arrow { transform: translateX(3px); }
        }

        .btn-primary { background: var(--ink); color: #fff; }
        @media (hover: hover) and (pointer: fine) {
            .btn-primary:hover { background: #2a2a2a; }
        }
        .btn-accent { background: var(--primary); color: var(--ink); }
        @media (hover: hover) and (pointer: fine) {
            .btn-accent:hover { background: #E8B90F; }
        }
        .btn-ghost {
            background: transparent;
            color: var(--ink);
            border: 2px solid var(--ink);
        }
        @media (hover: hover) and (pointer: fine) {
            .btn-ghost:hover { background: var(--soft); }
        }

        /* Section eyebrows. */
        .eyebrow {
            font-family: var(--sans);
            font-weight: 600;
            font-size: 13px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--ink-soft);
            display: inline-flex;
            align-items: center;
            gap: var(--s-3);
            margin: 0 0 var(--s-5);
        }
        .eyebrow .line { display: none; }

        /* Display & body type. */
        .display {
            font-family: var(--sans);
            font-weight: 800;
            font-size: clamp(48px, 7.4vw, 104px);
            line-height: 0.95;
            letter-spacing: -0.05em;
            color: var(--ink);
            margin: 0;
        }

        .h2 {
            font-family: var(--sans);
            font-weight: 800;
            font-size: clamp(34px, 4.6vw, 60px);
            line-height: 1.02;
            letter-spacing: -0.045em;
            margin: 0 0 var(--s-4);
        }
        /* Highlighted words in headings: a chrome-yellow marker under ink text. */
        .display .accent, .h2 .accent, h1 .accent {
            color: var(--ink);
            background: linear-gradient(transparent 58%, var(--primary) 58%, var(--primary) 92%, transparent 92%);
            padding: 0 0.04em;
        }

        .h3 {
            font-family: var(--sans);
            font-weight: 700;
            font-size: clamp(20px, 1.8vw, 24px);
            line-height: 1.2;
            letter-spacing: -0.025em;
            margin: 0 0 var(--s-3);
        }

        .lede {
            font-family: var(--sans);
            font-size: clamp(17px, 1.4vw, 20px);
            line-height: 1.55;
            color: var(--ink-soft);
            max-width: 56ch;
            margin: 0 0 var(--s-8);
        }
        .lede em { color: var(--ink); font-style: normal; font-weight: 600; }

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
            footer.site .col a:hover { color: var(--ink); text-decoration: underline; }
        }
        footer.site .colophon {
            font-size: 15px;
            color: var(--ink-soft);
            max-width: 28ch;
            margin-top: var(--s-4);
        }
        footer.site .bottom {
            border-top: 1px solid var(--rule);
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

        /* 2.0 overrides for page templates (they were written for the old serif italic look). */
        body *:not(em):not(i) { font-style: normal !important; }
        .tier-card .price, .pricing-grid .amount, .stat .v, .stat-strip .v { font-weight: 800; letter-spacing: -0.04em; }
        .faq-list summary, .trust-item h4, .pillar h3, .channel h3, .topic h4, .legal article h2, .tier-card .name, .price .tier { font-weight: 700; letter-spacing: -0.02em; }
        .legal article h2 { font-weight: 800; letter-spacing: -0.035em; }
        .tier-card, .pricing-grid .price, .toc, .channel, .pillar, .topic, .reply-card, .region { border-radius: 16px; }
        .tier-card.featured, .pricing-grid .price.featured { border: 2px solid var(--ink); box-shadow: 8px 8px 0 var(--primary); }
        .tier-card .badge { background: var(--primary); color: var(--ink); font-family: var(--sans); font-weight: 700; letter-spacing: 0.08em; border-radius: 999px; }
        .region-chip { border-radius: 999px; font-family: var(--sans); font-weight: 600; }
        .region-chip.on { background: var(--ink); color: #fff; }
        .compare td.check::after { color: var(--ok); }
        .compare td.check, .compare td.dash { text-align: center; }
        .region-meta { margin-left: auto; margin-right: auto; font-family: var(--sans) !important; letter-spacing: 0.04em !important; }
        .faq-list summary .plus { color: var(--ink); }
        .legal article a, .region-meta a { color: var(--ink); }
    </style>

    @yield('head-extra')
</head>
<body>
    <div class="paper" aria-hidden="true"></div>
    <div class="grain" aria-hidden="true"></div>

    <header class="wrap">
        <nav class="nav" aria-label="Primary">
            <a class="brand" href="/" aria-label="Kharcha home">
                <svg width="34" height="34" viewBox="0 0 160 160" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><circle cx="80" cy="80" r="80" fill="#F5C518"/><rect x="39" y="37" width="20.4" height="86.4" rx="3" fill="#111"/><path d="M58.2 93.4L88.2 65.8H115.8L58.2 118.6Z" fill="#111"/><path d="M72.6 104.8L89.4 89.2L120.6 123.4H94.2Z" fill="#111"/></svg>
                <span class="wordmark">kharcha</span>
            </a>
            <div class="nav-links">
                <a href="/pricing" @if(($active ?? '') === 'pricing') class="here" aria-current="page" @endif>Pricing</a>
                <a href="/#features">Features</a>
                <a href="/#faq">FAQ</a>
                <a href="{{ $playUrl }}" class="nav-cta">Get the app</a>
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
                        <svg width="30" height="30" viewBox="0 0 160 160" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><circle cx="80" cy="80" r="80" fill="#F5C518"/><rect x="39" y="37" width="20.4" height="86.4" rx="3" fill="#111"/><path d="M58.2 93.4L88.2 65.8H115.8L58.2 118.6Z" fill="#111"/><path d="M72.6 104.8L89.4 89.2L120.6 123.4H94.2Z" fill="#111"/></svg>
                        <span class="wordmark">kharcha</span>
                    </a>
                    <p class="colophon">A household ledger, kept by hand. Private, encrypted, no account.</p>
                </div>
                <div class="col">
                    <h4>Product</h4>
                    <ul>
                        <li><a href="/#features">Features</a></li>
                        <li><a href="/pricing">Pricing</a></li>
                        <li><a href="/#faq">FAQ</a></li>
                        <li><a href="{{ $playUrl }}" target="_blank" rel="noopener">Get the Android app</a></li>
                    </ul>
                </div>
                <div class="col">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="/about">About</a></li>
                        <li><a href="/contact">Contact</a></li>
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
                <span>Built by a developer in Karachi</span>
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
