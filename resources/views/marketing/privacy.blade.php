@extends('marketing.layout', [
    'title' => 'Privacy · Kharcha',
    'description' => 'How Kharcha handles your data: what is stored, what is sent where, what is never shared. Written in plain English by the people who wrote the code.',
])

@section('head-style')
        .legal {
            padding: clamp(40px, 6vh, 80px) 0 clamp(60px, 10vh, 120px);
        }
        .legal-head { margin-bottom: var(--s-10); }
        .legal-meta {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--ink-faint);
            margin-top: var(--s-5);
        }
        .legal article h2 {
            font-family: var(--serif);
            font-style: italic;
            font-weight: 500;
            font-size: 32px;
            margin: var(--s-14) 0 var(--s-4);
            color: var(--ink);
            letter-spacing: -0.02em;
        }
        .legal article h3 {
            font-family: var(--sans);
            font-weight: 500;
            font-size: 16px;
            margin: var(--s-8) 0 var(--s-3);
            color: var(--ink);
        }
        .legal article p {
            font-size: 16px;
            color: var(--ink);
            line-height: 1.7;
            margin: 0 0 var(--s-4);
            max-width: 68ch;
        }
        .legal article ul {
            font-size: 16px;
            color: var(--ink);
            line-height: 1.7;
            margin: 0 0 var(--s-4);
            padding-left: var(--s-5);
            max-width: 68ch;
        }
        .legal article a { color: var(--accent-deep); text-decoration: underline; text-underline-offset: 3px; }
        .legal article strong { color: var(--ink); font-weight: 500; }
        .legal article em { color: var(--accent-deep); font-style: italic; }
        .legal article > section { border-top: 1px solid var(--rule); padding-top: var(--s-3); }
        .legal article > section:first-of-type { border-top: 0; padding-top: 0; }

        .toc {
            background: var(--card);
            border: 1px solid var(--rule);
            border-radius: 4px;
            padding: var(--s-6);
            margin: var(--s-8) 0 var(--s-10);
        }
        .toc p {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--ink-faint);
            margin: 0 0 var(--s-3);
        }
        .toc ol {
            list-style: none;
            counter-reset: item;
            padding: 0;
            margin: 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--s-2) var(--s-5);
        }
        .toc li { counter-increment: item; font-size: 14.5px; color: var(--ink-soft); }
        .toc li::before { content: counter(item, decimal-leading-zero) " · "; color: var(--accent); font-family: var(--mono); font-size: 11px; }
        .toc a { color: var(--ink-soft); text-decoration: none; }
        @media (hover: hover) and (pointer: fine) {
            .toc a:hover { color: var(--accent); }
        }
        @media (max-width: 720px) { .toc ol { grid-template-columns: 1fr; } }
@endsection

@section('content')

<section class="legal wrap-narrow">
    <header class="legal-head reveal">
        <p class="eyebrow"><span class="line"></span> Privacy notice</p>
        <h1 class="display">Your data,<br><span class="accent">your ledger</span><span class="roman">.</span></h1>
        <p class="legal-meta">Effective {{ now()->format('d F Y') }} · Kharcha, Karachi, Pakistan</p>
    </header>

    <nav class="toc reveal" aria-label="Contents">
        <p>Contents</p>
        <ol>
            <li><a href="#who">Who runs Kharcha</a></li>
            <li><a href="#what">What we collect</a></li>
            <li><a href="#why">Why we collect it</a></li>
            <li><a href="#ai">Receipts and the AI</a></li>
            <li><a href="#share">Who we share with</a></li>
            <li><a href="#store">Where it lives</a></li>
            <li><a href="#keep">How long we keep it</a></li>
            <li><a href="#rights">Your rights</a></li>
            <li><a href="#kids">Children</a></li>
            <li><a href="#changes">Changes</a></li>
            <li><a href="#contact">Contact</a></li>
        </ol>
    </nav>

    <article class="reveal">
        <section id="who">
            <h2>1. Who runs Kharcha</h2>
            <p>Kharcha is built and operated by a small team in Karachi, Pakistan. The service runs at <a href="https://expense.iukhan.tech">expense.iukhan.tech</a> and through the Kharcha Android app. We are the data controller for everything described below.</p>
        </section>

        <section id="what">
            <h2>2. What we collect</h2>
            <h3>You give us</h3>
            <ul>
                <li><strong>Account basics</strong>: your name, email, household name, hashed password.</li>
                <li><strong>Expenses</strong>: every entry you record, with its amount, date, list, and optional notes.</li>
                <li><strong>Receipt photos</strong>: only when you choose to scan one. See section 4.</li>
                <li><strong>Fuel logs</strong>: odometer, litres, rate, full-tank flag.</li>
            </ul>
            <h3>We collect automatically</h3>
            <ul>
                <li><strong>Sign-in tokens</strong>: a Sanctum API token per device for the app to stay signed in.</li>
                <li><strong>Server logs</strong>: IP address, timestamp, and the URL you requested. Used for debugging and abuse-prevention, rotated every 14 days.</li>
                <li><strong>Plan and scan usage</strong>: which plan your household is on, and how many AI scans you have used this month.</li>
            </ul>
            <p>That is the full list. We do not run third-party analytics or advertising trackers.</p>
        </section>

        <section id="why">
            <h2>3. Why we collect it</h2>
            <p>So Kharcha can do what you signed up for: keep your household ledger, parse the receipts you scan, calculate your fuel economy, and let you sign in across devices. Server logs exist so a real human can diagnose problems when something breaks.</p>
            <p>We do not use your expense data to profile you, score you for credit, or train any model.</p>
        </section>

        <section id="ai">
            <h2>4. Receipts and the AI</h2>
            <p>When you scan a receipt, the photo is sent to <strong>Google Gemini</strong> through Google's API to extract the text, line items and total. Google's API terms for paid usage state that requests are not used to train Google's foundation models. We forward the photo, receive the parsed result, and immediately discard the photo from memory.</p>
            <p>What stays in your account afterwards: the parsed text, the per-line items, the total, and a thumbnail you can review later. The original photo is not stored on Kharcha's server.</p>
            <p>If you do not want any data leaving Kharcha's server, do not use the scan feature; everything works without it through manual entry.</p>
        </section>

        <section id="share">
            <h2>5. Who we share with</h2>
            <ul>
                <li><strong>Google Gemini</strong>: only the receipt photo, only when you scan.</li>
                <li><strong>Our hosting provider (Hostinger VPS, Frankfurt)</strong>: as the operator of the server your data lives on.</li>
                <li><strong>Payment provider</strong> (when Pro launches): only the data required to take the payment (name, email, amount). We never receive your card number.</li>
                <li><strong>Pakistani authorities</strong>: if compelled by a valid Pakistani court order. We have never received one.</li>
            </ul>
            <p><em>We do not sell, rent, or share your data with advertisers, ever.</em></p>
        </section>

        <section id="store">
            <h2>6. Where it lives</h2>
            <p>Kharcha runs on a single VPS in Frankfurt, Germany, operated by Hostinger. The database is SQLite, stored on the same machine, backed up daily to encrypted off-site storage. Cross-border transfer happens because the server is outside Pakistan; by signing up you consent to this transfer for the purpose of running the service.</p>
        </section>

        <section id="keep">
            <h2>7. How long we keep it</h2>
            <ul>
                <li><strong>While your account is active</strong>: as long as you keep using Kharcha.</li>
                <li><strong>After you delete your account</strong>: account, entries, receipts and fuel logs are removed within 30 days. Backups age out within another 60 days, after which nothing is recoverable.</li>
                <li><strong>Server logs</strong>: 14 days, then deleted.</li>
                <li><strong>Receipt photos</strong>: not stored at all. See section 4.</li>
            </ul>
        </section>

        <section id="rights">
            <h2>8. Your rights</h2>
            <p>You can, at any time:</p>
            <ul>
                <li><strong>Export</strong> every entry, fuel log, and household setting as CSV from settings.</li>
                <li><strong>Correct</strong> anything in your ledger by editing it.</li>
                <li><strong>Delete</strong> your account, which deletes everything tied to it within 30 days.</li>
                <li><strong>Ask</strong> what we have on you and we will tell you, in plain language, within seven days.</li>
            </ul>
        </section>

        <section id="kids">
            <h2>9. Children</h2>
            <p>Kharcha is not designed for children under 13. We do not knowingly collect their data; if you believe a child has created an account, email us and we will delete it.</p>
        </section>

        <section id="changes">
            <h2>10. Changes</h2>
            <p>We will post any material change here with a new effective date, and email anyone with a Pro plan at least 14 days in advance. The history of changes is kept publicly so you can see what changed and when.</p>
        </section>

        <section id="contact">
            <h2>11. Contact</h2>
            <p>Email <a href="mailto:privacy@iukhan.tech">privacy@iukhan.tech</a> for any privacy question, a data request, or to flag a concern. A real human reads every email and replies within seven days.</p>
        </section>
    </article>
</section>

@endsection
