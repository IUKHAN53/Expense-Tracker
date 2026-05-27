<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>You have been invited to {{ $household }}</title>
</head>
<body style="margin:0; padding:0; background:#f6ecd6; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; color:#2b1f12; line-height:1.55; -webkit-font-smoothing:antialiased;">

    <span style="display:none !important; visibility:hidden; opacity:0; color:transparent; height:0; width:0; overflow:hidden;">
        {{ $invitedByName }} added you to {{ $household }} on Kharcha. Accept to join.
    </span>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f6ecd6;">
        <tr>
            <td align="center" style="padding:32px 16px 16px;">
                <table role="presentation" width="560" cellpadding="0" cellspacing="0" border="0" style="max-width:560px;width:100%;">

                    <tr>
                        <td style="padding:0 0 24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td>
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
                                            <td style="padding-right:10px;vertical-align:middle;">
                                                <div style="width:30px;height:30px;background:#2b1f12;border-radius:6px;line-height:30px;text-align:center;color:#fdf8ee;font-family:Georgia,serif;font-style:italic;font-size:18px;">K</div>
                                            </td>
                                            <td style="vertical-align:middle;font-family:Georgia,serif;font-style:italic;font-size:20px;color:#2b1f12;letter-spacing:-0.01em;">kharcha</td>
                                        </tr></table>
                                    </td>
                                    <td align="right" style="font-family:ui-monospace,'SF Mono',Menlo,monospace;font-size:10px;letter-spacing:0.22em;text-transform:uppercase;color:#8a7558;">
                                        Household invitation
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="background:#fffbf2;border:1px solid #e8dcc1;border-radius:4px;padding:40px 36px;">
                            <p style="margin:0 0 8px;font-family:Georgia,serif;font-style:italic;font-size:14px;letter-spacing:0.16em;text-transform:uppercase;color:#a04a13;">
                                {{ $invitedByName }} added you
                            </p>
                            <h1 style="margin:0 0 16px;font-family:Georgia,serif;font-style:italic;font-weight:500;font-size:38px;line-height:1.05;letter-spacing:-0.025em;color:#2b1f12;">
                                Join <em style="color:#c9621f;">{{ $household }}</em>.
                            </h1>
                            <p style="margin:0 0 28px;font-family:Georgia,serif;font-style:italic;font-size:18px;line-height:1.55;color:#6b5b46;">
                                {{ $invitedByName }} keeps a household ledger on Kharcha and wants you in it. Accepting joins their household, where your expenses live alongside everyone else's, all in one monthly view.
                            </p>

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:8px;">
                                <tr>
                                    <td style="background:#2b1f12;border-radius:2px;">
                                        <a href="{{ $inviteUrl }}" style="display:inline-block;padding:14px 24px;color:#fdf8ee;text-decoration:none;font-family:-apple-system,Segoe UI,Roboto,sans-serif;font-size:14px;font-weight:500;letter-spacing:0.02em;">
                                            Accept &amp; join {{ $household }}
                                            <span style="font-family:ui-monospace,'SF Mono',Menlo,monospace;font-size:13px;margin-left:8px;">&rarr;</span>
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:12px 0 0;font-family:ui-monospace,'SF Mono',Menlo,monospace;font-size:11px;letter-spacing:0.16em;text-transform:uppercase;color:#8a7558;">
                                Or paste the link in a browser: <span style="text-transform:none;letter-spacing:0;">{{ $inviteUrl }}</span>
                            </p>

                            <p style="margin:32px 0 0;padding-top:24px;border-top:1px solid #e8dcc1;font-family:Georgia,serif;font-style:italic;font-size:14px;color:#6b5b46;">
                                Heads up: this invitation expires on <strong style="color:#2b1f12;font-weight:500;">{{ $expiresAt->format('d M Y, H:i') }}</strong>. If something feels off, write to <a href="mailto:{{ $supportEmail }}" style="color:#a04a13;text-decoration:underline;">{{ $supportEmail }}</a>.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:24px 4px 32px;">
                            <p style="margin:0 0 8px;font-family:Georgia,serif;font-style:italic;font-size:13px;color:#6b5b46;">
                                Kharcha, a household ledger kept by hand.
                            </p>
                            <p style="margin:0;font-family:ui-monospace,'SF Mono',Menlo,monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:#a89478;">
                                expense.iukhan.tech &middot; Karachi, Pakistan
                            </p>
                            <p style="margin:16px 0 0;font-family:-apple-system,Segoe UI,Roboto,sans-serif;font-size:12px;color:#a89478;line-height:1.55;">
                                If you do not know {{ $invitedByName }}, you can ignore this email. The invitation expires on its own.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
