<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class sendNewCustomer extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    /**
     * Create a new message instance.
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $locale = config('app.locale'); // leest APP_LOCALE uit .env

        if($locale === 'nl') {


            return new Envelope(
                subject: 'Nieuwe accountaanvraag mijn.rietpanel.nl',
            );
        } else {


            return new Envelope(
                subject: 'Nieuwe accountaanvraag my.rietpanel.com',
            );
        }

    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $locale = config('app.locale'); // leest APP_LOCALE uit .env

        if($locale === 'nl') {

            return new Content(
                view: 'mail.sendNewCustomer',
            );
        } else {

            return new Content(
                view: 'mail.sendNewCustomerEn',
            );
        }

    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
