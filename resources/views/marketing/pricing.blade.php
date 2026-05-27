@extends('marketing.layout', [
    'title' => 'Pricing · Kharcha',
    'description' => 'Kharcha pricing in Pakistani Rupees: a free forever tier with three AI scans a month, Pro Monthly at PKR 499, Pro Lifetime at PKR 7,999.',
    'active' => 'pricing',
])

@section('head-style')
        .pricing-hero {
            padding: clamp(48px, 8vh, 96px) 0 clamp(32px, 4vh, 56px);
            text-align: center;
        }
        .pricing-hero .lede { margin-left: auto; margin-right: auto; }

        .region {
            margin-top: var(--s-8);
            display: inline-flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: var(--s-2);
            padding: var(--s-3);
            border: 1px solid var(--rule);
            border-radius: 4px;
            background: var(--card);
        }
        .region-label {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--ink-faint);
            padding: 0 var(--s-2);
        }
        .region-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: var(--s-2) var(--s-3);
            min-height: 32px;
            min-width: 56px;
            border-radius: 2px;
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--ink-soft);
            text-decoration: none;
            background: transparent;
            transition: background 180ms var(--ease-out), color 180ms var(--ease-out);
        }
        @media (hover: hover) and (pointer: fine) {
            .region-chip:hover { background: oklch(0.96 0.030 75); color: var(--accent-deep); }
        }
        .region-chip.on {
            background: var(--ink);
            color: var(--card);
        }
        .region-meta {
            margin-top: var(--s-3);
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--ink-faint);
        }
        .region-meta a {
            color: var(--accent-deep);
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .tiers {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--s-4);
            margin-top: var(--s-10);
        }
        .tier-card {
            background: var(--card);
            border: 1px solid var(--rule);
            border-radius: 4px;
            padding: var(--s-8) var(--s-6) var(--s-6);
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .tier-card.featured {
            border-color: var(--accent);
            box-shadow:
                0 1px 0 oklch(0.620 0.160 45 / 0.10),
                0 40px 60px -28px oklch(0.500 0.160 45 / 0.30);
        }
        .tier-card .badge {
            position: absolute;
            top: -10px; left: var(--s-6);
            background: var(--accent);
            color: var(--card);
            font-family: var(--mono);
            font-size: 10px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            padding: 4px var(--s-3);
            border-radius: 2px;
        }
        .tier-card .name {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.24em;
            text-transform: uppercase;
            color: var(--ink-soft);
            margin: 0 0 var(--s-3);
        }
        .tier-card.featured .name { color: var(--accent-deep); }
        .tier-card .price {
            font-family: var(--serif);
            font-style: italic;
            font-weight: 500;
            font-size: 56px;
            line-height: 1;
            font-variant-numeric: tabular-nums;
            margin: 0 0 var(--s-1);
        }
        .tier-card .price .cur {
            font-family: var(--mono);
            font-style: normal;
            font-size: 13px;
            color: var(--ink-faint);
            letter-spacing: 0.16em;
            margin-right: var(--s-2);
        }
        .tier-card .per {
            font-family: var(--sans);
            font-size: 14px;
            color: var(--ink-soft);
            margin: 0 0 var(--s-5);
        }
        .tier-card .blurb {
            color: var(--ink-soft);
            font-size: 14.5px;
            margin: 0 0 var(--s-6);
        }
        .tier-card ul {
            list-style: none;
            padding: 0;
            margin: 0 0 var(--s-6);
            display: flex;
            flex-direction: column;
            gap: var(--s-3);
        }
        .tier-card li {
            font-size: 14.5px;
            color: var(--ink);
            display: flex;
            gap: var(--s-3);
            align-items: baseline;
        }
        .tier-card li::before {
            content: '·';
            color: var(--accent);
            font-weight: 700;
            font-family: var(--mono);
            font-size: 20px;
            line-height: 0;
        }
        .tier-card li.muted { color: var(--ink-faint); }
        .tier-card li.muted::before { color: var(--rule-strong); }
        .tier-card .cta {
            margin-top: auto;
            display: inline-flex;
            justify-content: center;
        }

        @media (max-width: 900px) {
            .tiers { grid-template-columns: 1fr; }
        }

        /* Comparison table. */
        .compare {
            margin-top: clamp(60px, 10vh, 120px);
            border-top: 1px solid var(--rule);
            border-bottom: 1px solid var(--rule);
        }
        .compare h2 { margin-bottom: var(--s-8); }
        .compare table {
            width: 100%;
            border-collapse: collapse;
            font-family: var(--sans);
            font-size: 14.5px;
        }
        .compare th, .compare td {
            text-align: left;
            padding: var(--s-4) var(--s-3);
            border-bottom: 1px solid var(--rule);
            vertical-align: top;
        }
        .compare thead th {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--ink-faint);
            font-weight: 500;
            padding-bottom: var(--s-3);
        }
        .compare thead th.feat { color: var(--accent-deep); }
        .compare th[scope="row"] {
            font-weight: 500;
            color: var(--ink);
            font-family: var(--sans);
            font-size: 14.5px;
            text-transform: none;
            letter-spacing: 0;
        }
        .compare td.center { text-align: center; font-variant-numeric: tabular-nums; color: var(--ink-soft); }
        .compare td.check::after { content: '✓'; color: var(--accent); font-weight: 700; }
        .compare td.dash::after { content: '·'; color: var(--ink-faint); font-size: 18px; }
        .compare tbody tr:last-child td, .compare tbody tr:last-child th { border-bottom: 0; }

        /* Trust strip. */
        .trust {
            padding: clamp(60px, 8vh, 100px) 0;
        }
        .trust-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--s-10);
        }
        .trust-item h4 {
            font-family: var(--serif);
            font-style: italic;
            font-weight: 500;
            font-size: 22px;
            margin: 0 0 var(--s-2);
            color: var(--ink);
        }
        .trust-item p {
            color: var(--ink-soft);
            font-size: 15px;
            margin: 0;
        }

        @media (max-width: 760px) {
            .trust-grid { grid-template-columns: 1fr; gap: var(--s-6); }
        }

        /* FAQ reuse. */
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
        }
        .faq-list details[open] summary .plus { transform: rotate(45deg); }
        .faq-list .a {
            font-family: var(--sans);
            font-size: 15.5px;
            color: var(--ink-soft);
            margin-top: var(--s-4);
            max-width: 65ch;
        }
@endsection

@section('content')

<section class="pricing-hero wrap">
    <p class="eyebrow rise" style="justify-content: center;"><span class="line"></span> Pricing in {{ $pricing['currency'] }}</p>
    <h1 class="display rise d-1"><span class="roman">Fair</span> <span class="accent">prices</span><span class="roman">,</span><br><span class="roman">in your currency</span><span class="accent">.</span></h1>
    <p class="lede rise d-2" style="margin-top: var(--s-6);">Free for most households, Pro for the families who scan most receipts. Cancel anytime; everything except the AI quota is included on every plan.</p>

    {{-- Region switcher --}}
    <div class="region rise d-3" role="group" aria-label="Choose your country">
        <span class="region-label">Country</span>
        @foreach ($regions as $cc => $r)
            <a href="?cc={{ $cc }}#tiers"
               @class(['region-chip', 'on' => $cc === $pricing['cc']])
               @if($cc === $pricing['cc']) aria-current="true" @endif>
                {{ $r['currency'] }}
            </a>
        @endforeach
    </div>
    <p class="region-meta">Showing {{ $pricing['name'] }} ({{ $pricing['currency'] }}). <a href="?cc={{ \App\Support\RegionalPricing::BASE }}#tiers">Reset to USD</a></p>
</section>

<section class="wrap" id="tiers">
    <div class="tiers">
        <div class="tier-card reveal">
            <p class="name">Free</p>
            <p class="price"><span class="cur">{{ $pricing['currency'] }}</span>0</p>
            <p class="per">free forever, no card</p>
            <p class="blurb">For households entering most expenses by hand and only scanning a few receipts each month.</p>
            <ul>
                <li><strong>3 AI receipt scans</strong> per calendar month</li>
                <li><strong>Up to 3 household members</strong></li>
                <li>Unlimited manual entries</li>
                <li>Unlimited spending lists</li>
                <li>Split expenses across people</li>
                <li>Fuel tracking with km/L and per-km cost</li>
                <li>Monthly dashboard and charts</li>
                <li class="muted">No early access to new features</li>
            </ul>
            <a class="btn btn-ghost cta" href="/#cta">Start free</a>
        </div>

        <div class="tier-card featured reveal">
            <span class="badge">Most popular</span>
            <p class="name">Pro · Monthly</p>
            <p class="price"><span class="cur">{{ $pricing['currency'] }}</span>{{ $pricing['monthly_display'] }}</p>
            <p class="per">per month, cancel anytime</p>
            <p class="blurb">For larger families who scan most receipts. Invited members inherit Pro at no extra cost.</p>
            <ul>
                <li><strong>Unlimited</strong> AI receipt scans</li>
                <li><strong>Up to 5 household members</strong>, all on Pro</li>
                <li>Everything in Free</li>
                <li>Early access to new features</li>
                <li>Direct support from the maker</li>
                <li>Cancel from settings, no email needed</li>
            </ul>
            <a class="btn btn-accent cta" href="/#cta">Choose Pro</a>
        </div>

        <div class="tier-card reveal">
            <p class="name">Pro · Lifetime</p>
            <p class="price"><span class="cur">{{ $pricing['currency'] }}</span>{{ $pricing['lifetime_display'] }}</p>
            <p class="per">one payment, never again</p>
            <p class="blurb">Pay once and Kharcha Pro is yours for life. About {{ $pricing['breakeven_months'] }} months of the monthly plan; everything after is free.</p>
            <ul>
                <li>Pro features, <strong>forever</strong></li>
                <li>Up to 5 household members included</li>
                <li>One payment, zero renewals</li>
                <li>Founder pricing while it lasts</li>
                <li>Future Pro features included</li>
            </ul>
            <a class="btn btn-ghost cta" href="/#cta">Pay once</a>
        </div>
    </div>
</section>

{{-- COMPARISON --}}
<section class="band wrap compare">
    <div class="reveal">
        <p class="eyebrow"><span class="line"></span> Compare</p>
        <h2 class="h2">Every feature,<br><span class="accent">side by side</span><span class="roman">.</span></h2>
    </div>

    <div class="reveal" style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th class="center">Free</th>
                    <th class="center feat">Pro Monthly</th>
                    <th class="center">Pro Lifetime</th>
                </tr>
            </thead>
            <tbody>
                <tr><th scope="row">AI receipt scans</th><td class="center">3 / month</td><td class="center">Unlimited</td><td class="center">Unlimited</td></tr>
                <tr><th scope="row">Household members</th><td class="center">Up to 3</td><td class="center">Up to 5</td><td class="center">Up to 5</td></tr>
                <tr><th scope="row">Pro features inherited by members</th><td class="dash"></td><td class="check"></td><td class="check"></td></tr>
                <tr><th scope="row">Manual entries</th><td class="center">Unlimited</td><td class="center">Unlimited</td><td class="center">Unlimited</td></tr>
                <tr><th scope="row">Spending lists</th><td class="center">Unlimited</td><td class="center">Unlimited</td><td class="center">Unlimited</td></tr>
                <tr><th scope="row">Split expenses</th><td class="check"></td><td class="check"></td><td class="check"></td></tr>
                <tr><th scope="row">Fuel tracking (km/L, Rs/km, CSV import)</th><td class="check"></td><td class="check"></td><td class="check"></td></tr>
                <tr><th scope="row">Monthly dashboard &amp; charts</th><td class="check"></td><td class="check"></td><td class="check"></td></tr>
                <tr><th scope="row">Early access to new features</th><td class="dash"></td><td class="check"></td><td class="check"></td></tr>
                <tr><th scope="row">Direct support from the maker</th><td class="dash"></td><td class="check"></td><td class="check"></td></tr>
                <tr><th scope="row">Auto-renewal</th><td class="center">·</td><td class="center">Monthly</td><td class="center">Never</td></tr>
                <tr><th scope="row">Total over 24 months</th><td class="center">{{ $pricing['currency'] }} 0</td><td class="center">{{ $pricing['currency'] }} {{ number_format($pricing['monthly'] * 24, $pricing['monthly'] == floor($pricing['monthly']) ? 0 : 2) }}</td><td class="center">{{ $pricing['currency'] }} {{ $pricing['lifetime_display'] }}</td></tr>
            </tbody>
        </table>
    </div>
</section>

{{-- TRUST --}}
<section class="trust wrap">
    <div class="reveal">
        <p class="eyebrow"><span class="line"></span> Fine print, plain language</p>
        <h2 class="h2">No tricks. No card<br>until you upgrade<span class="accent">.</span></h2>
    </div>
    <div class="trust-grid reveal" style="margin-top: var(--s-10);">
        <div class="trust-item">
            <h4>No surprise charges</h4>
            <p>Free is free forever. Pro shows the exact rupee amount before you confirm. We never charge a card on file from the free tier.</p>
        </div>
        <div class="trust-item">
            <h4>Cancel in one tap</h4>
            <p>Monthly Pro lives in your settings. Tap cancel and you keep Pro until the end of the billing period, then revert to free with no data loss.</p>
        </div>
        <div class="trust-item">
            <h4>Your data is yours</h4>
            <p>We never sell or share with advertisers. Read the <a href="/privacy" style="color: var(--accent-deep); text-decoration: underline;">privacy notice</a> for the precise list of what is stored, for how long, and why.</p>
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="band wrap" id="faq">
    <div class="reveal" style="max-width: 760px;">
        <p class="eyebrow"><span class="line"></span> Pricing questions</p>
        <h2 class="h2">Asked,<br><span class="accent">answered</span><span class="roman">.</span></h2>
    </div>

    <div class="faq-list reveal" style="max-width: 760px;">
        <details>
            <summary>What payment methods can I use? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">Local cards (Visa, Mastercard, and UnionPay), JazzCash and EasyPaisa are on the way; bank transfer is supported on request for Lifetime. We will publish the exact mix when Pro opens for the first households.</p>
        </details>
        <details>
            <summary>Is there a student or family discount? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">Lifetime at PKR 7,999 is already founder pricing; it will move up. If you are a student or a household genuinely priced out, write in and we will sort something honest.</p>
        </details>
        <details>
            <summary>What counts as one scan? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">One photo of one receipt, regardless of how many line items it contains. A 30-item grocery bill is a single scan. Re-scanning the same receipt because the first photo was blurry counts as one extra scan; we do not refund those.</p>
        </details>
        <details>
            <summary>Will prices ever go up? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">Possibly, slowly, with at least a month of notice. Lifetime is locked at the price you paid for life. Monthly subscribers get the price they signed up at for as long as they stay subscribed, with grandfathering when it makes sense.</p>
        </details>
        <details>
            <summary>Is there a free trial of Pro? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">No trial of Pro itself, because Free already includes every Pro feature except the unlimited scans. Use Free for a month; if you hit the cap and want more, that is the moment Pro is worth it.</p>
        </details>
        <details>
            <summary>What if I want a refund? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">Email within seven days of any payment and we will refund it, no questions. After seven days we look at the situation honestly; usually we still refund.</p>
        </details>
    </div>
</section>

{{-- CTA --}}
<section class="wrap" id="cta">
    <div class="reveal" style="text-align: center; padding: clamp(60px, 10vh, 120px) 0;">
        <h2 class="h2">Try Kharcha free.<br><span class="accent">Upgrade if you outgrow it.</span></h2>
        <p class="lede" style="margin: var(--s-6) auto var(--s-8); max-width: 48ch;">Three scans a month is enough to learn whether Kharcha fits your household. The rest is unlimited from day one.</p>
        <a class="btn btn-primary" href="/#cta">
            Start free
            <span class="arrow" aria-hidden="true">→</span>
        </a>
    </div>
</section>

@endsection
