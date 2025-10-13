<?php

namespace App\Mail;

use App\Models\Vacation;
use App\Models\VacationActionToken;
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
    public $approveToken;
    public $rejectToken;
    public $approveUrl;
    public $rejectUrl;
    public $viewUrl;

    public function __construct(Vacation $vacation)
    {
        $this->vacation = $vacation;
        
        // Generate one-time action tokens
        $this->approveToken = VacationActionToken::generateToken($vacation->id, 'approve');
        $this->rejectToken = VacationActionToken::generateToken($vacation->id, 'reject');
        
        // Create secure URLs with tokens
        $this->approveUrl = url("/api/vacation-action/{$this->approveToken->token}");
        $this->rejectUrl = url("/api/vacation-action/{$this->rejectToken->token}");
        $this->viewUrl = url("/dashboard/vacations/{$vacation->id}");
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
