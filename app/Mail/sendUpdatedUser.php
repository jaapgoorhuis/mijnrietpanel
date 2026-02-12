<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class sendUpdatedUser extends Mailable
{
    use Queueable, SerializesModels;

    public $status;
    public $lang;

    /**
     * Create a new message instance.
     */
    public function __construct($status,$lang)
    {
        $this->lang = $lang;
        $this->status = $status;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {

        if($this->lang == 'nl') {
        return new Envelope(
            subject: 'Uw account op mijn.rietpanel.nl is geupdate',
        );} else {
            return new Envelope(
                subject: 'Your account on my.rietpanel.com has been updated!',
            );
        }
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        if($this->lang == 'nl') {
        return new Content(
            view: 'mail.sendUpdatedUser',
        );
        } else {
            return new Content(
                view: 'mail.sendUpdatedUserEn',
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
