<?php

namespace App\Mail;

use App\Models\Deal;
use App\Models\Lead;
use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AdminCommunicationMail extends Mailable
{
    use Queueable;

    public function __construct(
        public string $subjectLine,
        public string $messageBody,
        public ?Lead $lead = null,
        public ?Deal $deal = null,
        public ?Quote $quote = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.communication',
        );
    }
}
