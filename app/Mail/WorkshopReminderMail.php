<?php

namespace App\Mail;

use App\Models\Workshop;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkshopReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Workshop $workshop,
        public readonly User $user,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Promemoria: {$this->workshop->title} è domani!",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.workshop-reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
