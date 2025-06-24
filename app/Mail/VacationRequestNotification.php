<?php

namespace App\Mail;

use App\Models\Vacation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VacationRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $vacation;

    public function __construct(Vacation $vacation)
    {
        $this->vacation = $vacation;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Vacation Request - ' . $this->vacation->applicant_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vacation-request-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}