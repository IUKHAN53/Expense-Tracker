@extends('marketing.layout', [
    'title' => 'Terms · Kharcha',
    'description' => 'The terms that govern your use of Kharcha: what you can expect from us, what we ask of you, and what happens if either side stops.',
])

@section('head-style')
        .legal { padding: clamp(40px, 6vh, 80px) 0 clamp(60px, 10vh, 120px); }
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
        <h1 class="display">A short<br><span class="accent">honest</span> agreement<span class="roman">.</span></h1>
        <p class="legal-meta">Effective {{ now()->format('d F Y') }} · Kharcha, Karachi, Pakistan</p>
    </header>

    <nav class="toc reveal" aria-label="Contents">
        <p>Contents</p>
        <ol>
            <li><a href="#agreement">The agreement</a></li>
            <li><a href="#account">Your account</a></li>
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
            <p>By creating an account or using Kharcha you agree to these terms. They form a contract between you and the team that runs Kharcha, registered in Karachi, Pakistan. If you do not agree, do not use the service.</p>
        </section>

        <section id="account">
            <h2>2. Your account</h2>
            <p>You may create one account per household. You are responsible for keeping the password to that account secret. If anyone you have given the password to records expenses, those entries are yours.</p>
            <p>You must be 13 or older to create an account. If you create a household for your family, anyone you invite into it is treated as part of your account.</p>
        </section>

        <section id="use">
            <h2>3. Acceptable use</h2>
            <p>Use Kharcha for tracking household expenses. Do not use it to:</p>
            <ul>
                <li>Scan or store anything illegal under Pakistani law.</li>
                <li>Reverse-engineer the AI, the API, or the app.</li>
                <li>Spam, abuse, or attempt to harm other users or the service.</li>
                <li>Resell access to your account.</li>
            </ul>
            <p>We reserve the right to suspend accounts that violate this section. Where the violation is unambiguous (the law has been broken, the service has been abused) we will suspend without notice.</p>
        </section>

        <section id="data">
            <h2>4. Your data</h2>
            <p>Everything in your ledger remains <strong>yours</strong>. We hold it on your behalf so the service can work. We do not claim a licence to your data beyond what is necessary to run Kharcha for you: storing it, displaying it back to you, parsing receipts through Gemini when you ask for it, and including it in our backups.</p>
            <p>You can export it as CSV at any time and delete your account in one tap.</p>
        </section>

        <section id="pro">
            <h2>5. Pro plans &amp; billing</h2>
            <ul>
                <li><strong>Pro Monthly</strong>: PKR 499 per calendar month, charged on the day of the month you subscribed. Cancel any time from settings; access continues to the end of the period you have paid for.</li>
                <li><strong>Pro Lifetime</strong>: PKR 7,999 one-time, paid once and never charged again. "Lifetime" means the lifetime of Kharcha as a service. We have no plans to close, but if Kharcha shuts down we will give at least 90 days' notice and an export of your data.</li>
                <li><strong>All prices are in Pakistani Rupees</strong> and exclude any provincial or federal taxes that may apply.</li>
                <li><strong>Failed payments</strong>: a Monthly subscription falls back to the Free tier after a single failed charge and one reminder. Your data stays; only the unlimited-scan benefit pauses until you update payment.</li>
            </ul>
        </section>

        <section id="refunds">
            <h2>6. Refunds</h2>
            <p>If you ask for a refund within seven days of any payment, we will issue it, no questions. After seven days we look at it case by case and generally still refund. Lifetime is refundable on the same terms.</p>
        </section>

        <section id="availability">
            <h2>7. Availability</h2>
            <p>We aim for 99% uptime measured monthly. We do not offer a credit for downtime; if you find that unacceptable, Kharcha is not the right tool for you yet. We post incidents and post-mortems on the changelog when they happen.</p>
        </section>

        <section id="warranty">
            <h2>8. No warranty</h2>
            <p>Kharcha is provided as is. We make no warranty that the AI scan will be 100% accurate; receipts vary, photos vary, models vary. <strong>Always review parsed receipts before saving them.</strong> Treat Kharcha's totals as your records of what you spent, not as legally binding accounting.</p>
        </section>

        <section id="liability">
            <h2>9. Liability</h2>
            <p>To the extent permitted by Pakistani law, our total liability to you for anything arising out of Kharcha is capped at the total amount you have paid us in the 12 months before the claim. For free accounts that cap is PKR 0; for Pro accounts it is what you paid. We are not liable for indirect, consequential, or special damages.</p>
        </section>

        <section id="termination">
            <h2>10. Termination</h2>
            <p>You can stop using Kharcha and delete your account at any time. We can suspend or terminate your account if you violate these terms or fail to pay a Pro charge. On termination we delete your data within 30 days, as described in the <a href="/privacy">privacy notice</a>.</p>
        </section>

        <section id="law">
            <h2>11. Governing law</h2>
            <p>These terms are governed by the laws of Pakistan. Any dispute that cannot be resolved by email is subject to the exclusive jurisdiction of the courts of Karachi.</p>
        </section>

        <section id="changes">
            <h2>12. Changes</h2>
            <p>We may update these terms. Material changes will be posted here with a new effective date and announced by email to Pro users at least 14 days before they take effect. Continued use after a change means you accept it.</p>
        </section>
    </article>
</section>

@endsection
