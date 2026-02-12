<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class sendOfferteToOrder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct($order)
    {
        $this->order_id = $order->order_id;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {

        $locale = config('app.locale'); // leest APP_LOCALE uit .env

        if($locale === 'nl') {
            return new Envelope(
                subject: 'Offerte omgezet tot #order-'.$this->order_id.' op mijn.rietpanel.nl',
            );
        } else {
            return new Envelope(
                subject: 'Quote converted to order #order-'.$this->order_id.' on my.rietpanel.com',
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
                view: 'mail.offerteToOrder',
            );
        } else {
            return new Content(
                view: 'mail.offerteToOrderEn',
            );
        }


    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */

}
