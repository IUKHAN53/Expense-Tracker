@extends('marketing.layout', [
    'title' => 'Pricing · Kharcha',
    'description' => 'Kharcha is free with no account. Kharcha Pro adds unlimited receipt scans for PKR 300 a month or PKR 2,500 a year, with a 7-day free trial, billed by Google Play.',
    'active' => 'pricing',
])

@section('head-style')
        .pricing-hero {
            padding-top: clamp(48px, 8vh, 96px); padding-bottom: clamp(32px, 4vh, 56px);
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
            .region-chip:hover { background: var(--soft); color: var(--accent-deep); }
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
            box-shadow: 8px 8px 0 var(--primary);
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
            padding-top: clamp(60px, 8vh, 100px); padding-bottom: clamp(60px, 8vh, 100px);
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
    <h1 class="display rise d-1"><span class="roman">Free</span> <span class="accent">to use</span>,<br><span class="roman">Pro to scan</span>.</h1>
    <p class="lede rise d-2" style="margin-top: var(--s-6);">Everything you type in is free, forever: entries, lists, splits, fuel and encrypted backups. Kharcha Pro adds unlimited receipt scanning. Subscriptions are handled by Google Play, so you can cancel any time from the Play Store.</p>

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
    <p class="region-meta">Showing {{ $pricing['name'] }} ({{ $pricing['currency'] }}). Google Play shows the exact price for your country before you pay.</p>
</section>

<section class="wrap" id="tiers">
    <div class="tiers">
        <div class="tier-card reveal">
            <p class="name">Free</p>
            <p class="price"><span class="cur">{{ $pricing['currency'] }}</span>0</p>
            <p class="per">free forever, no account</p>
            <p class="blurb">Everything you need to track a household by hand, private on your phone.</p>
            <ul>
                <li><strong>3 receipt scans</strong> to try it</li>
                <li>Unlimited entries, lists and categories</li>
                <li>Split an expense across people</li>
                <li>Fuel tracking with km/L and cost per km</li>
                <li>Budgets per list with what's left</li>
                <li>Encrypted backup files and CSV export</li>
                <li>Works fully offline, no sign-up</li>
            </ul>
            <a class="btn btn-ghost cta" href="{{ $playUrl }}">Get it on Google Play</a>
        </div>

        <div class="tier-card featured reveal">
            <span class="badge">Best value</span>
            <p class="name">Pro · Yearly</p>
            <p class="price"><span class="cur">{{ $pricing['currency'] }}</span>{{ $pricing['yearly_display'] }}</p>
            <p class="per">per year, 7 days free, save {{ $pricing['yearly_saving'] }}%</p>
            <p class="blurb">Snap a bill instead of typing it. Every line item is read so you can check it and assign it to a list.</p>
            <ul>
                <li><strong>Unlimited</strong> receipt scans</li>
                <li>Edit and assign every item before saving</li>
                <li>Petrol slips fill in the refill form</li>
                <li>Everything in Free</li>
                <li>7-day free trial for new subscribers</li>
            </ul>
            <a class="btn btn-accent cta" href="{{ $playUrl }}">Start free trial</a>
        </div>

        <div class="tier-card reveal">
            <p class="name">Pro · Monthly</p>
            <p class="price"><span class="cur">{{ $pricing['currency'] }}</span>{{ $pricing['monthly_display'] }}</p>
            <p class="per">per month, 7 days free</p>
            <p class="blurb">The same Pro, billed monthly. Cancel any time in Google Play and keep Pro until the period ends.</p>
            <ul>
                <li><strong>Unlimited</strong> receipt scans</li>
                <li>Everything in Free</li>
                <li>7-day free trial for new subscribers</li>
                <li>Cancel from the Play Store</li>
            </ul>
            <a class="btn btn-ghost cta" href="{{ $playUrl }}">Start free trial</a>
        </div>
    </div>
</section>

{{-- COMPARISON --}}
<section class="band wrap compare">
    <div class="reveal">
        <p class="eyebrow"><span class="line"></span> Compare</p>
        <h2 class="h2">Every feature,<br><span class="accent">side by side</span>.</h2>
    </div>

    <div class="reveal" style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th class="center">Free</th>
                    <th class="center feat">Pro</th>
                </tr>
            </thead>
            <tbody>
                <tr><th scope="row">Receipt scans</th><td class="center">3 to try</td><td class="center">Unlimited</td></tr>
                <tr><th scope="row">Entries, lists, categories</th><td class="center">Unlimited</td><td class="center">Unlimited</td></tr>
                <tr><th scope="row">Split expenses</th><td class="check"></td><td class="check"></td></tr>
                <tr><th scope="row">Fuel tracking (km/L, cost per km)</th><td class="check"></td><td class="check"></td></tr>
                <tr><th scope="row">Budgets per list</th><td class="check"></td><td class="check"></td></tr>
                <tr><th scope="row">Encrypted on-device data</th><td class="check"></td><td class="check"></td></tr>
                <tr><th scope="row">Encrypted backups, CSV export</th><td class="check"></td><td class="check"></td></tr>
                <tr><th scope="row">Account needed</th><td class="center">No</td><td class="center">No</td></tr>
                <tr><th scope="row">Free trial</th><td class="dash"></td><td class="center">7 days</td></tr>
                <tr><th scope="row">Price</th><td class="center">{{ $pricing['currency'] }} 0</td><td class="center">{{ $pricing['currency'] }} {{ $pricing['monthly_display'] }}/mo or {{ $pricing['yearly_display'] }}/yr</td></tr>
            </tbody>
        </table>
    </div>
</section>

{{-- TRUST --}}
<section class="trust wrap">
    <div class="reveal">
        <p class="eyebrow"><span class="line"></span> Fine print, plain language</p>
        <h2 class="h2">No account. No tricks.</h2>
    </div>
    <div class="trust-grid reveal" style="margin-top: var(--s-10);">
        <div class="trust-item">
            <h4>Billed by Google Play</h4>
            <p>Payment, receipts and renewals are handled by Google Play with the method on your Play account. We never see your card.</p>
        </div>
        <div class="trust-item">
            <h4>Cancel any time</h4>
            <p>Cancel in the Play Store under Payments &amp; subscriptions. Pro stays on until the end of the period you paid for; your data is never touched.</p>
        </div>
        <div class="trust-item">
            <h4>Your data stays yours</h4>
            <p>Your ledger lives encrypted on your phone. Scanned photos are read and discarded. Read the <a href="/privacy" style="color: var(--accent-deep); text-decoration: underline;">privacy notice</a> for the details.</p>
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="band wrap" id="faq">
    <div class="reveal" style="max-width: 760px;">
        <p class="eyebrow"><span class="line"></span> Pricing questions</p>
        <h2 class="h2">Asked,<br><span class="accent">answered</span>.</h2>
    </div>

    <div class="faq-list reveal" style="max-width: 760px;">
        <details>
            <summary>How do I pay? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">Inside the app, through Google Play, with whatever payment method your Play account has: card, carrier billing, Play balance or gift cards where available.</p>
        </details>
        <details>
            <summary>How does the free trial work? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">New subscribers get 7 days of Pro free on either plan. Cancel in Google Play before the trial ends and you are not charged.</p>
        </details>
        <details>
            <summary>What counts as one scan? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">One photo of one receipt, however many items it has. A scan that can't be read doesn't count against your free scans.</p>
        </details>
        <details>
            <summary>Do I need an account? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">No. Kharcha has no sign-up. Pro is tied to your Google Play account, so it follows you to a new phone: tap Restore purchase in the app.</p>
        </details>
        <details>
            <summary>What happens to my data if I cancel? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">Nothing. Everything you entered or scanned stays on your phone. You go back to typing entries by hand.</p>
        </details>
        <details>
            <summary>Can I get a refund? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">Request one from Google Play under Order history, or email us and we will help.</p>
        </details>
    </div>
</section>

{{-- CTA --}}
<section class="wrap" id="cta">
    <div class="reveal" style="text-align: center; padding: clamp(60px, 10vh, 120px) 0;">
        <h2 class="h2">Track it free.<br><span class="accent">Scan it with Pro.</span></h2>
        <p class="lede" style="margin: var(--s-6) auto var(--s-8); max-width: 48ch;">Install Kharcha, try three scans on your own receipts, and decide from there.</p>
        <a class="btn btn-primary" href="{{ $playUrl }}">
            Get it on Google Play
            <span class="arrow" aria-hidden="true">→</span>
        </a>
    </div>
</section>

@endsection
