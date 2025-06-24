<?php

namespace App\Http\Controllers;

use App\Models\Vacation;
use App\Models\Employee;
use App\Models\VacationType;
use App\Models\EmployeeVacationBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\VacationRequestSubmitted;
use App\Mail\VacationRequestNotification;
use Carbon\Carbon;

class VacationRequestController extends Controller
{
    public function index()
    {
        $vacations = Vacation::with(['employee', 'vacationType'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        
        // Get employees and vacation types for the form
        $employees = Employee::select('id', 'name', 'email', 'phone', 'pin')
                           ->orderBy('name')
                           ->get();
        
        $vacationTypes = VacationType::orderBy('name')->get();

        return view('default.vacation-request', compact('vacations', 'employees', 'vacationTypes'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'vacation_type_id' => 'required|exists:vacation_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'applicant_name' => 'required|string|max:255',
            'applicant_email' => 'required|email|max:255',
            'applicant_phone' => 'required|string|max:20',
            'reason' => 'nullable|string|max:1000',
        ]);

        // Verify PIN (additional security check)
        $employee = Employee::find($validated['employee_id']);
        if (!$employee) {
            return redirect()->back()->withErrors(['employee_id' => 'Employee not found.']);
        }

        // Create the vacation request
        $vacation = Vacation::create([
            'employee_id' => $validated['employee_id'],
            'vacation_type_id' => $validated['vacation_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'pending',
            'applicant_name' => $validated['applicant_name'],
            'applicant_email' => $validated['applicant_email'],
            'applicant_phone' => $validated['applicant_phone'],
            'reason' => $validated['reason'],
        ]);

        // Load relationships for email
        $vacation->load(['employee', 'vacationType']);

        // Send email to employee
        try {
            Mail::to($validated['applicant_email'])
                ->send(new VacationRequestSubmitted($vacation));
        } catch (\Exception $e) {
            // Log the error but don't fail the request
            \Log::error('Failed to send employee email: ' . $e->getMessage());
        }

        // Send email to admin (you can configure admin email in config/mail.php)
        try {
            $adminEmail = config('mail.admin_email', 'admin@yourcompany.com');
            Mail::to($adminEmail)
                ->send(new VacationRequestNotification($vacation));
        } catch (\Exception $e) {
            // Log the error but don't fail the request
            \Log::error('Failed to send admin email: ' . $e->getMessage());
        }

        return redirect()->route('vacation-request.index')
                       ->with('success', 'Vacation request submitted successfully! You will receive a confirmation email shortly.');
    }

    public function getVacationBalance($employeeId, $vacationTypeId)
    {
        $currentYear = now()->year;
        $employee = Employee::find($employeeId);
        $vacationType = VacationType::find($vacationTypeId);

        if (!$employee || !$vacationType) {
            return response()->json(['error' => 'Employee or vacation type not found'], 404);
        }

        // Get vacation balance for current year
        $balance = $employee->getVacationBalance($vacationTypeId, $currentYear);
        
        // Get used days for current year
        $usedDays = $employee->getTotalVacationDaysUsed($vacationTypeId, $currentYear);

        $totalDays = null;
        $remainingDays = null;

        if ($balance) {
            $totalDays = $balance->total_days;
            $remainingDays = $balance->remaining_days;
        } elseif ($vacationType->max_days_allowed) {
            $totalDays = $vacationType->max_days_allowed;
            $remainingDays = $totalDays - $usedDays;
        }

        return response()->json([
            'total_days' => $totalDays,
            'used_days' => $usedDays,
            'remaining_days' => $remainingDays,
            'is_unlimited' => $vacationType->isUnlimited(),
        ]);
    }
}