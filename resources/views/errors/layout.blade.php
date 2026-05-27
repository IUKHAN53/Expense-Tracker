<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="light">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'Something went wrong · Kharcha' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;1,6..72,400;1,6..72,500;1,6..72,600&family=Geist:wght@300;400;500;600&family=Geist+Mono:wght@400;500&display=swap">

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml;utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 96 96'%3E%3Crect x='2' y='2' width='92' height='92' rx='16' fill='%232b1f12'/%3E%3Cpath d='M14 64 L 28 44 L 40 56 L 54 30 L 68 50 L 82 38' fill='none' stroke='%23fdf8ee' stroke-width='2.6' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ccircle cx='28' cy='44' r='2.8' fill='%23c9621f'/%3E%3Ccircle cx='54' cy='30' r='2.8' fill='%23c9621f'/%3E%3Ccircle cx='82' cy='38' r='2.8' fill='%23c9621f'/%3E%3C/svg%3E">

    <meta name="theme-color" content="#fdf8ee">

    <style>
        :root {
            --bg:          oklch(0.975 0.018 80);
            --bg-deep:     oklch(0.930 0.040 80);
            --card:        oklch(0.990 0.014 85);
            --ink:         oklch(0.220 0.024 60);
            --ink-soft:    oklch(0.430 0.030 70);
            --ink-faint:   oklch(0.560 0.038 72);
            --rule:        oklch(0.880 0.038 80);
            --rule-strong: oklch(0.810 0.058 80);
            --accent:      oklch(0.620 0.160 45);
            --accent-deep: oklch(0.500 0.160 45);

            --s-1: 4px;  --s-2: 8px;  --s-3: 12px; --s-4: 16px;
            --s-5: 20px; --s-6: 24px; --s-8: 32px; --s-10: 40px;
            --s-14: 56px; --s-20: 80px;

            --serif: 'Newsreader', 'Iowan Old Style', Georgia, serif;
            --sans:  'Geist', 'Helvetica Neue', Arial, sans-serif;
            --mono:  'Geist Mono', 'JetBrains Mono', ui-monospace, monospace;

            --ease-out: cubic-bezier(0.23, 1, 0.32, 1);
        }

        * { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }

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

        :focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 3px;
            border-radius: 2px;
        }

        .page {
            min-height: 100vh;
            max-width: 720px;
            margin: 0 auto;
            padding: var(--s-8) clamp(20px, 4vw, 56px);
            display: flex;
            flex-direction: column;
        }

        .masthead {
            padding-bottom: var(--s-4);
            border-bottom: 1px solid var(--rule);
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

        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: clamp(40px, 10vh, 120px) 0;
        }

        .status {
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
        .status .line {
            display: inline-block;
            width: 32px; height: 1px;
            background: var(--accent);
            opacity: 0.6;
        }

        .display {
            font-family: var(--serif);
            font-style: italic;
            font-weight: 500;
            font-size: clamp(72px, 12vw, 168px);
            line-height: 0.88;
            letter-spacing: -0.035em;
            color: var(--ink);
            margin: 0 0 var(--s-6);
            font-variant-numeric: tabular-nums;
        }
        .display .accent {
            color: var(--accent);
            font-weight: 600;
        }

        .lede {
            font-family: var(--serif);
            font-size: clamp(20px, 1.8vw, 24px);
            line-height: 1.5;
            color: var(--ink-soft);
            max-width: 36ch;
            margin: 0 0 var(--s-10);
        }
        .lede em { color: var(--accent-deep); font-style: italic; }

        .cta-row {
            display: flex;
            gap: var(--s-3);
            flex-wrap: wrap;
        }

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
            transition: background 160ms var(--ease-out),
                        color 160ms var(--ease-out),
                        border-color 160ms var(--ease-out),
                        transform 100ms var(--ease-out);
        }
        .btn:active { transform: scale(0.97); }
        .btn .arrow {
            font-family: var(--mono);
            font-size: 13px;
            transition: transform 200ms var(--ease-out);
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

        .btn-ghost {
            background: transparent;
            color: var(--ink);
            border: 1px solid var(--rule-strong);
        }
        @media (hover: hover) and (pointer: fine) {
            .btn-ghost:hover {
                border-color: var(--ink);
                color: var(--accent-deep);
            }
        }

        .meta {
            margin-top: var(--s-8);
            padding-top: var(--s-5);
            border-top: 1px dashed var(--rule);
            font-family: var(--mono);
            font-size: 10.5px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--ink-faint);
            display: flex;
            justify-content: space-between;
            gap: var(--s-4);
            flex-wrap: wrap;
        }
        .meta a { color: var(--accent-deep); text-decoration: none; }
        @media (hover: hover) and (pointer: fine) {
            .meta a:hover { color: var(--accent); }
        }

        footer {
            padding-top: var(--s-5);
            border-top: 1px solid var(--rule);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: var(--s-4);
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--ink-faint);
            flex-wrap: wrap;
        }
        footer .colophon {
            font-family: var(--serif);
            font-style: italic;
            font-size: 13px;
            text-transform: none;
            letter-spacing: 0;
            color: var(--ink-soft);
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                transition-duration: 1ms !important;
            }
        }

        @media (max-width: 640px) {
            .display { font-size: clamp(64px, 18vw, 112px); }
            footer { flex-direction: column; align-items: flex-start; gap: var(--s-2); }
        }
    </style>
</head>
<body>
    <div class="paper" aria-hidden="true"></div>
    <div class="grain" aria-hidden="true"></div>

    <div class="page">
        <header class="masthead">
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
        </header>

        <main>
            <p class="status">
                <span class="line" aria-hidden="true"></span>
                @yield('status-label')
            </p>

            <h1 class="display">@yield('display')</h1>

            <p class="lede">@yield('lede')</p>

            <div class="cta-row">
                @yield('cta')
            </div>

            @hasSection('meta')
                <p class="meta">@yield('meta')</p>
            @endif
        </main>

        <footer>
            <span class="colophon"><em>Kharcha</em>, a household ledger kept by hand.</span>
            <span>© {{ date('Y') }} · iukhan.tech</span>
        </footer>
    </div>
</body>
</html>
