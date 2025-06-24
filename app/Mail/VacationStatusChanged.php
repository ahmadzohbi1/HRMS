<?php

namespace App\Mail;

use App\Models\Vacation;
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

    public function __construct(Vacation $vacation, $adminNotes = null)
    {
        $this->vacation = $vacation;
        $this->adminNotes = $adminNotes;
    }

    public function envelope(): Envelope
    {
        $status = ucfirst($this->vacation->status);
        
        return new Envelope(
            subject: "Vacation Request {$status} - " . $this->vacation->applicant_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vacation-status-changed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}