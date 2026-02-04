<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class sendOrderListConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public $order_id;

    public $suplier_email;
    /**
     * Create a new message instance.
     */
    public function __construct($order, $suplier_email)
    {
        $this->order_id = $order->order_id;
        $this->order = $order;
        $this->suplier_email = $suplier_email;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kopie inkooporder #order-'.$this->order_id.' van mijn.rietpanel.nl',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.sendInkoopOrderKopie',
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
