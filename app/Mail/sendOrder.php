<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class sendOrder extends Mailable
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
                subject: 'Order ontvangen #order-'.$this->order_id.' van mijn.rietpanel.nl',
            );
        } else {
            return new Envelope(
                subject: 'Order ontvangen #order-'.$this->order_id.' van my.rietpanel.com',
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
                view: 'mail.sendOrder',
            );
        } else {
            return new Content(
                view: 'mail.sendOrderEn',
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
            Attachment::fromPath(public_path('/storage/orders/order-'.$this->order_id.'.pdf'))
                ->as('order-'.$this->order_id.'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
