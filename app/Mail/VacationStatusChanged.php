<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VacationStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $vacation;
    public $adminNotes;

    /**
     * Create a new message instance.
     */
    public function __construct($vacation, $adminNotes = null)
    {
        $this->vacation = $vacation;
        $this->adminNotes = $adminNotes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'Vacation Request ' . ucfirst($this->vacation->status);
        
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.vacation-status-changed',
            with: [
                'vacation' => $this->vacation,
                'adminNotes' => $this->adminNotes,
            ],
        );
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