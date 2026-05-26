<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Kharcha',
        );
    }

    public function content(): Content
    {
        $household = $this->user->account?->name ?? ($this->user->name."'s household");

        return new Content(
            view: 'mail.welcome',
            text: 'mail.welcome-text',
            with: [
                'name'      => trim(explode(' ', (string) $this->user->name)[0] ?? $this->user->name),
                'household' => $household,
                'appUrl'    => url('/'),
                'loginUrl'  => url('/admin/login'),
                'deepLink'  => 'kharcha://login',
                'supportEmail' => 'hello@iukhan.tech',
                'unsubscribeNote' => 'You are receiving this because you signed up for Kharcha. This is the only email of its kind; we do not run marketing campaigns.',
            ],
        );
    }
}
