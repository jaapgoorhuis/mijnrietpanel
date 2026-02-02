<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class sendOfferte extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct($offerte)
    {
        $this->offerte_id = $offerte->offerte_id;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $locale = config('app.locale'); // leest APP_LOCALE uit .env

        if($locale === 'nl') {
            return new Envelope(
                subject: 'Offerte ontvangen #offerte-'.$this->offerte_id.' van mijn.rietpanel.nl',
            );
        } else {

            return new Envelope(
                subject: 'Quotation #quotation-'.$this->offerte_id.' van my.rietpanel.com',
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
                view: 'mail.sendOfferte',
            );
        } else {
            return new Content(
                view: 'mail.sendOfferteEn',
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
        return [
            Attachment::fromPath(public_path('/storage/offertes/offerte-'.$this->offerte_id.'.pdf'))
                ->as('offerte-'.$this->offerte_id.'.pdf')
                ->withMime('application/pdf'),

            Attachment::fromPath(public_path('/storage/Riet Panel B.V. Algemene Voorwaarden.pdf'))
                ->as('Riet Panel B.V. Algemene Voorwaarden.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
