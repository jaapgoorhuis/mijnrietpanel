<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class sendOrderConfirmedByRietpanel extends Mailable
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


        if($this->order->lang === 'nl') {

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
        return new Content(view: 'mail.sendOrderConfirmationByRietpanel');

    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
            return [
                Attachment::fromPath(public_path('/storage/orders/order-' . $this->order->order_id . '.pdf'))
                    ->as('orderbevestiging-' . $this->order->order_id . '.pdf')
                    ->withMime('application/pdf'),
            ];
    }
}
