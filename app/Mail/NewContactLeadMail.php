<?php

namespace App\Mail;

use App\Models\ContactLead;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewContactLeadMail extends Mailable
{

    /**
     * Create a new message instance.
     */
    public function __construct(
        public ContactLead $lead
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📩 Nuevo Lead — ' . $this->lead->parent_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new-contact-lead',
        );
    }
}
