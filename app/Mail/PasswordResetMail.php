<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $resetUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($user, string $token)
    {
        $this->resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], false));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $locale = config('app.locale'); // leest APP_LOCALE uit .env

        if ($locale === 'nl') {
            return new Envelope(
                subject: 'Wachtwoord resetten - mijn.rietpanel.nl',
            );
        } else {
            return new Envelope(
                subject: 'Reset your password - my.rietpanel.com',
            );
        }
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $locale = config('app.locale'); // leest APP_LOCALE uit .env

        if ($locale === 'nl') {
            return new Content(
                view: 'mail.passwordReset',
            );
        } else {
            return new Content(
                view: 'mail.passwordResetEn',
            );
        }
    }
}
