<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="color-scheme" content="light">
    <title>Welcome to Kharcha</title>
</head>
<body style="margin:0; padding:0; background:#f6ecd6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color:#2b1f12; line-height:1.55; -webkit-font-smoothing:antialiased;">

    <span style="display:none !important; visibility:hidden; opacity:0; color:transparent; height:0; width:0; overflow:hidden;">
        Your household ledger is open. Three first steps inside.
    </span>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f6ecd6;">
        <tr>
            <td align="center" style="padding:32px 16px 16px;">

                <table role="presentation" width="560" cellpadding="0" cellspacing="0" border="0" style="max-width:560px; width:100%;">

                    {{-- Masthead --}}
                    <tr>
                        <td style="padding:0 0 24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="vertical-align:middle;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
                                            <td style="padding-right:10px; vertical-align:middle;">
                                                <img src="{{ url('/og.svg') }}" alt="" width="0" height="0" style="display:none;">
                                                <!-- Inline SVG mark for clients that allow it; falls back to nothing in plain clients -->
                                                <div style="width:30px; height:30px; background:#2b1f12; border-radius:6px; line-height:30px; text-align:center; color:#fdf8ee; font-family:Georgia,serif; font-style:italic; font-size:18px;">K</div>
                                            </td>
                                            <td style="vertical-align:middle; font-family:Georgia,serif; font-style:italic; font-size:20px; color:#2b1f12; letter-spacing:-0.01em;">
                                                kharcha
                                            </td>
                                        </tr></table>
                                    </td>
                                    <td align="right" style="font-family: ui-monospace, 'SF Mono', Menlo, monospace; font-size:10px; letter-spacing:0.22em; text-transform:uppercase; color:#8a7558;">
                                        A small welcome
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Paper card --}}
                    <tr>
                        <td style="background:#fffbf2; border:1px solid #e8dcc1; border-radius:4px; padding:40px 36px;">

                            {{-- Display headline --}}
                            <p style="margin:0 0 8px; font-family:Georgia,serif; font-style:italic; font-size:14px; letter-spacing:0.16em; text-transform:uppercase; color:#a04a13;">
                                Hello, {{ $name }}.
                            </p>
                            <h1 style="margin:0 0 16px; font-family:Georgia,serif; font-style:italic; font-weight:500; font-size:42px; line-height:1.05; letter-spacing:-0.025em; color:#2b1f12;">
                                You're in<span style="color:#c9621f;">.</span>
                            </h1>
                            <p style="margin:0 0 28px; font-family:Georgia,serif; font-style:italic; font-size:18px; line-height:1.55; color:#6b5b46;">
                                Your household, <em style="color:#a04a13;">{{ $household }}</em>, has a fresh ledger waiting. Three small things to try first.
                            </p>

                            {{-- Steps --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:32px;">
                                <tr>
                                    <td style="padding:14px 0; border-top:1px dashed #e8dcc1;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
                                            <td valign="top" width="36" style="font-family:ui-monospace,'SF Mono',Menlo,monospace; font-size:11px; letter-spacing:0.14em; color:#c9621f;">01</td>
                                            <td valign="top" style="font-family:-apple-system,Segoe UI,Roboto,sans-serif; font-size:15px; color:#2b1f12; line-height:1.5;">
                                                <strong style="font-weight:500;">Add the people in your household.</strong><br>
                                                <span style="color:#6b5b46;">Up to five names. Each gets their own ledger; Home and Car are always there too.</span>
                                            </td>
                                        </tr></table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 0; border-top:1px dashed #e8dcc1;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
                                            <td valign="top" width="36" style="font-family:ui-monospace,'SF Mono',Menlo,monospace; font-size:11px; letter-spacing:0.14em; color:#c9621f;">02</td>
                                            <td valign="top" style="font-family:-apple-system,Segoe UI,Roboto,sans-serif; font-size:15px; color:#2b1f12; line-height:1.5;">
                                                <strong style="font-weight:500;">Scan your first receipt.</strong><br>
                                                <span style="color:#6b5b46;">Open the Android app, tap Scan, point at any printed receipt. Three scans a month are free.</span>
                                            </td>
                                        </tr></table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 0; border-top:1px dashed #e8dcc1; border-bottom:1px dashed #e8dcc1;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
                                            <td valign="top" width="36" style="font-family:ui-monospace,'SF Mono',Menlo,monospace; font-size:11px; letter-spacing:0.14em; color:#c9621f;">03</td>
                                            <td valign="top" style="font-family:-apple-system,Segoe UI,Roboto,sans-serif; font-size:15px; color:#2b1f12; line-height:1.5;">
                                                <strong style="font-weight:500;">Log a fuel refill.</strong><br>
                                                <span style="color:#6b5b46;">Rupees and rate; Kharcha works out the litres and the per-km cost.</span>
                                            </td>
                                        </tr></table>
                                    </td>
                                </tr>
                            </table>

                            {{-- CTA --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:8px;">
                                <tr>
                                    <td style="background:#2b1f12; border-radius:2px;">
                                        <a href="{{ $loginUrl }}" style="display:inline-block; padding:14px 24px; color:#fdf8ee; text-decoration:none; font-family:-apple-system,Segoe UI,Roboto,sans-serif; font-size:14px; font-weight:500; letter-spacing:0.02em;">
                                            Open Kharcha
                                            <span style="font-family:ui-monospace,'SF Mono',Menlo,monospace; font-size:13px; margin-left:8px;">&rarr;</span>
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:12px 0 0; font-family:ui-monospace,'SF Mono',Menlo,monospace; font-size:11px; letter-spacing:0.16em; text-transform:uppercase; color:#8a7558;">
                                On Android: tap <a href="{{ $deepLink }}" style="color:#a04a13; text-decoration:underline;">{{ $deepLink }}</a> from your phone.
                            </p>

                            {{-- Colophon --}}
                            <p style="margin:32px 0 0; padding-top:24px; border-top:1px solid #e8dcc1; font-family:Georgia,serif; font-style:italic; font-size:14px; color:#6b5b46;">
                                Questions, edge cases, or anything that feels off, write to <a href="mailto:{{ $supportEmail }}" style="color:#a04a13; text-decoration:underline;">{{ $supportEmail }}</a>. A real person reads every email.
                            </p>

                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:24px 4px 32px;">
                            <p style="margin:0 0 8px; font-family:Georgia,serif; font-style:italic; font-size:13px; color:#6b5b46;">
                                Kharcha, a household ledger kept by hand.
                            </p>
                            <p style="margin:0; font-family:ui-monospace,'SF Mono',Menlo,monospace; font-size:10px; letter-spacing:0.18em; text-transform:uppercase; color:#a89478;">
                                {{ $appUrl }} · Karachi, Pakistan
                            </p>
                            <p style="margin:16px 0 0; font-family:-apple-system,Segoe UI,Roboto,sans-serif; font-size:12px; color:#a89478; line-height:1.55;">
                                {{ $unsubscribeNote }}
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>
</body>
</html>
