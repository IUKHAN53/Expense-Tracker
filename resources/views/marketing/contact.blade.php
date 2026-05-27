@extends('marketing.layout', [
    'title' => 'Contact · Kharcha',
    'description' => 'Get in touch with the team behind Kharcha. A real human reads every email and replies within seven days.',
])

@section('head-style')
        .contact-hero {
            padding: clamp(48px, 8vh, 96px) 0 clamp(24px, 4vh, 56px);
        }

        .channels {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--s-6);
            margin-top: var(--s-10);
        }
        .channel {
            background: var(--card);
            border: 1px solid var(--rule);
            border-radius: 4px;
            padding: clamp(24px, 3vw, 36px);
            display: flex;
            flex-direction: column;
        }
        .channel .icon {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: oklch(0.96 0.040 75);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: var(--s-4);
        }
        .channel h3 {
            font-family: var(--serif);
            font-style: italic;
            font-weight: 500;
            font-size: 24px;
            margin: 0 0 var(--s-2);
            color: var(--ink);
        }
        .channel p {
            color: var(--ink-soft);
            font-size: 14.5px;
            line-height: 1.55;
            margin: 0 0 var(--s-5);
        }
        .channel .target {
            font-family: var(--mono);
            font-size: 13px;
            color: var(--accent-deep);
            text-decoration: none;
            border-bottom: 1px dashed var(--accent);
            padding-bottom: 2px;
            display: inline-block;
            margin-top: auto;
        }
        @media (hover: hover) and (pointer: fine) {
            .channel .target:hover { color: var(--accent); border-bottom-color: var(--ink); }
        }

        @media (max-width: 720px) {
            .channels { grid-template-columns: 1fr; }
        }

        .reply-card {
            background: var(--card-warm);
            border: 1px solid var(--rule);
            border-radius: 4px;
            padding: clamp(28px, 4vw, 56px);
            margin-top: clamp(40px, 6vh, 80px);
            text-align: center;
        }
        .reply-card .quote {
            font-family: var(--serif);
            font-style: italic;
            font-weight: 500;
            font-size: clamp(22px, 2.4vw, 32px);
            line-height: 1.4;
            color: var(--ink);
            max-width: 38ch;
            margin: 0 auto var(--s-5);
            letter-spacing: -0.015em;
        }
        .reply-card .quote em { color: var(--accent-deep); }
        .reply-card .sig {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--ink-soft);
        }

        .topics {
            margin-top: clamp(60px, 8vh, 100px);
        }
        .topics-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--s-8);
            margin-top: var(--s-8);
        }
        .topic h4 {
            font-family: var(--mono);
            font-size: 11px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: var(--accent-deep);
            margin: 0 0 var(--s-3);
        }
        .topic p {
            color: var(--ink-soft);
            font-size: 14.5px;
            line-height: 1.55;
            margin: 0;
        }
        .topic p a { color: var(--accent-deep); text-decoration: underline; }
        @media (max-width: 760px) { .topics-grid { grid-template-columns: 1fr; gap: var(--s-5); } }
@endsection

@section('content')

<section class="contact-hero wrap">
    <p class="eyebrow rise"><span class="line"></span> Contact</p>
    <h1 class="display rise d-1">Say hello<span class="accent">.</span></h1>
    <p class="lede rise d-2" style="margin-top: var(--s-6); max-width: 52ch;">A real human reads every email. Replies within seven days, often the same day. Pick whichever channel matches your question.</p>
</section>

<section class="wrap">
    <div class="channels reveal">
        <div class="channel">
            <div class="icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="color: var(--accent-deep);">
                    <path d="M4 6h16v12H4z"/>
                    <path d="M4 6l8 7 8-7"/>
                </svg>
            </div>
            <h3>Email support</h3>
            <p>Bug reports, billing questions, account help, anything technical. Reaches the same inbox that runs the service, no triage layer in between.</p>
            <a class="target" href="mailto:hello@iukhan.tech?subject=Kharcha%20support">hello@iukhan.tech</a>
        </div>

        <div class="channel">
            <div class="icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="color: var(--accent-deep);">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 7v5l3 2"/>
                </svg>
            </div>
            <h3>Privacy & legal</h3>
            <p>Data deletion requests, takedowns, compliance questions, anything that should not go through general support. Replies within seven days.</p>
            <a class="target" href="mailto:privacy@iukhan.tech?subject=Kharcha%20privacy%20request">privacy@iukhan.tech</a>
        </div>
    </div>

    <div class="reply-card reveal">
        <p class="quote">"If something feels off in Kharcha, write to me. I built it; <em>I will read what you wrote</em>."</p>
        <p class="sig">Irfan Ullah · Maker</p>
    </div>

    <div class="topics reveal">
        <p class="eyebrow"><span class="line"></span> Pick a topic</p>
        <h2 class="h2">What are you writing about<span class="accent">?</span></h2>

        <div class="topics-grid">
            <div class="topic">
                <h4>Trouble signing up</h4>
                <p>Verification code not arriving, password reset stuck, anything blocking you at the door. Mention which email you tried; we will look on our side.</p>
            </div>
            <div class="topic">
                <h4>Scan misreading a receipt</h4>
                <p>The AI gets receipts wrong sometimes. Attach the photo and what it should have read; the model improves on real Pakistani receipts the more we see.</p>
            </div>
            <div class="topic">
                <h4>Pro, billing, refunds</h4>
                <p>Refunds within seven days, no questions. See the <a href="/pricing#faq">pricing FAQ</a> for the long answer; email for anything it does not cover.</p>
            </div>
            <div class="topic">
                <h4>Household invitations</h4>
                <p>Invite emails landing in spam, members not appearing, accidental removals. Send the household name and the affected email and we will sort it.</p>
            </div>
            <div class="topic">
                <h4>Delete my account</h4>
                <p>You can delete your own account from Settings in the app. If you cannot reach Settings, email and we will do it for you the same day.</p>
            </div>
            <div class="topic">
                <h4>Press, partnerships, the rest</h4>
                <p>Same address as support. No PR firm, no sales team; whatever it is, just write what you want and a real reply comes back.</p>
            </div>
        </div>
    </div>
</section>

<section class="wrap">
    <div class="reveal" style="text-align: center; padding: clamp(60px, 10vh, 120px) 0;">
        <h2 class="h2">Already a user?<br><span class="accent">Open Kharcha</span><span class="roman">.</span></h2>
        <p class="lede" style="margin: var(--s-6) auto var(--s-8); max-width: 44ch;">Settings has the same support email and a one-tap account deletion if you ever need it.</p>
        <a class="btn btn-primary" href="/admin/login">
            Sign in
            <span class="arrow" aria-hidden="true">→</span>
        </a>
    </div>
</section>

@endsection
