<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Lead $lead)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Lead: ' . $this->lead->first_name . ' ' . $this->lead->last_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lead-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
