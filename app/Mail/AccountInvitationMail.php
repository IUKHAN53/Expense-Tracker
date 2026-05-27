<?php

namespace App\Mail;

use App\Models\AccountInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public AccountInvitation $invitation)
    {
        $this->invitation->loadMissing(['account', 'invitedBy']);
    }

    public function envelope(): Envelope
    {
        $by = $this->invitation->invitedBy->name;

        return new Envelope(
            subject: $by." invited you to "
                .$this->invitation->account->name." on Kharcha",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.invitation',
            text: 'mail.invitation-text',
            with: [
                'household'     => $this->invitation->account->name,
                'invitedByName' => $this->invitation->invitedBy->name,
                'inviteUrl'     => $this->invitation->url(),
                'expiresAt'     => $this->invitation->expires_at,
                'supportEmail'  => 'hello@iukhan.tech',
            ],
        );
    }
}
