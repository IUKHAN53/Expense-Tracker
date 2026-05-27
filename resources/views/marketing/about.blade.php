@extends('marketing.layout', [
    'title' => 'About · Kharcha',
    'description' => 'Kharcha is built by Irfan Ullah, a full-stack developer in Pakistan, to fix a frustration his own household kept hitting. One person, one ledger, calmly maintained.',
])

@section('head-style')
        .about-hero {
            padding: clamp(48px, 8vh, 96px) 0 clamp(24px, 4vh, 56px);
        }

        .prose {
            font-family: var(--serif);
            font-size: clamp(18px, 1.5vw, 21px);
            line-height: 1.65;
            color: var(--ink);
            max-width: 64ch;
        }
        .prose p { margin: 0 0 var(--s-5); }
        .prose em { color: var(--accent-deep); font-style: italic; }
        .prose strong { color: var(--ink); font-weight: 500; }
        .prose a { color: var(--accent-deep); text-decoration: underline; text-underline-offset: 3px; }

        section.band h2 { margin-bottom: var(--s-5); }
        section.band p.lede { max-width: 58ch; }

        .pillars {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: var(--s-10) var(--s-8);
            margin-top: var(--s-10);
        }
        .pillar .num {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--accent);
            margin: 0 0 var(--s-3);
        }
        .pillar h3 {
            font-family: var(--serif);
            font-style: italic;
            font-weight: 500;
            font-size: 22px;
            line-height: 1.2;
            margin: 0 0 var(--s-3);
            color: var(--ink);
        }
        .pillar p { color: var(--ink-soft); font-size: 15px; margin: 0; }

        @media (max-width: 880px) {
            .pillars { grid-template-columns: 1fr; }
        }

        .stat-strip {
            background: var(--card-warm);
            border: 1px solid var(--rule);
            border-radius: 4px;
            padding: clamp(24px, 4vw, 40px);
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--s-6);
        }
        .stat { text-align: center; }
        .stat .v {
            font-family: var(--serif);
            font-style: italic;
            font-weight: 500;
            font-size: 38px;
            line-height: 1;
            color: var(--ink);
            font-variant-numeric: tabular-nums;
        }
        .stat .k {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--ink-soft);
            margin-top: var(--s-2);
        }
        @media (max-width: 720px) { .stat-strip { grid-template-columns: 1fr; gap: var(--s-4); } }

        .stack-list {
            list-style: none;
            padding: 0;
            margin: var(--s-6) 0 0;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--s-3);
            font-family: var(--mono);
            font-size: 11.5px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--ink-soft);
        }
        .stack-list li {
            border: 1px solid var(--rule);
            background: var(--card);
            padding: var(--s-3) var(--s-4);
            border-radius: 2px;
        }
        .stack-list li strong { color: var(--accent-deep); font-weight: 500; }
        @media (max-width: 720px) { .stack-list { grid-template-columns: 1fr 1fr; } }
@endsection

@section('content')

<section class="about-hero wrap">
    <p class="eyebrow rise"><span class="line"></span> About</p>
    <h1 class="display rise d-1">A small tool,<br><span class="accent">built carefully</span><span class="roman">.</span></h1>
    <p class="lede rise d-2" style="margin-top: var(--s-6);">Kharcha is the household ledger I wanted for my own family. It exists because we kept running out of clean answers to a simple question.</p>
</section>

<section class="band wrap">
    <div class="reveal" style="display: grid; grid-template-columns: 1fr 1.4fr; gap: clamp(28px, 5vw, 72px); align-items: start;">
        <div>
            <p class="eyebrow"><span class="line"></span> Why this exists</p>
            <h2 class="h2">The receipts kept piling up<span class="accent">.</span></h2>
        </div>
        <div class="prose">
            <p>For years my household tracked expenses in a spreadsheet that nobody fully trusted. Receipts got lost. Someone would buy groceries on a card, someone else would handle the milk man in cash, and at the end of the month <em>nobody could say where the rupees went</em>.</p>
            <p>The apps we tried wanted us to categorise every transaction by hand, in English, with categories that did not match how we shop. They billed in dollars. They never quite worked.</p>
            <p>So I built one for us. A camera that reads the receipt. A way to split a single bill across the people who chipped in. Fuel logged the way drivers actually measure it, in litres per Pakistani Rupee.</p>
            <p>Then friends asked for a copy, and Kharcha became a product.</p>
        </div>
    </div>
</section>

<section class="band wrap">
    <div class="reveal">
        <p class="eyebrow"><span class="line"></span> The maker</p>
        <h2 class="h2">Irfan Ullah,<br><span class="accent">full-stack developer</span><span class="roman">.</span></h2>
        <div class="prose" style="margin-top: var(--s-6);">
            <p>I am a full-stack developer based in Pakistan. By day I build internal tools for <a href="https://www.iukhan.tech" target="_blank" rel="noopener">Tameer-e-Khalaq Foundation</a>: a Laravel admin panel that tracks field monitoring across the country, a React Native app for issue tracking, and a Laravel/Sanctum data-collection platform used by 100+ field workers daily.</p>
            <p>Outside of TKF I have shipped projects for 70+ Fiverr clients with a 4.9★ rating, mostly in Laravel + React Native. Kharcha is the first thing I have built that is not work-for-hire. <em>It is for my own household first</em>, and yours by extension.</p>
        </div>
    </div>

    <ul class="stack-list reveal" aria-label="Tools Kharcha is built with">
        <li><strong>Laravel 13</strong> &nbsp;Backend</li>
        <li><strong>Filament 5</strong> &nbsp;Admin</li>
        <li><strong>Expo SDK 56</strong> &nbsp;Android</li>
        <li><strong>Google Gemini</strong> &nbsp;Receipt AI</li>
        <li><strong>SQLite</strong> &nbsp;Storage</li>
        <li><strong>Zoho Mail</strong> &nbsp;Transactional</li>
    </ul>
</section>

<section class="band wrap">
    <div class="reveal">
        <p class="eyebrow"><span class="line"></span> What we will not do</p>
        <h2 class="h2">Three commitments<span class="accent">.</span></h2>
    </div>

    <div class="pillars reveal">
        <article class="pillar">
            <p class="num">01</p>
            <h3>We will never sell your data.</h3>
            <p>Not to advertisers, not to banks, not to credit scorers. Your ledger is yours; we hold it on your behalf so the service can work. The <a href="/privacy" style="color: var(--accent-deep); text-decoration: underline;">privacy notice</a> spells out exactly what we share with Google Gemini (the receipt photo, on scan, momentarily) and with our hosting provider, and nothing else.</p>
        </article>
        <article class="pillar">
            <p class="num">02</p>
            <h3>The free tier stays free.</h3>
            <p>Three AI scans a month plus unlimited manual entry, splits, fuel, lists, and household sharing for up to 3 members, with no card on file. Pro lifts the AI quota and raises the member cap; it is not a paywall around the basics.</p>
        </article>
        <article class="pillar">
            <p class="num">03</p>
            <h3>A real person reads support email.</h3>
            <p>Not a chatbot, not a ticket queue. The same person who wrote the code answers the email. Reply times stretch when life gets busy, but they always reach a human.</p>
        </article>
    </div>
</section>

<section class="band wrap">
    <div class="stat-strip reveal">
        <div class="stat">
            <div class="v">2026</div>
            <div class="k">First release</div>
        </div>
        <div class="stat">
            <div class="v">100%</div>
            <div class="k">PKR-first pricing</div>
        </div>
        <div class="stat">
            <div class="v">1</div>
            <div class="k">Maker, replies to email</div>
        </div>
    </div>
</section>

<section class="wrap">
    <div class="reveal" style="text-align: center; padding: clamp(60px, 10vh, 120px) 0;">
        <h2 class="h2">Try Kharcha free.<br><span class="accent">Tell me what breaks.</span></h2>
        <p class="lede" style="margin: var(--s-6) auto var(--s-8); max-width: 48ch;">If you have feedback, a bug, or a feature you wish existed, the fastest path to me is email.</p>
        <div style="display: flex; gap: var(--s-3); justify-content: center; flex-wrap: wrap;">
            <a class="btn btn-primary" href="/admin/login">
                Start free
                <span class="arrow" aria-hidden="true">→</span>
            </a>
            <a class="btn btn-ghost" href="/contact">Get in touch</a>
        </div>
    </div>
</section>

@endsection
