<?php

// app/Mail/VacationRequestSubmitted.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VacationRequestSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $vacation;
    public $requestData;

    /**
     * Create a new message instance.
     */
    public function __construct($vacation, $requestData)
    {
        $this->vacation = $vacation;
        $this->requestData = $requestData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Vacation Request Submitted - Confirmation',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.vacation-request-submitted',
            with: [
                'vacation' => $this->vacation,
                'requestData' => $this->requestData,
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