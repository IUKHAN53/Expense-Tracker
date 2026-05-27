@extends('marketing.layout', [
    'title' => 'Kharcha · A household ledger, kept by hand',
    'description' => 'The calm expense tracker for Pakistani households. Snap a receipt with the camera, split a bill across the family, log fuel by the litre, see exactly where the month went.',
    'active' => 'home',
])

@section('head-style')
        /* ============ HERO ============ */
        .hero {
            padding: clamp(40px, 8vh, 96px) 0 clamp(40px, 6vh, 80px);
        }
        .hero-grid {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: clamp(28px, 6vw, 80px);
            align-items: center;
        }
        .hero h1 {
            font-family: var(--serif);
            font-style: italic;
            font-weight: 500;
            font-size: clamp(54px, 7.5vw, 112px);
            line-height: 0.92;
            letter-spacing: -0.035em;
            color: var(--ink);
            margin: 0 0 var(--s-6);
        }
        .hero h1 .accent { color: var(--accent); font-weight: 600; }
        .hero h1 .roman { font-style: normal; }
        .hero .lede {
            margin-bottom: var(--s-8);
            max-width: 44ch;
        }
        .hero-cta {
            display: flex;
            gap: var(--s-3);
            flex-wrap: wrap;
        }
        .hero-meta {
            margin-top: var(--s-8);
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--ink-faint);
            display: flex;
            gap: var(--s-6);
            flex-wrap: wrap;
        }
        .hero-meta .dot { width: 4px; height: 4px; border-radius: 50%; background: var(--accent); display: inline-block; margin-right: var(--s-2); transform: translateY(-2px); }

        /* Receipt mock in hero. A folded-paper card with an itemized list. */
        .receipt {
            background: var(--card);
            border: 1px solid var(--rule);
            border-radius: 4px;
            padding: var(--s-6) var(--s-6) var(--s-5);
            box-shadow:
                0 1px 0 oklch(0.220 0.024 60 / 0.04),
                0 20px 60px -32px oklch(0.220 0.024 60 / 0.30),
                0 80px 100px -60px oklch(0.500 0.160 45 / 0.22);
            transform: rotate(-1.5deg);
            position: relative;
            font-family: var(--mono);
            font-size: 12.5px;
            color: var(--ink);
            max-width: 360px;
            margin-left: auto;
        }
        .receipt::before, .receipt::after {
            content: '';
            position: absolute;
            left: 0; right: 0;
            height: 12px;
            background:
                radial-gradient(circle at 6px 6px, var(--bg) 5px, transparent 5.5px) 0 0/12px 12px repeat-x;
        }
        .receipt::before { top: -6px; }
        .receipt::after  { bottom: -6px; transform: scaleY(-1); }
        .receipt h4 {
            font-family: var(--serif);
            font-style: italic;
            font-weight: 500;
            font-size: 19px;
            margin: 0 0 var(--s-1);
            color: var(--ink);
        }
        .receipt .place {
            font-family: var(--mono);
            font-size: 10.5px;
            letter-spacing: 0.18em;
            color: var(--ink-faint);
            text-transform: uppercase;
            margin: 0 0 var(--s-4);
        }
        .receipt ul {
            list-style: none;
            padding: 0;
            margin: 0 0 var(--s-4);
            display: flex;
            flex-direction: column;
            gap: var(--s-2);
        }
        .receipt li {
            display: flex;
            justify-content: space-between;
            font-variant-numeric: tabular-nums;
        }
        .receipt li .qty { color: var(--ink-faint); margin-right: var(--s-3); }
        .receipt .sep {
            border: 0;
            border-top: 1px dashed var(--rule-strong);
            margin: var(--s-3) 0;
        }
        .receipt .total {
            display: flex;
            justify-content: space-between;
            font-weight: 500;
            font-size: 14px;
            font-variant-numeric: tabular-nums;
        }
        .receipt .scanned {
            margin-top: var(--s-4);
            font-family: var(--mono);
            font-size: 10px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--accent-deep);
            display: flex;
            align-items: center;
            gap: var(--s-2);
        }
        .receipt .scanned .pip {
            width: 6px; height: 6px; border-radius: 50%; background: var(--accent);
            box-shadow: 0 0 0 4px oklch(0.620 0.160 45 / 0.18);
        }

        @media (max-width: 880px) {
            .hero-grid { grid-template-columns: 1fr; gap: var(--s-10); }
            .receipt { margin: 0 auto; transform: rotate(-1deg); }
        }

        /* ============ SECTION SCAFFOLD ============ */
        section.band {
            padding: clamp(60px, 10vh, 140px) 0;
            position: relative;
        }
        section.band + section.band { border-top: 1px solid var(--rule); }

        /* ============ FEATURES (varied, not a card grid) ============ */
        .features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--s-14) var(--s-10);
        }
        .feature .num {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--accent);
            margin: 0 0 var(--s-3);
            display: flex;
            align-items: baseline;
            gap: var(--s-3);
        }
        .feature .num::after {
            content: '';
            display: inline-block;
            flex: 1;
            height: 1px;
            background: var(--rule);
        }
        .feature h3 { margin-bottom: var(--s-3); }
        .feature p { color: var(--ink-soft); font-size: 15.5px; margin: 0; }

        /* ============ APP PREVIEW (phone mock) ============ */
        .preview {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--s-14);
            align-items: center;
        }
        .phone-stack {
            position: relative;
            min-height: 580px;
        }
        .phone {
            width: 280px;
            height: 560px;
            border-radius: 38px;
            background: var(--bg-deep);
            border: 1px solid var(--rule-strong);
            box-shadow:
                0 2px 0 oklch(0.220 0.024 60 / 0.05),
                0 30px 80px -30px oklch(0.220 0.024 60 / 0.32),
                0 80px 140px -60px oklch(0.500 0.160 45 / 0.22);
            padding: 14px;
            position: absolute;
        }
        .phone .screen {
            background: var(--bg);
            border-radius: 26px;
            height: 100%;
            padding: var(--s-5) var(--s-4);
            overflow: hidden;
            position: relative;
        }
        .phone .notch {
            position: absolute;
            top: 6px;
            left: 50%;
            width: 86px; height: 22px;
            border-radius: 12px;
            background: var(--ink);
            transform: translateX(-50%);
            opacity: 0.92;
        }
        .phone .screen-eyebrow {
            margin-top: var(--s-6);
            font-family: var(--mono);
            font-size: 9.5px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--ink-faint);
        }
        .phone .screen-month {
            font-family: var(--serif);
            font-style: italic;
            font-size: 26px;
            line-height: 1.1;
            color: var(--ink);
            margin: var(--s-1) 0 var(--s-4);
        }
        .phone .spent {
            display: flex;
            align-items: baseline;
            gap: var(--s-2);
            margin-bottom: var(--s-6);
        }
        .phone .spent .amount {
            font-family: var(--serif);
            font-weight: 500;
            font-size: 38px;
            letter-spacing: -0.02em;
            font-variant-numeric: tabular-nums;
        }
        .phone .spent .cur {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.2em;
            color: var(--ink-faint);
            text-transform: uppercase;
        }
        .phone .bar {
            height: 6px;
            background: var(--rule);
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: var(--s-6);
            display: flex;
        }
        .phone .bar i { display: block; height: 100%; }
        .phone .bar .home  { background: var(--accent); width: 38%; }
        .phone .bar .car   { background: oklch(0.55 0.13 60); width: 22%; }
        .phone .bar .baba  { background: oklch(0.62 0.10 200); width: 18%; }
        .phone .bar .ammi  { background: oklch(0.55 0.10 300); width: 14%; }
        .phone .bar .rest  { background: var(--rule-strong); width: 8%; }

        .phone .lists {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: var(--s-2);
        }
        .phone .lists li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--s-3) 0;
            border-bottom: 1px solid var(--rule);
        }
        .phone .lists li:last-child { border-bottom: none; }
        .phone .lists .swatch { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: var(--s-2); }
        .phone .lists .name { font-size: 14px; color: var(--ink); }
        .phone .lists .amt {
            font-family: var(--mono);
            font-size: 13px;
            color: var(--ink-soft);
            font-variant-numeric: tabular-nums;
        }

        .phone-front {
            left: 0; top: 0;
            transform: rotate(-3deg);
            z-index: 2;
        }
        .phone-back {
            right: 0; top: 32px;
            transform: rotate(4deg);
            opacity: 0.85;
            z-index: 1;
        }
        .phone-back .screen-eyebrow { color: var(--accent-deep); }
        .phone-back .scan-card {
            margin-top: var(--s-6);
            padding: var(--s-4);
            background: var(--card);
            border: 1px solid var(--rule);
            border-radius: 8px;
        }
        .phone-back .scan-card .row {
            display: flex;
            justify-content: space-between;
            font-family: var(--mono);
            font-size: 11.5px;
            color: var(--ink);
            margin-bottom: var(--s-2);
            font-variant-numeric: tabular-nums;
        }
        .phone-back .scan-card .row.faint { color: var(--ink-faint); }
        .phone-back .scan-card .total {
            margin-top: var(--s-3);
            padding-top: var(--s-3);
            border-top: 1px dashed var(--rule-strong);
            display: flex;
            justify-content: space-between;
            font-family: var(--serif);
            font-style: italic;
            font-size: 16px;
        }

        @media (max-width: 880px) {
            .preview { grid-template-columns: 1fr; gap: var(--s-10); }
            .phone-stack { min-height: 640px; max-width: 360px; margin: 0 auto; }
            .phone-front { left: 0; }
            .phone-back  { right: 0; }
        }

        /* ============ SPLIT FLOW STRIP ============ */
        .split-strip {
            background: var(--card-warm);
            border: 1px solid var(--rule);
            border-radius: 4px;
            padding: clamp(28px, 4vw, 56px);
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: clamp(28px, 5vw, 56px);
            align-items: center;
            box-shadow:
                0 1px 0 oklch(0.220 0.024 60 / 0.03),
                0 30px 60px -40px oklch(0.500 0.160 45 / 0.24);
        }
        .split-strip h2 { margin-bottom: var(--s-3); }
        .split-people {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: var(--s-2);
        }
        .person {
            border: 1px solid var(--rule);
            background: var(--card);
            border-radius: 4px;
            padding: var(--s-4) var(--s-2);
            text-align: center;
            font-family: var(--mono);
            font-size: 10px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--ink-soft);
            position: relative;
        }
        .person.on {
            border-color: var(--accent);
            background: oklch(0.96 0.04 75);
            color: var(--accent-deep);
        }
        .person .avatar {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: var(--rule-strong);
            margin: 0 auto var(--s-2);
            font-family: var(--serif);
            font-style: italic;
            font-size: 14px;
            color: var(--card);
            display: flex;
            align-items: center;
            justify-content: center;
            text-transform: none;
            letter-spacing: 0;
        }
        .person.on .avatar { background: var(--accent); }
        .person .check {
            position: absolute;
            top: 6px; right: 6px;
            font-family: var(--mono);
            font-size: 10px;
            color: var(--accent);
        }
        .person.on .check::after { content: '✓'; }

        .split-row {
            margin-top: var(--s-5);
            display: flex;
            justify-content: space-between;
            font-family: var(--mono);
            font-size: 12px;
            color: var(--ink-soft);
            padding-top: var(--s-3);
            border-top: 1px dashed var(--rule-strong);
        }
        .split-row .each {
            color: var(--accent-deep);
            font-weight: 500;
        }

        @media (max-width: 760px) {
            .split-strip { grid-template-columns: 1fr; }
            .split-people { grid-template-columns: repeat(5, 1fr); gap: var(--s-1); }
            .person { padding: var(--s-3) var(--s-1); }
        }

        /* ============ FUEL ============ */
        .fuel {
            display: grid;
            grid-template-columns: 1fr 1.4fr;
            gap: clamp(28px, 5vw, 72px);
            align-items: center;
        }
        .fuel-chart {
            background: var(--card);
            border: 1px solid var(--rule);
            border-radius: 4px;
            padding: var(--s-6);
            box-shadow:
                0 1px 0 oklch(0.220 0.024 60 / 0.04),
                0 20px 40px -28px oklch(0.220 0.024 60 / 0.18);
        }
        .fuel-chart .topline {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: var(--s-4);
        }
        .fuel-chart .label {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--ink-faint);
        }
        .fuel-chart .big {
            font-family: var(--serif);
            font-style: italic;
            font-size: 38px;
            font-weight: 500;
            line-height: 1;
            font-variant-numeric: tabular-nums;
        }
        .fuel-chart .big .unit {
            font-family: var(--mono);
            font-style: normal;
            font-size: 11px;
            letter-spacing: 0.2em;
            color: var(--ink-faint);
            margin-left: var(--s-2);
            text-transform: uppercase;
        }
        .fuel-svg {
            width: 100%;
            height: 140px;
            display: block;
        }
        .fuel-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: var(--s-4);
            margin-top: var(--s-4);
            border-top: 1px dashed var(--rule-strong);
            padding-top: var(--s-4);
        }
        .fuel-row .stat .v {
            font-family: var(--serif);
            font-style: italic;
            font-size: 22px;
            font-variant-numeric: tabular-nums;
        }
        .fuel-row .stat .k {
            font-family: var(--mono);
            font-size: 10px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--ink-faint);
        }

        @media (max-width: 880px) {
            .fuel { grid-template-columns: 1fr; gap: var(--s-8); }
        }

        /* ============ PRICING TEASER ============ */
        .pricing-teaser {
            text-align: center;
        }
        .pricing-grid {
            margin-top: var(--s-10);
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--s-4);
        }
        .price {
            background: var(--card);
            border: 1px solid var(--rule);
            border-radius: 4px;
            padding: var(--s-8) var(--s-6);
            text-align: left;
            position: relative;
        }
        .price.featured {
            border-color: var(--accent);
            box-shadow:
                0 1px 0 oklch(0.620 0.160 45 / 0.08),
                0 30px 50px -28px oklch(0.500 0.160 45 / 0.30);
        }
        .price .tier {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.24em;
            text-transform: uppercase;
            color: var(--ink-soft);
            margin: 0 0 var(--s-3);
        }
        .price.featured .tier { color: var(--accent-deep); }
        .price .amount {
            font-family: var(--serif);
            font-style: italic;
            font-weight: 500;
            font-size: 44px;
            line-height: 1;
            margin: 0 0 var(--s-1);
            font-variant-numeric: tabular-nums;
        }
        .price .amount .cur {
            font-family: var(--mono);
            font-style: normal;
            font-size: 13px;
            color: var(--ink-faint);
            letter-spacing: 0.16em;
            margin-right: var(--s-2);
        }
        .price .amount .per {
            font-family: var(--sans);
            font-style: normal;
            font-size: 14px;
            color: var(--ink-soft);
            margin-left: var(--s-2);
        }
        .price .blurb {
            color: var(--ink-soft);
            font-size: 14px;
            margin: var(--s-3) 0 var(--s-5);
        }
        .price .cta {
            display: inline-flex;
            justify-content: center;
            width: 100%;
            margin-top: var(--s-3);
        }

        @media (max-width: 880px) {
            .pricing-grid { grid-template-columns: 1fr; }
        }

        /* ============ FAQ ============ */
        .faq-list {
            margin-top: var(--s-8);
            border-top: 1px solid var(--rule);
        }
        .faq-list details {
            border-bottom: 1px solid var(--rule);
            padding: var(--s-5) 0;
        }
        .faq-list summary {
            list-style: none;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: var(--s-4);
            font-family: var(--serif);
            font-style: italic;
            font-size: 21px;
            color: var(--ink);
            transition: color 200ms var(--ease-out);
        }
        .faq-list summary::-webkit-details-marker { display: none; }
        @media (hover: hover) and (pointer: fine) {
            .faq-list summary:hover { color: var(--accent-deep); }
        }
        .faq-list summary .plus {
            font-family: var(--mono);
            font-size: 18px;
            color: var(--accent);
            transition: transform 220ms var(--ease-out);
            flex: 0 0 auto;
        }
        .faq-list details[open] summary .plus { transform: rotate(45deg); }
        .faq-list .a {
            font-family: var(--sans);
            font-size: 15.5px;
            color: var(--ink-soft);
            margin-top: var(--s-4);
            max-width: 65ch;
        }

        /* ============ CTA ============ */
        .cta-band {
            text-align: center;
            padding: clamp(60px, 12vh, 140px) var(--s-6);
            background: var(--ink);
            color: oklch(0.97 0.014 80);
            border-radius: 4px;
            margin: var(--s-10) 0 0;
            position: relative;
            overflow: hidden;
        }
        .cta-band::before {
            content: '';
            position: absolute; inset: 0;
            background:
                radial-gradient(80% 60% at 50% 0%, oklch(0.620 0.160 45 / 0.30), transparent 60%);
            pointer-events: none;
        }
        .cta-band > * { position: relative; }
        .cta-band h2 {
            color: oklch(0.97 0.014 80);
            margin-bottom: var(--s-4);
        }
        .cta-band h2 .accent { color: var(--accent-soft); }
        .cta-band p {
            color: oklch(0.85 0.020 80);
            max-width: 48ch;
            margin: 0 auto var(--s-8);
            font-family: var(--serif);
            font-size: 19px;
        }
@endsection

@section('content')

<section class="hero wrap">
    <div class="hero-grid">
        <div>
            <p class="eyebrow rise"><span class="line"></span> A household ledger, kept by hand</p>
            <h1 class="rise d-1">Where the<br><span class="accent">month</span><span class="roman">,</span><br>went<span class="accent">.</span></h1>
            <p class="lede rise d-2">A calm expense tracker for Pakistani households. Snap a receipt, split a bill, log fuel by the litre. <em>See exactly where the rupees go</em>, without spreadsheets and without guesswork.</p>
            <div class="hero-cta rise d-3">
                <a class="btn btn-primary" href="/admin/login">
                    Start free
                    <span class="arrow" aria-hidden="true">→</span>
                </a>
                <a class="btn btn-ghost" href="/pricing">See pricing</a>
            </div>
            <div class="hero-meta rise d-4">
                <span><span class="dot" aria-hidden="true"></span>Free forever tier</span>
                <span><span class="dot" aria-hidden="true"></span>Up to 5 household members</span>
                <span><span class="dot" aria-hidden="true"></span>Android &amp; web</span>
            </div>
        </div>

        <div class="rise d-2" aria-hidden="true">
            <div class="receipt">
                <h4>Imtiaz Super Market</h4>
                <p class="place">Karachi · 17:42</p>
                <ul>
                    <li><span><span class="qty">2×</span>Milk pack 1L</span><span>540</span></li>
                    <li><span><span class="qty">1×</span>Atta 10kg</span><span>1,820</span></li>
                    <li><span><span class="qty">3×</span>Soap</span><span>360</span></li>
                    <li><span><span class="qty">1×</span>Rooh Afza</span><span>440</span></li>
                    <li><span><span class="qty">6×</span>Egg tray</span><span>720</span></li>
                </ul>
                <hr class="sep">
                <div class="total"><span>Total</span><span>Rs 3,880</span></div>
                <p class="scanned"><span class="pip"></span>Scanned · routed to Home</p>
            </div>
        </div>
    </div>
</section>

{{-- ====================== FEATURES ====================== --}}
<section class="band wrap" id="features">
    <div class="reveal">
        <p class="eyebrow"><span class="line"></span> What it does</p>
        <h2 class="h2">Five small frictions<br><span class="accent">removed</span><span class="roman">.</span></h2>
        <p class="lede" style="max-width: 56ch;">Not another budget app that asks you to categorise every transaction by hand. Kharcha is built around the way a household actually shops, drives and pays.</p>
    </div>

    <div class="features" style="margin-top: var(--s-10);">
        <article class="feature reveal">
            <p class="num">01 <span aria-hidden="true">·</span></p>
            <h3 class="h3">Snap. The receipt parses itself.</h3>
            <p>Open the camera on a paper receipt or a phone bill. Gemini reads every line, totals it up, and asks you who in the family it belongs to. Three free scans every month; unlimited on Pro.</p>
        </article>

        <article class="feature reveal">
            <p class="num">02 <span aria-hidden="true">·</span></p>
            <h3 class="h3">Split a bill across the family.</h3>
            <p>Tap two, three, four people on one expense. Kharcha divides it evenly and writes one entry into each person's list, all tied to the same split so the total is always honest.</p>
        </article>

        <article class="feature reveal">
            <p class="num">03 <span aria-hidden="true">·</span></p>
            <h3 class="h3">Fuel, the way drivers measure it.</h3>
            <p>Log a refill by rupees and rate; Kharcha computes the litres, your km/L and your Rs/km between full tanks. A dedicated Fuel tab keeps the car's history away from the household ledger.</p>
        </article>

        <article class="feature reveal">
            <p class="num">04 <span aria-hidden="true">·</span></p>
            <h3 class="h3">A list per person, plus Home and Car.</h3>
            <p>Separate ledgers for each household member, plus Home and Car: one monthly view across all of them. The dashboard answers the question parents actually ask, <em>where did the month go?</em></p>
        </article>

        <article class="feature reveal">
            <p class="num">05 <span aria-hidden="true">·</span></p>
            <h3 class="h3">Invite the family. One household, one ledger.</h3>
            <p>Send an email invitation to your spouse, parent or child; they sign up and join your household instead of starting a separate ledger. Free covers <strong>up to 3 members</strong>; Pro raises it to 5. When you upgrade, <em>everyone in the household inherits Pro</em>, no separate billing.</p>
        </article>
    </div>
</section>

{{-- ====================== APP PREVIEW ====================== --}}
<section class="band wrap">
    <div class="preview">
        <div class="reveal">
            <p class="eyebrow"><span class="line"></span> In your hand</p>
            <h2 class="h2">A month at a glance,<br><span class="accent">person by person</span><span class="roman">.</span></h2>
            <p class="lede">Open Kharcha and the dashboard shows the current month's total broken down across every list. The split bar is the first thing you see, the first thing your family asks about.</p>
            <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: var(--s-3); color: var(--ink-soft); font-size: 15px;">
                <li>· Current month is always the home view.</li>
                <li>· Tap any list to drill into its entries.</li>
                <li>· Pull up the Fuel tab for refill history and km/L.</li>
                <li>· FAB opens a quick menu: fuel refill or other expense.</li>
            </ul>
        </div>

        <div class="phone-stack reveal" aria-hidden="true">
            <div class="phone phone-back">
                <div class="screen">
                    <span class="notch"></span>
                    <p class="screen-eyebrow">Scan</p>
                    <p class="screen-month">Imtiaz</p>
                    <div class="scan-card">
                        <div class="row"><span>Milk 1L × 2</span><span>540</span></div>
                        <div class="row"><span>Atta 10kg</span><span>1,820</span></div>
                        <div class="row"><span>Soap × 3</span><span>360</span></div>
                        <div class="row faint"><span>Rooh Afza</span><span>440</span></div>
                        <div class="row faint"><span>Eggs × 6</span><span>720</span></div>
                        <div class="total"><span>Total</span><span>Rs 3,880</span></div>
                    </div>
                </div>
            </div>
            <div class="phone phone-front">
                <div class="screen">
                    <span class="notch"></span>
                    <p class="screen-eyebrow">May 2026</p>
                    <p class="screen-month">This month</p>
                    <div class="spent">
                        <span class="amount">128,420</span>
                        <span class="cur">Rs</span>
                    </div>
                    <div class="bar" aria-hidden="true">
                        <i class="home" title="Home"></i>
                        <i class="car" title="Car"></i>
                        <i class="baba" title="Baba"></i>
                        <i class="ammi" title="Ammi"></i>
                        <i class="rest" title="Others"></i>
                    </div>
                    <ul class="lists">
                        <li>
                            <span><span class="swatch" style="background: var(--accent);"></span><span class="name">Home</span></span>
                            <span class="amt">48,810</span>
                        </li>
                        <li>
                            <span><span class="swatch" style="background: oklch(0.55 0.13 60);"></span><span class="name">Car</span></span>
                            <span class="amt">28,000</span>
                        </li>
                        <li>
                            <span><span class="swatch" style="background: oklch(0.62 0.10 200);"></span><span class="name">Baba</span></span>
                            <span class="amt">22,310</span>
                        </li>
                        <li>
                            <span><span class="swatch" style="background: oklch(0.55 0.10 300);"></span><span class="name">Ammi</span></span>
                            <span class="amt">18,000</span>
                        </li>
                        <li>
                            <span><span class="swatch" style="background: var(--rule-strong);"></span><span class="name">Kids</span></span>
                            <span class="amt">11,300</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ====================== SPLIT STRIP ====================== --}}
<section class="band wrap">
    <div class="split-strip reveal">
        <div>
            <p class="eyebrow"><span class="line"></span> Split, in one tap</p>
            <h2 class="h2">One bill, four ledgers,<br><span class="accent">no maths</span><span class="roman">.</span></h2>
            <p style="color: var(--ink-soft); font-size: 15.5px; margin: 0;">Dinner out, a wedding gift, a school trip: tap everyone who chipped in and Kharcha divides the total evenly. Every entry is tagged with the same split, so the household total never drifts.</p>
        </div>
        <div>
            <div class="split-people" role="group" aria-label="Split between four people">
                <div class="person on"><div class="avatar">B</div>Baba<span class="check" aria-hidden="true"></span></div>
                <div class="person on"><div class="avatar">A</div>Ammi<span class="check" aria-hidden="true"></span></div>
                <div class="person"><div class="avatar">Z</div>Zara</div>
                <div class="person on"><div class="avatar">H</div>Hamza<span class="check" aria-hidden="true"></span></div>
                <div class="person on"><div class="avatar">N</div>Nani<span class="check" aria-hidden="true"></span></div>
            </div>
            <div class="split-row">
                <span>Total · Rs 6,400</span>
                <span class="each">Each · Rs 1,600</span>
            </div>
        </div>
    </div>
</section>

{{-- ====================== FUEL ====================== --}}
<section class="band wrap">
    <div class="fuel">
        <div class="reveal">
            <p class="eyebrow"><span class="line"></span> The car gets its own page</p>
            <h2 class="h2">Petrol, by the litre,<br><span class="accent">measured properly</span><span class="roman">.</span></h2>
            <p style="color: var(--ink-soft); font-size: 16px;">Type the rate and the rupees. Kharcha calculates the litres, your km since the last full tank, and the Rs/km between fills. Import a year of CSV from old logs in one paste.</p>
        </div>
        <div class="fuel-chart reveal" aria-hidden="true">
            <div class="topline">
                <span class="big">11.8<span class="unit">avg km / L</span></span>
                <span class="label">Last 12 fills</span>
            </div>
            <svg class="fuel-svg" viewBox="0 0 360 140" preserveAspectRatio="none" aria-hidden="true">
                <defs>
                    <linearGradient id="fg" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="oklch(0.620 0.160 45)" stop-opacity="0.30"/>
                        <stop offset="100%" stop-color="oklch(0.620 0.160 45)" stop-opacity="0"/>
                    </linearGradient>
                </defs>
                {{-- Horizontal grid rules. --}}
                <g stroke="oklch(0.880 0.038 80)" stroke-dasharray="2 4">
                    <line x1="0" y1="35"  x2="360" y2="35"/>
                    <line x1="0" y1="70"  x2="360" y2="70"/>
                    <line x1="0" y1="105" x2="360" y2="105"/>
                </g>
                {{-- Area under the line. --}}
                <path d="M 0 80 L 30 70 L 60 90 L 90 60 L 120 65 L 150 55 L 180 70 L 210 45 L 240 55 L 270 40 L 300 60 L 330 35 L 360 50 L 360 140 L 0 140 Z"
                      fill="url(#fg)"/>
                {{-- The line itself. --}}
                <path d="M 0 80 L 30 70 L 60 90 L 90 60 L 120 65 L 150 55 L 180 70 L 210 45 L 240 55 L 270 40 L 300 60 L 330 35 L 360 50"
                      fill="none" stroke="oklch(0.620 0.160 45)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                {{-- Recent points. --}}
                <g fill="oklch(0.500 0.160 45)">
                    <circle cx="270" cy="40" r="3"/>
                    <circle cx="300" cy="60" r="3"/>
                    <circle cx="330" cy="35" r="3"/>
                    <circle cx="360" cy="50" r="3"/>
                </g>
            </svg>
            <div class="fuel-row">
                <div class="stat"><div class="v">Rs 24.30</div><div class="k">per km</div></div>
                <div class="stat"><div class="v">412 L</div><div class="k">this year</div></div>
                <div class="stat"><div class="v">9,840 km</div><div class="k">since Jan</div></div>
            </div>
        </div>
    </div>
</section>

{{-- ====================== PRICING TEASER ====================== --}}
<section class="band wrap pricing-teaser" id="pricing-teaser">
    <div class="reveal">
        <p class="eyebrow" style="justify-content:center;"><span class="line"></span> Pricing</p>
        <h2 class="h2">Free forever for most.<br><span class="accent">Pro</span><span class="roman">, when scans add up.</span></h2>
        <p class="lede" style="margin-left: auto; margin-right: auto; max-width: 52ch;">Manual entries, fuel logs, split bills, every chart: free. Pro lifts the cap on AI receipt scans and supports the project.</p>
    </div>

    <div class="pricing-grid reveal">
        <div class="price">
            <p class="tier">Free</p>
            <p class="amount"><span class="cur">{{ $pricing['currency'] }}</span>0<span class="per">/forever</span></p>
            <p class="blurb">For households that mostly enter expenses by hand and only scan a few receipts a month.</p>
            <ul style="list-style: none; padding: 0; margin: 0; font-size: 14px; color: var(--ink-soft); display: flex; flex-direction: column; gap: var(--s-2);">
                <li>· 3 AI receipt scans / month</li>
                <li>· Unlimited manual entries</li>
                <li>· Unlimited lists, splits, fuel</li>
                <li>· Monthly dashboard &amp; charts</li>
            </ul>
            <a class="btn btn-ghost cta" href="/admin/login">Start free</a>
        </div>

        <div class="price featured">
            <p class="tier">Pro · Monthly</p>
            <p class="amount"><span class="cur">{{ $pricing['currency'] }}</span>{{ $pricing['monthly_display'] }}<span class="per">/month</span></p>
            <p class="blurb">For families who scan most receipts. Unlimited AI scans, early access to whatever ships next.</p>
            <ul style="list-style: none; padding: 0; margin: 0; font-size: 14px; color: var(--ink-soft); display: flex; flex-direction: column; gap: var(--s-2);">
                <li>· <strong style="color: var(--ink);">Unlimited</strong> AI receipt scans</li>
                <li>· Everything in Free</li>
                <li>· Early access to new features</li>
                <li>· Direct support from the maker</li>
            </ul>
            <a class="btn btn-accent cta" href="/admin/login">Choose Pro</a>
        </div>

        <div class="price">
            <p class="tier">Pro · Lifetime</p>
            <p class="amount"><span class="cur">{{ $pricing['currency'] }}</span>{{ $pricing['lifetime_display'] }}<span class="per">/once</span></p>
            <p class="blurb">Pay once, never again. About {{ $pricing['breakeven_months'] }} months of the monthly plan; everything after that is on the house.</p>
            <ul style="list-style: none; padding: 0; margin: 0; font-size: 14px; color: var(--ink-soft); display: flex; flex-direction: column; gap: var(--s-2);">
                <li>· Pro features, forever</li>
                <li>· One payment, no renewals</li>
                <li>· Founder pricing</li>
                <li>· Future features included</li>
            </ul>
            <a class="btn btn-ghost cta" href="/admin/login">Pay once</a>
        </div>
    </div>

    <p style="margin-top: var(--s-8); font-family: var(--mono); font-size: 11px; letter-spacing: 0.22em; text-transform: uppercase; color: var(--ink-faint);">Showing prices in {{ $pricing['name'] }} ({{ $pricing['currency'] }}) · <a href="/pricing#tiers" style="color: var(--accent-deep); text-decoration: underline;">Change country</a></p>
</section>

{{-- ====================== FAQ ====================== --}}
<section class="band wrap" id="faq">
    <div class="reveal" style="max-width: 760px;">
        <p class="eyebrow"><span class="line"></span> Common questions</p>
        <h2 class="h2">Things people ask,<br><span class="accent">before signing up</span><span class="roman">.</span></h2>
    </div>

    <div class="faq-list reveal" style="max-width: 760px;">
        <details>
            <summary>Is Kharcha really free? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">Yes. The free tier has no time limit and no card on file. Every feature except unlimited AI receipt scans is included. Pro exists for families who scan most of their receipts; if you mostly type expenses in by hand, free will last you forever.</p>
        </details>
        <details>
            <summary>Where is my data stored? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">On Kharcha's server in your account, owned by you. Receipt images are sent to Google's Gemini API for parsing and immediately released; the extracted text and totals are kept with your account. Nothing is sold, nothing is shared with advertisers, ever.</p>
        </details>
        <details>
            <summary>Do you support iPhone? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">Android first, web second. iPhone is on the roadmap. The web app already works on Safari for everything except camera-based scanning, which is best on the Android app.</p>
        </details>
        <details>
            <summary>Can I cancel Pro at any time? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">Yes. Monthly Pro can be cancelled in one tap from settings; you stay Pro until the end of the billing period and revert to free after. Lifetime Pro is one payment, no recurring charges to cancel.</p>
        </details>
        <details>
            <summary>Is Kharcha made in Pakistan? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">Yes, by a small team in Karachi. Pricing, language, and the features themselves are designed for Pakistani households. Currency is PKR by default and every receipt the AI has seen is local.</p>
        </details>
        <details>
            <summary>How do household members work? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">From the app, open Settings → Household and send an email invitation. The invitee follows the link, signs up (or signs in if they already have Kharcha), and joins your household. Their entries land in the same ledger as yours. Free households fit up to 3 members; Pro raises the cap to 5. When you upgrade to Pro, every existing member inherits it at no extra cost.</p>
        </details>
        <details>
            <summary>Do I need to verify my email? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">Yes. After signup you get a 6-digit code in your inbox; entering it unlocks the rest of the app. The code expires in 15 minutes and you can request a fresh one any time. This keeps fake signups off the service and means we can reach you when a household member sends an invitation.</p>
        </details>
        <details>
            <summary>What happens if the Gemini quota runs out? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">On Free, the third successful scan in a month is your last until the month rolls over. On Pro the cap is effectively unbounded. Manual entry is always available, on any plan, with no count.</p>
        </details>
    </div>
</section>

{{-- ====================== CTA ====================== --}}
<section class="wrap" id="cta">
    <div class="cta-band reveal">
        <h2 class="h2">Start tracking<br><span class="accent">your month</span><span class="roman">.</span></h2>
        <p>Free forever, no card required. Sign in to the web dashboard, install the Android app, and your household ledger lives in both places.</p>
        <div style="display: flex; gap: var(--s-3); justify-content: center; flex-wrap: wrap;">
            <a class="btn btn-accent" href="/admin/login">
                Open Kharcha
                <span class="arrow" aria-hidden="true">→</span>
            </a>
            <a class="btn btn-ghost" href="/pricing" style="color: oklch(0.97 0.014 80); border-color: oklch(0.97 0.014 80 / 0.30);">See pricing</a>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script>
    // FAQ: expanding one collapses the others. Subtle, expected, not magic.
    document.querySelectorAll('.faq-list details').forEach((d) => {
        d.addEventListener('toggle', () => {
            if (d.open) {
                document.querySelectorAll('.faq-list details').forEach((o) => {
                    if (o !== d) o.open = false;
                });
            }
        });
    });
</script>
@endsection
