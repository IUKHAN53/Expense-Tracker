{{ $invitedByName }} invited you to join {{ $household }} on Kharcha.

Kharcha is a household expense tracker. Accepting this invitation joins
{{ $invitedByName }}'s household; your expenses live alongside everyone
else's, all in one monthly view.

Accept and join:
{{ $inviteUrl }}

The invitation expires on {{ $expiresAt->format('d M Y, H:i') }}.
If something feels off, write to {{ $supportEmail }}.

_____
Kharcha, a household ledger kept by hand.
expense.iukhan.tech · Karachi, Pakistan

If you do not know {{ $invitedByName }}, you can ignore this email.
The invitation expires on its own.
