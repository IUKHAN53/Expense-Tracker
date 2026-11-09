@extends('marketing.layout', [
    'title' => 'Kharcha · Private household expense tracker',
    'description' => 'Kharcha is a private household expense tracker for Android. No account, encrypted on your phone. Split bills, track fuel km/L, scan receipts. Free, with Kharcha Pro for unlimited scans.',
    'active' => 'home',
])

@section('head-style')
        /* ---------- Hero ---------- */
        .hero { padding-top: clamp(24px, 5vh, 64px); padding-bottom: clamp(56px, 9vh, 112px); }
        .hero-grid {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: clamp(32px, 5vw, 72px);
            align-items: center;
        }
        .hero h1 { margin-bottom: var(--s-6); }
        .hero .lede { font-size: clamp(18px, 1.6vw, 21px); }
        .hero-cta { display: flex; flex-wrap: wrap; gap: var(--s-3); margin-bottom: var(--s-8); }
        .hero-meta { display: flex; flex-wrap: wrap; gap: var(--s-3) var(--s-6); list-style: none; padding: 0; margin: 0; }
        .hero-meta li { display: inline-flex; align-items: center; gap: var(--s-2); font-weight: 600; font-size: 15px; }
        .tick {
            width: 22px; height: 22px; border-radius: 50%; flex: none;
            background: var(--primary); display: inline-grid; place-items: center;
        }
        .tick svg { width: 12px; height: 12px; }

        /* ---------- Phone frames (real app screenshots) ---------- */
        .phone {
            position: relative;
            width: min(100%, 320px);
            aspect-ratio: 540 / 1000;
            border: 10px solid var(--ink);
            border-radius: 44px;
            background: #fff;
            overflow: hidden;
            box-shadow: 14px 14px 0 var(--primary);
        }
        .phone img { display: block; width: 100%; height: auto; }
        .hero-phone { position: relative; justify-self: center; }
        .sticker {
            position: absolute; left: -64px; top: 46%; z-index: 2;
            background: #fff; border: 2px solid var(--ink); border-radius: 16px;
            padding: 12px 16px; display: flex; gap: 12px; align-items: center;
            box-shadow: 6px 6px 0 var(--ink);
        }
        .sticker .coin { width: 36px; height: 36px; border-radius: 50%; background: var(--primary); display: grid; place-items: center; font-weight: 800; }
        .sticker b { display: block; font-size: 15px; letter-spacing: -0.02em; }
        .sticker span { font-size: 13px; color: var(--ink-soft); }

        /* ---------- Sections ---------- */
        .band { padding-top: clamp(64px, 10vh, 120px); padding-bottom: clamp(64px, 10vh, 120px); }
        .section-head { max-width: 760px; margin-bottom: var(--s-10); }

        .features { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--s-4); }
        .feature {
            border: 1.5px solid var(--rule); border-radius: 20px;
            padding: var(--s-6); background: #fff;
            transition: border-color 200ms var(--ease-out), transform 200ms var(--ease-out);
        }
        @media (hover: hover) and (pointer: fine) {
            .feature:hover { border-color: var(--ink); transform: translateY(-2px); }
        }
        .feature .num {
            width: 40px; height: 40px; border-radius: 50%; background: var(--primary);
            display: grid; place-items: center; font-weight: 800; font-size: 15px; margin: 0 0 var(--s-5);
        }
        .feature p { color: var(--ink-soft); font-size: 15.5px; margin: 0; }
        .feature.wide { grid-column: span 2; background: var(--soft); border-color: var(--soft); }

        .screens { display: grid; grid-template-columns: repeat(3, 1fr); gap: clamp(24px, 4vw, 56px); }
        .screen-card { display: flex; flex-direction: column; align-items: center; text-align: center; }
        .screen-card .phone { width: min(100%, 280px); box-shadow: 10px 10px 0 var(--primary); }
        .screen-card h3 { margin-top: var(--s-8); }
        .screen-card p { color: var(--ink-soft); font-size: 15.5px; margin: 0 auto; max-width: 32ch; }

        /* ---------- Privacy (ink band) ---------- */
        .private { background: var(--ink); color: #fff; border-radius: 32px; padding: clamp(40px, 7vw, 88px); }
        .private .eyebrow { color: #BDBDBD; }
        .private .h2 { color: #fff; }
        .private .h2 .accent { color: var(--ink); background: var(--primary); padding: 0 0.12em; border-radius: 8px; -webkit-box-decoration-break: clone; box-decoration-break: clone; }
        .private .lede { color: #CFCFCF; }
        .private-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--s-8); margin-top: var(--s-10); }
        .private-grid h3 { color: #fff; }
        .private-grid p { color: #BDBDBD; font-size: 15.5px; margin: 0; }
        .private-grid .tick { margin-bottom: var(--s-4); width: 32px; height: 32px; }
        .private-grid .tick svg { width: 16px; height: 16px; }
        .private a { color: #fff; }

        /* ---------- Pricing teaser ---------- */
        .pricing-teaser { text-align: center; }
        .pricing-teaser .eyebrow { justify-content: center; }
        .pricing-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--s-4); margin-top: var(--s-10); text-align: left; }
        .pricing-grid .price { border: 1.5px solid var(--rule); padding: var(--s-8) var(--s-6) var(--s-6); display: flex; flex-direction: column; background: #fff; }
        .pricing-grid .tier { font-size: 13px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--ink-soft); margin: 0 0 var(--s-3); }
        .pricing-grid .amount { font-size: 48px; line-height: 1; margin: 0 0 var(--s-4); font-variant-numeric: tabular-nums; }
        .pricing-grid .amount .cur { font-size: 15px; font-weight: 600; color: var(--ink-soft); margin-right: 6px; letter-spacing: 0; }
        .pricing-grid .amount .per { font-size: 15px; font-weight: 500; color: var(--ink-soft); margin-left: 4px; letter-spacing: 0; }
        .pricing-grid .blurb { color: var(--ink-soft); font-size: 15px; margin: 0 0 var(--s-5); }
        .pricing-grid ul { margin-bottom: var(--s-6) !important; }
        .pricing-grid .cta { margin-top: auto; }

        /* ---------- FAQ ---------- */
        .faq-list { border-top: 1.5px solid var(--ink); }
        .faq-list details { border-bottom: 1px solid var(--rule); padding: var(--s-5) 0; }
        .faq-list summary {
            list-style: none; cursor: pointer; display: flex; justify-content: space-between; align-items: center;
            gap: var(--s-4); font-size: 19px;
        }
        .faq-list summary::-webkit-details-marker { display: none; }
        .faq-list summary .plus {
            width: 32px; height: 32px; border-radius: 50%; background: var(--soft); flex: none;
            display: grid; place-items: center; font-size: 20px; transition: transform 220ms var(--ease-out), background 200ms;
        }
        .faq-list details[open] summary .plus { transform: rotate(45deg); background: var(--primary); }
        .faq-list .a { color: var(--ink-soft); font-size: 16px; margin: var(--s-4) 0 0; max-width: 65ch; }
        .faq-list .a a { color: var(--ink) !important; }

        /* ---------- CTA (yellow band) ---------- */
        .cta-band { background: var(--primary); border-radius: 32px; padding: clamp(48px, 8vw, 96px) var(--s-6); text-align: center; }
        .cta-band .h2 .accent { background: none; text-decoration: underline; text-decoration-thickness: 6px; text-underline-offset: 8px; }
        .cta-band p { color: var(--ink); max-width: 46ch; margin: 0 auto var(--s-8); font-size: 18px; }

        @media (max-width: 960px) {
            .hero-grid { grid-template-columns: 1fr; }
            .hero-phone { margin-top: var(--s-6); }
            .sticker { left: -16px; }
            .features { grid-template-columns: 1fr 1fr; }
            .feature.wide { grid-column: span 2; }
            .screens, .private-grid, .pricing-grid { grid-template-columns: 1fr; }
            .screens { gap: var(--s-14); }
        }
        @media (max-width: 600px) {
            .features { grid-template-columns: 1fr; }
            .feature.wide { grid-column: auto; }
            .sticker { display: none; }
            .phone { border-width: 8px; border-radius: 36px; }
        }
@endsection

@section('content')

@php $tick = '<svg viewBox="0 0 12 12" fill="none" stroke="#111" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.5 6.3l2.3 2.2 4.7-5"/></svg>'; @endphp

<section class="hero wrap">
    <div class="hero-grid">
        <div>
            <p class="eyebrow rise">Expense tracker for Android</p>
            <h1 class="display rise d-1">Where the <span class="accent">month</span> went.</h1>
            <p class="lede rise d-2">Kharcha is a private household ledger. Log an expense in seconds, split it across the family, track fuel by the litre and scan receipts, in <em>your currency</em>. No account. Everything stays encrypted on your phone.</p>
            <div class="hero-cta rise d-3">
                <a class="btn btn-primary" href="{{ $playUrl }}" target="_blank" rel="noopener">
                    <span class="play-mark" aria-hidden="true">▶</span>
                    Get it on Google Play
                </a>
                <a class="btn btn-ghost" href="/pricing">See pricing</a>
            </div>
            <ul class="hero-meta rise d-4">
                <li><span class="tick" aria-hidden="true">{!! $tick !!}</span>Free, no account</li>
                <li><span class="tick" aria-hidden="true">{!! $tick !!}</span>Encrypted on your phone</li>
                <li><span class="tick" aria-hidden="true">{!! $tick !!}</span>Works offline</li>
            </ul>
        </div>

        <div class="hero-phone rise d-2">
            <div class="phone">
                <img src="/img/app/home.webp" width="540" height="922" alt="Kharcha home screen: Rs 37,133 spent in November, split across Home, Car and Me, with recent expenses" fetchpriority="high">
            </div>
            <div class="sticker" aria-hidden="true">
                <span class="coin">k</span>
                <div><b>Rs 49,970 less</b><span>than October</span></div>
            </div>
        </div>
    </div>
</section>

{{-- ====================== FEATURES ====================== --}}
<section class="band wrap" id="features">
    <div class="section-head reveal">
        <p class="eyebrow">What it does</p>
        <h2 class="h2">Five small frictions, <span class="accent">removed</span>.</h2>
        <p class="lede">Not another budget app that wants you to categorise every transaction. Kharcha is built around the way a household actually shops, drives and pays.</p>
    </div>

    <div class="features">
        <article class="feature reveal">
            <p class="num">1</p>
            <h3 class="h3">Snap. The receipt reads itself.</h3>
            <p>Every line is read for you; check each item and assign it to the right person or list before saving. Three scans to try; unlimited with Pro.</p>
        </article>
        <article class="feature reveal">
            <p class="num">2</p>
            <h3 class="h3">Split a bill across the family.</h3>
            <p>Pick two, three or four lists on one expense. Kharcha divides it evenly and writes one entry into each, tied together so the total stays honest.</p>
        </article>
        <article class="feature reveal">
            <p class="num">3</p>
            <h3 class="h3">Fuel, the way drivers measure it.</h3>
            <p>Enter any two of amount, rate and litres; Kharcha works out the third, then your km/L and cost per km between full tanks. Top-ups handled correctly.</p>
        </article>
        <article class="feature reveal">
            <p class="num">4</p>
            <h3 class="h3">A list per person, plus Home and Car.</h3>
            <p>Each list can carry a monthly budget. One screen answers the question every family asks: <strong>where did the month go?</strong></p>
        </article>
        <article class="feature wide reveal">
            <p class="num">5</p>
            <h3 class="h3">Private by design. No account, no cloud.</h3>
            <p>Your ledger is encrypted on your phone and never leaves it. No sign-up, no server copy, no ads, no trackers. Save an encrypted backup file whenever you like and restore it on a new phone.</p>
        </article>
    </div>
</section>

{{-- ====================== SCREENS ====================== --}}
<section class="band wrap" id="screens">
    <div class="section-head reveal">
        <p class="eyebrow">In your hand</p>
        <h2 class="h2">Big numbers. <span class="accent">Few taps.</span></h2>
        <p class="lede">Designed like signage: one clear number per screen and one yellow button for the thing you came to do.</p>
    </div>

    <div class="screens">
        <figure class="screen-card reveal" style="margin: 0;">
            <div class="phone"><img src="/img/app/scan.webp" width="540" height="922" loading="lazy" alt="Scanned Carrefour receipt with six items, each assigned to a list and category"></div>
            <figcaption>
                <h3 class="h3">Scan, check, assign</h3>
                <p>Every item on the receipt, editable, with one tap to send it to Home, Car or a person.</p>
            </figcaption>
        </figure>
        <figure class="screen-card reveal" style="margin: 0;">
            <div class="phone"><img src="/img/app/list.webp" width="540" height="922" loading="lazy" alt="Home list for November: Rs 19,980 of a Rs 60,000 budget, broken down by category"></div>
            <figcaption>
                <h3 class="h3">Budgets that talk back</h3>
                <p>See what's left for the month and where it went, category by category.</p>
            </figcaption>
        </figure>
        <figure class="screen-card reveal" style="margin: 0;">
            <div class="phone"><img src="/img/app/refill.webp" width="540" height="922" loading="lazy" alt="New refill screen with amount, rate, litres, odometer and live km per litre"></div>
            <figcaption>
                <h3 class="h3">Refills in ten seconds</h3>
                <p>Last rate prefilled, litres worked out, km/L shown live as you type the odometer.</p>
            </figcaption>
        </figure>
    </div>
</section>

{{-- ====================== PRIVACY ====================== --}}
<section class="band wrap" id="private">
    <div class="private reveal">
        <p class="eyebrow">Private by design</p>
        <h2 class="h2">Your money. <span class="accent">Your phone.</span> Nobody else.</h2>
        <p class="lede">Kharcha 2.0 has no account and no server copy of your ledger. There is nothing for us to leak, sell or lose.</p>
        <div class="private-grid">
            <div>
                <span class="tick" aria-hidden="true">{!! $tick !!}</span>
                <h3 class="h3">Encrypted on device</h3>
                <p>A SQLCipher database with a key kept in the Android Keystore. Cloud backup is switched off.</p>
            </div>
            <div>
                <span class="tick" aria-hidden="true">{!! $tick !!}</span>
                <h3 class="h3">No account, no tracking</h3>
                <p>No sign-up, no analytics, no ads. Scanned photos are read and discarded, never stored.</p>
            </div>
            <div>
                <span class="tick" aria-hidden="true">{!! $tick !!}</span>
                <h3 class="h3">Backups you control</h3>
                <p>Save a password-protected backup file anywhere you like. Read the <a href="/privacy">privacy notice</a>.</p>
            </div>
        </div>
    </div>
</section>

{{-- ====================== PRICING TEASER ====================== --}}
<section class="band wrap pricing-teaser" id="pricing-teaser">
    <div class="reveal">
        <p class="eyebrow" style="justify-content:center;">Pricing</p>
        <h2 class="h2">Free to track. <span class="accent">Pro to scan.</span></h2>
        <p class="lede" style="margin-left: auto; margin-right: auto; max-width: 52ch;">Entries, lists, splits, fuel and encrypted backups: free, no account. Pro adds unlimited receipt scanning, billed by Google Play with a 7-day free trial.</p>
    </div>

    <div class="pricing-grid reveal">
        <div class="price">
            <p class="tier">Free</p>
            <p class="amount"><span class="cur">{{ $pricing['currency'] }}</span>0<span class="per">/forever</span></p>
            <p class="blurb">Everything you need to track a household by hand, private on your phone.</p>
            <ul style="list-style: none; padding: 0; margin: 0; font-size: 14px; color: var(--ink-soft); display: flex; flex-direction: column; gap: var(--s-2);">
                <li>· 3 receipt scans to try</li>
                <li>· Unlimited entries and lists</li>
                <li>· Splits, budgets, fuel km/L</li>
                <li>· Encrypted backups, CSV export</li>
            </ul>
            <a class="btn btn-ghost cta" href="{{ $playUrl }}">Get the app</a>
        </div>

        <div class="price featured">
            <p class="tier">Pro · Yearly</p>
            <p class="amount"><span class="cur">{{ $pricing['currency'] }}</span>{{ $pricing['yearly_display'] }}<span class="per">/year</span></p>
            <p class="blurb">Snap a bill instead of typing it. Save {{ $pricing['yearly_saving'] }}% against monthly.</p>
            <ul style="list-style: none; padding: 0; margin: 0; font-size: 14px; color: var(--ink-soft); display: flex; flex-direction: column; gap: var(--s-2);">
                <li>· <strong style="color: var(--ink);">Unlimited</strong> receipt scans</li>
                <li>· Edit and assign every item</li>
                <li>· Petrol slips fill the refill form</li>
                <li>· 7 days free</li>
            </ul>
            <a class="btn btn-accent cta" href="{{ $playUrl }}">Start free trial</a>
        </div>

        <div class="price">
            <p class="tier">Pro · Monthly</p>
            <p class="amount"><span class="cur">{{ $pricing['currency'] }}</span>{{ $pricing['monthly_display'] }}<span class="per">/month</span></p>
            <p class="blurb">The same Pro, month to month. Cancel any time in Google Play.</p>
            <ul style="list-style: none; padding: 0; margin: 0; font-size: 14px; color: var(--ink-soft); display: flex; flex-direction: column; gap: var(--s-2);">
                <li>· <strong style="color: var(--ink);">Unlimited</strong> receipt scans</li>
                <li>· Everything in Free</li>
                <li>· 7 days free</li>
            </ul>
            <a class="btn btn-ghost cta" href="{{ $playUrl }}">Start free trial</a>
        </div>
    </div>

    <p style="margin-top: var(--s-8); font-family: var(--mono); font-size: 11px; letter-spacing: 0.22em; text-transform: uppercase; color: var(--ink-faint);">Showing prices in {{ $pricing['name'] }} ({{ $pricing['currency'] }}) · <a href="/pricing#tiers" style="color: var(--accent-deep); text-decoration: underline;">Change country</a></p>
</section>

{{-- ====================== FAQ ====================== --}}
<section class="band wrap" id="faq">
    <div class="reveal" style="max-width: 760px;">
        <p class="eyebrow">Common questions</p>
        <h2 class="h2">Things people ask, <span class="accent">before installing</span>.</h2>
    </div>

    <div class="faq-list reveal" style="max-width: 760px;">
        <details>
            <summary>Is Kharcha really free? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">Yes. Entries, lists, splits, budgets, fuel tracking and encrypted backups are free with no time limit and no account. Kharcha Pro adds unlimited receipt scanning; everyone gets 3 scans to try it.</p>
        </details>
        <details>
            <summary>Do I need an account? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">No. There is no sign-up and no password. Open the app, pick your currency, and start. Pro is tied to your Google Play account, so it follows you to a new phone.</p>
        </details>
        <details>
            <summary>Where is my data stored? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">Only on your phone, in a database encrypted with SQLCipher. The key stays in the Android Keystore. Nothing is synced to a server. When you scan a receipt, the photo is read by our server using Google Gemini and then discarded; see the <a href="/privacy" style="color: var(--accent-deep); text-decoration: underline;">privacy notice</a>.</p>
        </details>
        <details>
            <summary>What if I change phones? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">Save an encrypted backup file from Settings (protected by a password you choose), then restore it on the new phone. Tap Restore purchase on the paywall to bring Pro along.</p>
        </details>
        <details>
            <summary>Do you support iPhone? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">Not yet. Kharcha is Android only for now.</p>
        </details>
        <details>
            <summary>Can I cancel Pro at any time? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">Yes, in the Play Store under Payments &amp; subscriptions. Pro stays on until the end of the period you paid for, and none of your data is touched.</p>
        </details>
        <details>
            <summary>Which currencies does Kharcha support? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">USD, EUR, GBP, INR, PKR, BDT, LKR, AED, SAR, CAD, AUD and CNY. Pick one on first launch and change it any time in Settings.</p>
        </details>
        <details>
            <summary>Who built Kharcha? <span class="plus" aria-hidden="true">+</span></summary>
            <p class="a">A solo developer in Karachi, Pakistan, who uses it every day for the family budget and the car.</p>
        </details>
    </div>
</section>

{{-- ====================== CTA ====================== --}}
<section class="wrap" id="cta">
    <div class="cta-band reveal">
        <h2 class="h2">Start tracking <span class="accent">your month</span>.</h2>
        <p>Free, no account, encrypted on your phone. Install it and log your first expense in a few seconds.</p>
        <a class="btn btn-primary" href="{{ $playUrl }}" target="_blank" rel="noopener">
            <span class="play-mark" aria-hidden="true">▶</span>
            Get it on Google Play
        </a>
    </div>
</section>

@endsection

@section('scripts')
<script>
    // FAQ: expanding one collapses the others.
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
