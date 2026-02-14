<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class sendOrderList extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public $order_id;
    /**
     * Create a new message instance.
     */
    public function __construct($order)
    {
        $this->order_id = $order->order_id;
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     */

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('inkoop@rietpanel.nl', 'Inkoop Rietpanel'),
            subject: 'Nieuwe inkooporder #order-'.$this->order_id.' van mijn.rietpanel.nl',
        );
    }


    public function content(): Content
    {
            return new Content(
                view: 'mail.test',
            );

    }


    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath(public_path('/storage/orderlijst/order-'.$this->order_id.'.pdf'))
                ->as('order-'.$this->order_id.'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
