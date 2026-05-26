<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="light">
    <meta name="robots" content="noindex, nofollow">
    <title>Reset password · Kharcha</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;1,6..72,400;1,6..72,500;1,6..72,600&family=Geist:wght@300;400;500;600&family=Geist+Mono:wght@400;500&display=swap">

    <style>
        :root {
            /* Color tokens, OKLCH, all tinted toward the warm 75–80° brand hue. */
            --bg:           oklch(0.975 0.018 80);
            --bg-deep:      oklch(0.930 0.040 80);
            --card:         oklch(0.990 0.014 85);
            --ink:          oklch(0.220 0.024 60);
            --ink-soft:     oklch(0.430 0.030 70);
            --ink-faint:    oklch(0.560 0.038 72);
            --rule:         oklch(0.880 0.038 80);
            --rule-strong:  oklch(0.810 0.058 80);
            --accent:       oklch(0.620 0.160 45);
            --accent-deep:  oklch(0.500 0.160 45);
            --alarm:        oklch(0.500 0.180 35);
            --ok:           oklch(0.520 0.130 130);
            --focus:        oklch(0.620 0.160 45 / 0.35);

            /* Spacing scale (8 base). */
            --s-1: 4px;  --s-2: 8px;  --s-3: 12px; --s-4: 16px;
            --s-5: 20px; --s-6: 24px; --s-8: 32px; --s-10: 40px;
            --s-14: 56px; --s-20: 80px;

            /* Type. */
            --serif: 'Newsreader', 'Iowan Old Style', Georgia, serif;
            --sans:  'Geist', 'Helvetica Neue', Arial, sans-serif;
            --mono:  'Geist Mono', 'JetBrains Mono', ui-monospace, monospace;

            /* Motion. */
            --ease-out: cubic-bezier(0.22, 1, 0.36, 1);          /* ease-out-quart */
            --ease-out-soft: cubic-bezier(0.16, 1, 0.3, 1);      /* ease-out-expo  */
        }

        * { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }

        body {
            font-family: var(--sans);
            color: var(--ink);
            background: var(--bg);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            line-height: 1.5;
            overflow-x: hidden;
        }

        ::selection { background: var(--accent); color: var(--card); }

        /* Background: paper grain + warm wash, layered. */
        .paper {
            position: fixed; inset: 0;
            background:
                radial-gradient(120% 80% at 12% -10%, oklch(0.620 0.160 45 / 0.10), transparent 55%),
                radial-gradient(80% 60% at 100% 100%, oklch(0.500 0.160 45 / 0.07), transparent 60%),
                linear-gradient(180deg, var(--bg) 0%, var(--bg-deep) 100%);
            z-index: -2;
            pointer-events: none;
        }
        .grain {
            position: fixed; inset: 0;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='180' height='180'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2' stitchTiles='stitch'/><feColorMatrix values='0 0 0 0 0.17 0 0 0 0 0.12 0 0 0 0 0.07 0 0 0 0.4 0'/></filter><rect width='100%25' height='100%25' filter='url(%23n)' opacity='0.55'/></svg>");
            opacity: 0.45;
            mix-blend-mode: multiply;
            z-index: -1;
            pointer-events: none;
        }

        /* Page frame. */
        .page {
            max-width: 1240px;
            margin: 0 auto;
            padding: var(--s-8) clamp(20px, 4vw, 56px) var(--s-10);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Shared focus ring. */
        :focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 3px;
            border-radius: 2px;
        }

        /* Masthead. */
        .masthead {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: var(--s-6);
            padding-bottom: var(--s-4);
            border-bottom: 1px solid var(--rule);
            opacity: 0;
            animation: rise 0.7s 0.05s var(--ease-out) forwards;
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
        .crumbs {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--ink-soft);
            display: flex;
            align-items: center;
            gap: var(--s-3);
        }
        .crumbs .dot { width: 3px; height: 3px; border-radius: 50%; background: var(--rule-strong); }
        .crumbs .here { color: var(--accent-deep); }

        /* Composition. Form gets slightly more weight: it is the task. */
        main {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1px 1.05fr;
            gap: clamp(28px, 5vw, 72px);
            padding: clamp(36px, 7vh, 80px) 0 clamp(28px, 5vh, 56px);
            align-items: start;
        }

        .editorial { padding-right: clamp(0px, 2vw, 16px); }

        .eyebrow {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: var(--accent-deep);
            margin: 0 0 var(--s-6);
            display: flex;
            align-items: center;
            gap: var(--s-3);
            opacity: 0;
            animation: rise 0.7s 0.18s var(--ease-out) forwards;
        }
        .eyebrow .line {
            display: inline-block;
            width: 32px; height: 1px;
            background: var(--accent);
            opacity: 0.6;
        }

        .display {
            font-family: var(--serif);
            font-style: italic;
            font-weight: 500;
            font-size: clamp(64px, 10vw, 140px);
            line-height: 0.88;
            letter-spacing: -0.035em;
            color: var(--ink);
            margin: 0 0 var(--s-4);
            opacity: 0;
            animation: rise 0.9s 0.22s var(--ease-out-soft) forwards;
        }
        .display .accent { color: var(--accent); font-weight: 600; }

        .subline {
            font-family: var(--serif);
            font-size: clamp(18px, 1.5vw, 21px);
            line-height: 1.5;
            color: var(--ink-soft);
            max-width: 28ch;
            margin: 0 0 var(--s-10);
            opacity: 0;
            animation: rise 0.7s 0.32s var(--ease-out) forwards;
        }
        .subline em { color: var(--accent-deep); font-style: italic; }

        .steps {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: var(--s-4);
            max-width: 36ch;
            opacity: 0;
            animation: rise 0.7s 0.42s var(--ease-out) forwards;
        }
        .steps li {
            display: grid;
            grid-template-columns: 36px 1fr;
            gap: var(--s-3);
            align-items: baseline;
            padding-bottom: var(--s-3);
            border-bottom: 1px dashed var(--rule);
        }
        .steps li:last-child { border-bottom: none; padding-bottom: 0; }
        .steps .n {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.14em;
            color: var(--accent);
        }
        .steps .t {
            font-family: var(--sans);
            font-size: 15px;
            color: var(--ink);
            line-height: 1.5;
        }
        .steps .t small {
            display: block;
            margin-top: var(--s-1);
            font-size: 13px;
            color: var(--ink-soft);
            line-height: 1.55;
        }

        /* Ornamental rule between columns. Quiet, no dot ornaments. */
        .rule {
            width: 1px;
            align-self: stretch;
            background: linear-gradient(180deg,
                transparent 0%,
                var(--rule-strong) 14%,
                var(--rule-strong) 86%,
                transparent 100%);
        }

        /* Form card. */
        .card {
            background: var(--card);
            border: 1px solid var(--rule);
            border-radius: 3px;
            padding: clamp(28px, 3vw, 40px) clamp(26px, 3vw, 36px);
            box-shadow:
                0 1px 0 oklch(0.220 0.024 60 / 0.04),
                0 18px 40px -28px oklch(0.220 0.024 60 / 0.22),
                0 60px 90px -60px oklch(0.500 0.160 45 / 0.18);
            position: relative;
        }

        .form-title {
            font-family: var(--serif);
            font-style: italic;
            font-weight: 500;
            font-size: 22px;
            line-height: 1.15;
            margin: 0 0 var(--s-1);
            color: var(--ink);
        }
        .form-sub {
            font-family: var(--sans);
            font-size: 13.5px;
            color: var(--ink-soft);
            margin: 0 0 var(--s-8);
        }

        .field {
            margin-bottom: var(--s-6);
            position: relative;
        }
        .field label {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--ink-soft);
            margin-bottom: var(--s-2);
        }
        .field label .hint {
            color: var(--ink-faint);
            text-transform: none;
            letter-spacing: 0.04em;
            font-size: 11.5px;
        }
        .field label .hint[data-state="ok"] { color: var(--ok); }
        .field label .hint[data-state="mismatch"] { color: var(--alarm); }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--rule-strong);
            transition: border-color 180ms var(--ease-out);
        }
        .input-wrap:focus-within { border-color: var(--accent); }

        .input-wrap input {
            flex: 1;
            border: 0;
            background: transparent;
            font-family: var(--sans);
            font-size: 17px;
            padding: var(--s-1) 0 var(--s-2);
            color: var(--ink);
            outline: none;
            min-width: 0;
        }
        .input-wrap input::placeholder { color: var(--ink-faint); }
        .input-wrap input:read-only { color: var(--ink-soft); }

        .lock {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--ink-soft);
            padding-left: var(--s-3);
            display: inline-flex;
            align-items: center;
            gap: var(--s-1);
        }
        .lock svg { display: block; }

        .toggle-eye {
            border: 0;
            background: transparent;
            color: var(--ink-soft);
            cursor: pointer;
            padding: var(--s-2) var(--s-2);
            min-height: 44px;
            min-width: 64px;
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            border-radius: 2px;
            transition: color 180ms var(--ease-out);
        }
        .toggle-eye:hover { color: var(--accent); }

        /* Strength meter. */
        .meter {
            margin-top: var(--s-3);
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: var(--s-1);
        }
        .meter span {
            height: 2px;
            background: var(--rule);
            border-radius: 1px;
            transition: background 280ms var(--ease-out);
        }
        .meter[data-strength="1"] span:nth-child(1) { background: var(--alarm); }
        .meter[data-strength="2"] span:nth-child(-n+2) { background: var(--accent); }
        .meter[data-strength="3"] span { background: var(--ok); }

        .meter-label {
            margin-top: var(--s-2);
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--ink-soft);
            display: flex;
            justify-content: space-between;
        }
        .meter-label .verdict[data-strength="1"] { color: var(--alarm); }
        .meter-label .verdict[data-strength="2"] { color: var(--accent-deep); }
        .meter-label .verdict[data-strength="3"] { color: var(--ok); }

        .sr-only {
            position: absolute; width: 1px; height: 1px;
            padding: 0; margin: -1px; overflow: hidden;
            clip: rect(0,0,0,0); white-space: nowrap; border: 0;
        }

        /* Submit. */
        .submit {
            margin-top: var(--s-5);
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: var(--s-3);
            padding: var(--s-4) var(--s-5);
            background: var(--ink);
            color: var(--card);
            border: 0;
            border-radius: 2px;
            font-family: var(--sans);
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.03em;
            cursor: pointer;
            transition: background 180ms var(--ease-out), transform 120ms var(--ease-out);
            min-height: 48px;
        }
        .submit:hover { background: var(--accent-deep); }
        .submit:active { transform: translateY(1px); }
        .submit[disabled] { opacity: 0.7; cursor: progress; }
        .submit .arrow {
            font-family: var(--mono);
            font-size: 13px;
            transition: transform 200ms var(--ease-out);
        }
        .submit:hover .arrow { transform: translateX(3px); }

        /* Inline error. */
        .err {
            margin-top: var(--s-4);
            font-family: var(--sans);
            font-size: 13.5px;
            color: var(--alarm);
            display: flex;
            align-items: flex-start;
            gap: var(--s-2);
        }
        .err::before {
            content: '';
            display: inline-block;
            width: 0; height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-bottom: 8px solid var(--alarm);
            margin-top: 5px;
            flex: 0 0 auto;
        }

        .legal {
            margin-top: var(--s-5);
            padding-top: var(--s-4);
            border-top: 1px dashed var(--rule);
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--ink-soft);
            display: flex;
            justify-content: space-between;
            gap: var(--s-3);
        }

        /* Success state. */
        .checkmark {
            width: 48px; height: 48px;
            display: block;
            margin: var(--s-1) 0 var(--s-5);
        }
        .checkmark circle, .checkmark path {
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        .checkmark circle {
            stroke: var(--accent);
            stroke-width: 2;
            stroke-dasharray: 152;
            stroke-dashoffset: 152;
            animation: draw 0.7s 0.1s var(--ease-out) forwards;
        }
        .checkmark path {
            stroke: var(--ink);
            stroke-width: 2.4;
            stroke-dasharray: 36;
            stroke-dashoffset: 36;
            animation: draw 0.45s 0.65s var(--ease-out) forwards;
        }
        @keyframes draw { to { stroke-dashoffset: 0; } }

        .return {
            font-family: var(--serif);
            font-style: italic;
            font-size: 18px;
            line-height: 1.55;
            color: var(--ink);
            margin: 0 0 var(--s-6);
        }
        .return em { color: var(--accent-deep); }

        .cta-row {
            display: flex;
            flex-direction: column;
            gap: var(--s-2);
        }
        .cta-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: var(--s-3);
            padding: var(--s-4) var(--s-5);
            background: var(--accent);
            color: oklch(0.99 0.005 80);
            text-decoration: none;
            border-radius: 2px;
            font-family: var(--sans);
            font-weight: 500;
            font-size: 14px;
            letter-spacing: 0.03em;
            min-height: 48px;
            transition: background 180ms var(--ease-out);
        }
        .cta-primary:hover { background: var(--accent-deep); }
        .cta-secondary {
            display: inline-flex;
            justify-content: center;
            padding: var(--s-3);
            color: var(--ink-soft);
            text-decoration: none;
            font-family: var(--mono);
            font-size: 11.5px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            border-radius: 2px;
            transition: color 180ms var(--ease-out);
        }
        .cta-secondary:hover { color: var(--accent); }

        /* Footer. */
        footer {
            padding-top: var(--s-5);
            border-top: 1px solid var(--rule);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: var(--s-6);
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--ink-soft);
            opacity: 0;
            animation: rise 0.7s 0.55s var(--ease-out) forwards;
        }
        footer .colophon {
            font-family: var(--serif);
            font-style: italic;
            font-size: 13.5px;
            text-transform: none;
            letter-spacing: 0;
            color: var(--ink-soft);
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
            .editorial > *, .masthead, footer { opacity: 1 !important; transform: none !important; }
            .checkmark circle, .checkmark path { stroke-dashoffset: 0 !important; }
        }

        @media (max-width: 880px) {
            main {
                grid-template-columns: 1fr;
                gap: var(--s-8);
                padding: var(--s-8) 0 var(--s-6);
            }
            .rule { display: none; }
            .display { font-size: clamp(56px, 16vw, 96px); }
            .subline { margin-bottom: var(--s-6); }
            .steps { max-width: none; }
            .editorial { padding-right: 0; }
            footer { flex-direction: column; align-items: flex-start; gap: var(--s-3); }
            .crumbs { display: none; }
        }
    </style>
</head>
<body>
    <div class="paper" aria-hidden="true"></div>
    <div class="grain" aria-hidden="true"></div>

    <div class="page">

        <header class="masthead">
            <a class="brand" href="/" aria-label="Kharcha home">
                <svg width="32" height="32" viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                    <rect x="2" y="2" width="92" height="92" rx="16" fill="#2b1f12"/>
                    <line x1="14" y1="74" x2="82" y2="74" stroke="#c9621f" stroke-width="1" opacity="0.35"/>
                    <path d="M14 64 L 28 44 L 40 56 L 54 30 L 68 50 L 82 38" fill="none" stroke="#fdf8ee" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="28" cy="44" r="2.8" fill="#c9621f"/>
                    <circle cx="54" cy="30" r="2.8" fill="#c9621f"/>
                    <circle cx="82" cy="38" r="2.8" fill="#c9621f"/>
                </svg>
                <span class="wordmark">kharcha</span>
            </a>
            <nav class="crumbs" aria-label="Section">
                <span>Account</span>
                <span class="dot" aria-hidden="true"></span>
                <span>Security</span>
                <span class="dot" aria-hidden="true"></span>
                <span class="here" aria-current="page">Reset</span>
            </nav>
        </header>

        <main>
            <section class="editorial">
                @if ($state === 'success')
                    <p class="eyebrow"><span class="line" aria-hidden="true"></span> Saved</p>
                    <h1 class="display">Done<span class="accent">.</span></h1>
                    <p class="subline">Your password is set. Every other device has been signed out.</p>

                @elseif ($state === 'expired')
                    <p class="eyebrow"><span class="line" aria-hidden="true"></span> Link expired</p>
                    <h1 class="display">Lost<span class="accent">,</span></h1>
                    <p class="subline">This reset link has been used, or its 60 minute window has passed. Open Kharcha and request a fresh one.</p>

                @else
                    <p class="eyebrow"><span class="line" aria-hidden="true"></span> Kharcha · Password reset</p>
                    <h1 class="display">A new<br>password<span class="accent">,</span><br>by hand<span class="accent">.</span></h1>
                    <p class="subline">Set it once below. Your <em>household ledger</em> is yours again, and every other device quietly signs out.</p>

                    <ol class="steps" aria-label="What happens next">
                        <li>
                            <span class="n" aria-hidden="true">01</span>
                            <span class="t">Choose a new password
                                <small>Eight characters or more. Long passphrases beat clever ones.</small>
                            </span>
                        </li>
                        <li>
                            <span class="n" aria-hidden="true">02</span>
                            <span class="t">Existing sessions sign out
                                <small>Every other device is logged out the moment you submit.</small>
                            </span>
                        </li>
                        <li>
                            <span class="n" aria-hidden="true">03</span>
                            <span class="t">Return to your household
                                <small>Open Kharcha on your phone and sign in with the new password.</small>
                            </span>
                        </li>
                    </ol>
                @endif
            </section>

            <div class="rule" aria-hidden="true"></div>

            <aside>
                @if ($state === 'success')
                    <div class="card" role="status" aria-live="polite">
                        <svg class="checkmark" viewBox="0 0 56 56" aria-hidden="true" focusable="false">
                            <circle cx="28" cy="28" r="24"/>
                            <path d="M16 29 L25 38 L41 19"/>
                        </svg>

                        <h2 class="form-title">Welcome back to your household.</h2>
                        <p class="return">Your new password is set. <em>Now fetch your phone</em>: Kharcha is waiting on the doorstep.</p>

                        <div class="cta-row">
                            <a class="cta-primary" href="kharcha://login">
                                Open Kharcha
                                <span class="arrow" aria-hidden="true">→</span>
                            </a>
                            <a class="cta-secondary" href="/admin/login">Or sign in on the web</a>
                        </div>

                        <p class="legal">
                            <span>Signed · {{ now()->format('d M Y · H:i') }}</span>
                            <span>Kharcha</span>
                        </p>
                    </div>

                @elseif ($state === 'expired')
                    <div class="card" role="status" aria-live="polite">
                        <h2 class="form-title">This link is no longer valid.</h2>
                        <p class="form-sub">Reset links expire after 60 minutes, and each one only works once. Open Kharcha, tap <em>Forgot password</em>, and a fresh letter goes out.</p>

                        <div class="cta-row">
                            <a class="cta-primary" href="kharcha://login">
                                Open Kharcha to retry
                                <span class="arrow" aria-hidden="true">→</span>
                            </a>
                            <a class="cta-secondary" href="/admin/login">Or sign in if you remember it</a>
                        </div>
                    </div>

                @else
                    <form class="card" method="POST" action="{{ route('password.update') }}" novalidate>
                        @csrf

                        <h2 class="form-title">A new password, please.</h2>
                        <p class="form-sub">Long and easy to type beats short and clever.</p>

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="field">
                            <label for="email">
                                <span>Account</span>
                                <span class="hint">Locked to this link</span>
                            </label>
                            <div class="input-wrap">
                                <input id="email" name="email" type="email" value="{{ $email }}" readonly tabindex="-1" autocomplete="username">
                                <span class="lock" aria-hidden="true">
                                    <svg width="11" height="13" viewBox="0 0 11 13" fill="none">
                                        <rect x="1" y="6" width="9" height="6" rx="1" stroke="currentColor"/>
                                        <path d="M3 6V4a2.5 2.5 0 015 0v2" stroke="currentColor"/>
                                    </svg>
                                    Locked
                                </span>
                            </div>
                        </div>

                        <div class="field">
                            <label for="password">
                                <span>New password</span>
                                <span class="hint">Min 8 characters</span>
                            </label>
                            <div class="input-wrap">
                                <input id="password" name="password" type="password" autocomplete="new-password" required minlength="8" placeholder="••••••••••••" autofocus aria-describedby="meter-desc">
                                <button type="button" class="toggle-eye" data-toggle="password" aria-label="Show password" aria-pressed="false">Show</button>
                            </div>
                            <div class="meter" id="meter" data-strength="0" aria-hidden="true">
                                <span></span><span></span><span></span>
                            </div>
                            <div class="meter-label">
                                <span>Strength</span>
                                <span class="verdict" id="verdict" data-strength="0" aria-hidden="true">·</span>
                            </div>
                            <span id="meter-desc" class="sr-only" aria-live="polite"></span>
                        </div>

                        <div class="field">
                            <label for="password_confirmation">
                                <span>Confirm</span>
                                <span class="hint" id="match-hint">Type it once more</span>
                            </label>
                            <div class="input-wrap">
                                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required minlength="8" placeholder="••••••••••••">
                                <button type="button" class="toggle-eye" data-toggle="password_confirmation" aria-label="Show password" aria-pressed="false">Show</button>
                            </div>
                        </div>

                        <button type="submit" class="submit" id="submit">
                            <span class="label">Set new password</span>
                            <span class="arrow" aria-hidden="true">→</span>
                        </button>

                        @if ($errors->any())
                            <div class="err" role="alert">
                                <span>{{ $errors->first() }}</span>
                            </div>
                        @endif

                        <p class="legal">
                            <span>Encrypted</span>
                            <span>Expires in 60 min</span>
                        </p>
                    </form>
                @endif
            </aside>
        </main>

        <footer>
            <span class="colophon"><em>Kharcha</em>, a household ledger kept by hand.</span>
            <span>© {{ date('Y') }} · iukhan.tech</span>
        </footer>
    </div>

    <script>
        (function () {
            // Show/hide password.
            document.querySelectorAll('.toggle-eye').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-toggle');
                    const input = document.getElementById(id);
                    if (!input) return;
                    const wasPassword = input.type === 'password';
                    input.type = wasPassword ? 'text' : 'password';
                    btn.textContent = wasPassword ? 'Hide' : 'Show';
                    btn.setAttribute('aria-pressed', String(wasPassword));
                    btn.setAttribute('aria-label', wasPassword ? 'Hide password' : 'Show password');
                });
            });

            const pwd = document.getElementById('password');
            const conf = document.getElementById('password_confirmation');
            const meter = document.getElementById('meter');
            const verdict = document.getElementById('verdict');
            const meterDesc = document.getElementById('meter-desc');
            const matchHint = document.getElementById('match-hint');

            function score(value) {
                if (!value) return 0;
                let s = 0;
                if (value.length >= 8) s++;
                if (value.length >= 14) s++;
                if (/[a-z]/.test(value) && /[A-Z]/.test(value)) s++;
                if (/\d/.test(value) && /[^a-zA-Z0-9]/.test(value)) s++;
                return Math.min(3, Math.max(1, s));
            }
            const verdictText = { 0: '·', 1: 'Weak', 2: 'Ok', 3: 'Strong' };
            const verdictAnnounce = { 0: '', 1: 'Weak password.', 2: 'Acceptable password.', 3: 'Strong password.' };

            function updateStrength() {
                const s = score(pwd.value);
                meter.dataset.strength = String(s);
                verdict.dataset.strength = String(s);
                verdict.textContent = verdictText[s];
                if (meterDesc) meterDesc.textContent = verdictAnnounce[s];
            }
            function updateMatch() {
                if (!conf.value) {
                    matchHint.textContent = 'Type it once more';
                    matchHint.removeAttribute('data-state');
                    return;
                }
                const ok = conf.value === pwd.value;
                matchHint.textContent = ok ? 'Matches' : 'Does not match';
                matchHint.setAttribute('data-state', ok ? 'ok' : 'mismatch');
            }

            if (pwd) pwd.addEventListener('input', () => { updateStrength(); updateMatch(); });
            if (conf) conf.addEventListener('input', updateMatch);

            const form = document.querySelector('form.card');
            const submit = document.getElementById('submit');
            if (form && submit) {
                form.addEventListener('submit', () => {
                    submit.disabled = true;
                    const label = submit.querySelector('.label');
                    if (label) label.textContent = 'Setting…';
                });
            }
        })();
    </script>
</body>
</html>
