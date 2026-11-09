@extends('marketing.layout', [
    'title' => 'Terms · Kharcha',
    'description' => 'The terms that govern your use of Kharcha: what you can expect from us, what we ask of you, and what happens if either side stops.',
])

@section('head-style')
        .legal { padding-top: clamp(40px, 6vh, 80px); padding-bottom: clamp(60px, 10vh, 120px); }
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
            letter-spacing: -0.02em;
        }
        .legal article p, .legal article ul, .legal article ol {
            font-size: 16px;
            color: var(--ink);
            line-height: 1.7;
            margin: 0 0 var(--s-4);
            max-width: 68ch;
        }
        .legal article ul, .legal article ol { padding-left: var(--s-5); }
        .legal article a { color: var(--accent-deep); text-decoration: underline; text-underline-offset: 3px; }
        .legal article strong { color: var(--ink); font-weight: 500; }
        .legal article > section { border-top: 1px solid var(--rule); padding-top: var(--s-3); }
        .legal article > section:first-of-type { border-top: 0; padding-top: 0; }
        .legal article > section:first-of-type h2 { margin-top: 0; }

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
        <p class="eyebrow"><span class="line"></span> Terms of use</p>
        <h1 class="display">A short<br><span class="accent">honest</span> agreement.</h1>
        <p class="legal-meta">Effective 9 November 2026 · Kharcha, Karachi, Pakistan</p>
    </header>

    <nav class="toc reveal" aria-label="Contents">
        <p>Contents</p>
        <ol>
            <li><a href="#agreement">The agreement</a></li>
            <li><a href="#account">Using the app</a></li>
            <li><a href="#use">Acceptable use</a></li>
            <li><a href="#data">Your data</a></li>
            <li><a href="#pro">Pro plans &amp; billing</a></li>
            <li><a href="#refunds">Refunds</a></li>
            <li><a href="#availability">Availability</a></li>
            <li><a href="#warranty">No warranty</a></li>
            <li><a href="#liability">Liability</a></li>
            <li><a href="#termination">Termination</a></li>
            <li><a href="#law">Governing law</a></li>
            <li><a href="#changes">Changes</a></li>
        </ol>
    </nav>

    <article class="reveal">
        <section id="agreement">
            <h2>1. The agreement</h2>
            <p>By installing or using Kharcha you agree to these terms. They form a contract between you and the team that runs Kharcha, registered in Karachi, Pakistan. If you do not agree, do not use the service.</p>
        </section>

        <section id="account">
            <h2>2. Using the app</h2>
            <p>Kharcha 2.0 and later has no accounts. Your data is stored, encrypted, on your device, and you are responsible for keeping that device and any backup passwords safe. We cannot recover data from a lost phone or a forgotten backup password.</p>
            <p>You must be 13 or older to use Kharcha.</p>
        </section>

        <section id="use">
            <h2>3. Acceptable use</h2>
            <p>Use Kharcha for tracking household expenses. Do not use it to:</p>
            <ul>
                <li>Scan or store anything illegal under Pakistani law.</li>
                <li>Reverse-engineer the AI, the API, or the app.</li>
                <li>Spam, abuse, or attempt to harm other users or the service.</li>
                <li>Resell or automate access to the receipt scanning service.</li>
            </ul>
            <p>We may block access to scanning for devices that violate this section. Where the violation is unambiguous (the law has been broken, the service has been abused) we will suspend without notice.</p>
        </section>

        <section id="data">
            <h2>4. Your data</h2>
            <p>Everything in your ledger remains <strong>yours</strong>, and it stays on your device. The only data we process is a receipt photo you choose to scan, which we read through Google Gemini and discard, as described in the <a href="/privacy">privacy notice</a>.</p>
            <p>You can save an encrypted backup or export a CSV at any time.</p>
        </section>

        <section id="pro">
            <h2>5. Pro plans &amp; billing</h2>
            <ul>
                <li><strong>Kharcha Pro</strong> is an auto-renewing subscription, monthly or yearly, sold and billed by Google Play in your local currency. Prices are shown in Google Play before you confirm and on our <a href="/pricing">pricing page</a>.</li>
                <li><strong>Free trial</strong>: new subscribers get 7 days free. If you don't cancel before the trial ends, Google Play charges the plan you chose.</li>
                <li><strong>Cancelling</strong>: cancel any time in the Play Store. Pro continues until the end of the period you have paid for.</li>
                <li><strong>Taxes and payment problems</strong> are handled by Google Play under its own terms. If a renewal fails, Pro pauses; your data is never affected.</li>
            </ul>
        </section>

        <section id="refunds">
            <h2>6. Refunds</h2>
            <p>Refunds for Pro are handled by Google Play: request one from your Play order history. If Google declines and you think it is unfair, email us and we will look at it.</p>
        </section>

        <section id="availability">
            <h2>7. Availability</h2>
            <p>The app works offline. Receipt scanning needs our server and Google Gemini, which we aim to keep available but cannot guarantee; we do not offer credits for downtime.</p>
        </section>

        <section id="warranty">
            <h2>8. No warranty</h2>
            <p>Kharcha is provided as is. We make no warranty that the AI scan will be 100% accurate; receipts vary, photos vary, models vary. <strong>Always review parsed receipts before saving them.</strong> Treat Kharcha's totals as your records of what you spent, not as legally binding accounting.</p>
        </section>

        <section id="liability">
            <h2>9. Liability</h2>
            <p>To the extent permitted by Pakistani law, our total liability to you for anything arising out of Kharcha is capped at the total amount you have paid us in the 12 months before the claim. For free use that cap is zero; for Pro it is what you paid. We are not liable for indirect, consequential, or special damages.</p>
        </section>

        <section id="termination">
            <h2>10. Termination</h2>
            <p>You can stop using Kharcha at any time; uninstalling deletes your data from the device. We can block scanning for a device that violates these terms.</p>
        </section>

        <section id="law">
            <h2>11. Governing law</h2>
            <p>These terms are governed by the laws of Pakistan. Any dispute that cannot be resolved by email is subject to the exclusive jurisdiction of the courts of Karachi.</p>
        </section>

        <section id="changes">
            <h2>12. Changes</h2>
            <p>We may update these terms. Material changes will be posted here with a new effective date and announced in the app at least 14 days before they take effect. Continued use after a change means you accept it.</p>
        </section>
    </article>
</section>

@endsection
