<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class sendOrderConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    /**
     * Create a new message instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $locale = config('app.locale'); // leest APP_LOCALE uit .env

        if($locale === 'nl') {

            return new Envelope(
                subject: 'Order #'.$this->order->order_id.' bevestigd',
            );
        } else {
            return new Envelope(
                subject: 'Order #'.$this->order->order_id.' confirmed',
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
                view: 'mail.sendOrderConfirmation',
            );
        } else {

            return new Content(
                view: 'mail.sendOrderConfirmationEn',
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
            Attachment::fromPath(public_path('/storage/orders/order-'.$this->order->order_id.'.pdf'))
                ->as('order-'.$this->order->order_id.'.pdf')
                ->withMime('application/pdf'),
            Attachment::fromPath(public_path('/storage/Riet Panel B.V. Algemene Voorwaarden.pdf'))
                ->as('Riet Panel B.V. Algemene Voorwaarden.pdf')
                ->withMime('application/pdf'),
        ];

    }
}
