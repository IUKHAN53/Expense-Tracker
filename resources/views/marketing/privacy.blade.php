@extends('marketing.layout', [
    'title' => 'Privacy · Kharcha',
    'description' => 'Kharcha 2.0 has no account and no server: your expenses stay encrypted on your phone. What that means, in plain English.',
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
        <p class="legal-meta">Effective 8 November 2026 · Kharcha, Karachi, Pakistan</p>
    </header>

    <nav class="toc reveal" aria-label="Contents">
        <p>Contents</p>
        <ol>
            <li><a href="#who">Who runs Kharcha</a></li>
            <li><a href="#short">The short version</a></li>
            <li><a href="#app">What the app stores</a></li>
            <li><a href="#network">What leaves your phone</a></li>
            <li><a href="#backups">Backups and exports</a></li>
            <li><a href="#legacy">Older versions and web accounts</a></li>
            <li><a href="#share">Who we share with</a></li>
            <li><a href="#rights">Your rights</a></li>
            <li><a href="#kids">Children</a></li>
            <li><a href="#changes">Changes</a></li>
            <li><a href="#contact">Contact</a></li>
        </ol>
    </nav>

    <article class="reveal">
        <section id="who">
            <h2>1. Who runs Kharcha</h2>
            <p>Kharcha is built and operated by Irfan Ullah in Karachi, Pakistan, and published on Google Play as the Kharcha Android app. The website runs at <a href="https://expense.iukhan.tech">expense.iukhan.tech</a>.</p>
        </section>

        <section id="short">
            <h2>2. The short version</h2>
            <p><strong>Kharcha 2.0 and later has no account and no server.</strong> Everything you enter stays on your phone, in an encrypted database. We do not collect, receive, or see your expenses, and there are no analytics, ads, or trackers in the app.</p>
        </section>

        <section id="app">
            <h2>3. What the app stores</h2>
            <ul>
                <li><strong>Your ledger</strong>: expenses, lists, categories, fuel refills (odometer, litres, rate, full-tank or top-up), and your chosen currency.</li>
                <li><strong>Where</strong>: only on your device, in a SQLite database encrypted with SQLCipher (256-bit). The key is created on your phone and kept in the Android Keystore; it never leaves the device.</li>
                <li><strong>Cloud backup is off</strong>: Kharcha opts out of Android's automatic cloud backup, so the database is not copied to Google Drive.</li>
            </ul>
            <p>Uninstalling the app or clearing its data deletes everything on the device. We cannot recover it, because we never had it.</p>
        </section>

        <section id="network">
            <h2>4. What leaves your phone</h2>
            <p><strong>Nothing.</strong> The app makes no network requests of its own: no accounts, no sync, no analytics, no crash reporting, no ads.</p>
            <ul>
                <li><strong>Google Play</strong>: installs and updates the app under Google's own privacy policy.</li>
                <li><strong>Sharing the app</strong>: the "Share app" screen only opens Android's share sheet or the Play Store with a link to Kharcha.</li>
                <li><strong>Files you export</strong>: backups and CSVs go only where you choose to save or send them (see section 5).</li>
            </ul>
        </section>

        <section id="backups">
            <h2>5. Backups and exports</h2>
            <p>You can save a backup file to a folder you pick. It is encrypted with AES-256-GCM using a password you choose (stretched with scrypt). Without that password nobody, including us, can open it, and a lost password cannot be recovered.</p>
            <p>You can also export a CSV for spreadsheets. CSV files are <em>not</em> encrypted; the app warns you before creating one. Where you keep backup and CSV files, and who you share them with, is up to you.</p>
        </section>

        <section id="legacy">
            <h2>6. Older versions and web accounts</h2>
            <p>Versions before 2.0 used an account on our server (name, email, hashed password, and your ledger), and optional receipt scanning that sent the photo to Google Gemini for parsing. If you used one of those versions, that account data still exists on our server, a VPS in Frankfurt, Germany, until we retire it.</p>
            <p>You can move your data into the new app and ask us to delete the old account at any time; see section 8.</p>
        </section>

        <section id="share">
            <h2>7. Who we share with</h2>
            <p>For Kharcha 2.0: nobody, because we hold no data from the app.</p>
            <p>For older accounts still on our server: only our hosting provider (Hostinger) as the operator of that machine, or Pakistani authorities if compelled by a valid court order (we have never received one).</p>
            <p><em>We do not sell, rent, or share your data with advertisers, ever.</em></p>
        </section>

        <section id="rights">
            <h2>8. Your rights</h2>
            <ul>
                <li><strong>In the app</strong>: edit or delete any entry, export everything, or delete it all by uninstalling the app.</li>
                <li><strong>Older server accounts</strong>: email us to get a copy of your data or to delete the account; we reply within seven days and delete within 30.</li>
            </ul>
        </section>

        <section id="kids">
            <h2>9. Children</h2>
            <p>Kharcha is not designed for children under 13.</p>
        </section>

        <section id="changes">
            <h2>10. Changes</h2>
            <p>If this notice changes in a meaningful way, we will update it here with a new effective date. If a future paid feature (for example AI receipt scanning) ever needs to send anything off your phone, it will be opt-in and described here before it ships.</p>
        </section>

        <section id="contact">
            <h2>11. Contact</h2>
            <p>Email <a href="mailto:privacy@iukhan.tech">privacy@iukhan.tech</a> for any privacy question or data request. A real human reads every email and replies within seven days.</p>
        </section>
    </article>
</section>

@endsection
