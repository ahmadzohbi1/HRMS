<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VacationActionToken;
use App\Models\Vacation;
use App\Mail\VacationStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class VacationActionController extends Controller
{
    /**
     * Handle vacation action via secure token (approve or reject)
     */
    public function handleAction(string $token)
    {
        try {
            // Find the token and validate it
            $actionToken = VacationActionToken::findValidToken($token);

            if (!$actionToken) {
                return $this->showResultPage(
                    'error',
                    'Invalid or Expired Link',
                    'This action link is invalid, has already been used, or has expired. Please check the admin dashboard to manage this vacation request.',
                    null
                );
            }

            // Get the vacation request
            $vacation = $actionToken->vacation;

            // Check if vacation is still pending
            if ($vacation->status !== 'pending') {
                return $this->showResultPage(
                    'warning',
                    'Already Processed',
                    "This vacation request has already been {$vacation->status}.",
                    $vacation
                );
            }

            // Perform the action
            $action = $actionToken->action;
            $vacation->update(['status' => $action === 'approve' ? 'approved' : 'rejected']);

            // Mark token as used
            $actionToken->markAsUsed();

            // If approved, deduct vacation balance
            if ($action === 'approve') {
                $this->deductVacationBalance($vacation);
            }

            // Send notification email to employee
            $this->sendStatusChangeEmail($vacation);

            // Log the action
            Log::info("Vacation request {$action}d via email", [
                'vacation_id' => $vacation->id,
                'employee' => $vacation->applicant_name,
                'action' => $action,
            ]);

            // Show success page
            return $this->showResultPage(
                'success',
                'Action Completed Successfully',
                "The vacation request has been {$action}d successfully. The employee has been notified via email.",
                $vacation
            );

        } catch (\Exception $e) {
            Log::error('Error processing vacation action: ' . $e->getMessage());
            
            return $this->showResultPage(
                'error',
                'Error Processing Request',
                'An error occurred while processing your action. Please try again or use the admin dashboard.',
                null
            );
        }
    }

    /**
     * Deduct vacation balance when approved
     */
    private function deductVacationBalance(Vacation $vacation)
    {
        $employee = $vacation->employee;
        if (!$employee) {
            return;
        }

        $balance = $employee->getVacationBalance(
            $vacation->vacation_type_id,
            $vacation->start_date->year
        );

        if ($balance) {
            $balance->deductBalance($vacation->duration_in_days);
            Log::info("Vacation balance deducted", [
                'employee_id' => $employee->id,
                'days_deducted' => $vacation->duration_in_days,
                'remaining_balance' => $balance->balance,
            ]);
        }
    }

    /**
     * Send status change email to employee
     */
    private function sendStatusChangeEmail(Vacation $vacation)
    {
        try {
            $email = $vacation->applicant_email ?? $vacation->employee?->email;
            
            if ($email) {
                Mail::to($email)->send(new VacationStatusChanged($vacation));
                Log::info("Status change email sent to {$email}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send status change email: " . $e->getMessage());
        }
    }

    /**
     * Show result page with status and message
     */
    private function showResultPage(string $status, string $title, string $message, ?Vacation $vacation)
    {
        return view('emails.vacation-action-result', compact('status', 'title', 'message', 'vacation'));
    }
}
