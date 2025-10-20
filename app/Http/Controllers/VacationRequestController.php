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

        // Get employee from database
        $employee = Employee::find($validated['employee_id']);
        if (!$employee) {
            return redirect()->back()->withErrors(['employee_id' => 'Employee not found.']);
        }

        // Check if employee already submitted a request today (limit: 1 request per day)
        $todayRequestCount = Vacation::where('employee_id', $validated['employee_id'])
            ->whereDate('created_at', Carbon::today())
            ->count();

        if ($todayRequestCount >= 1) {
            return redirect()->back()
                ->withErrors(['employee_id' => 'This employee has already submitted a vacation request today. Only 1 request per day is allowed.'])
                ->withInput();
        }

        // Use employee's email from database, fallback to applicant_email if not available
        $employeeEmail = $employee->email ?? $validated['applicant_email'];

        // Create the vacation request with status 'pending'
        $vacation = Vacation::create([
            'employee_id' => $validated['employee_id'],
            'vacation_type_id' => $validated['vacation_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'pending', // Always starts as pending
            'applicant_name' => $validated['applicant_name'],
            'applicant_email' => $employeeEmail, // Use employee email from database
            'applicant_phone' => $validated['applicant_phone'],
            'reason' => $validated['reason'],
        ]);

        // Load relationships for email
        $vacation->load(['employee', 'vacationType']);

        // Send confirmation email to employee
        try {
            Mail::to($employeeEmail)
                ->send(new VacationRequestSubmitted($vacation));
            \Log::info("Confirmation email sent to employee: {$employeeEmail}");
        } catch (\Exception $e) {
            // Log the error but don't fail the request
            \Log::error('Failed to send employee email: ' . $e->getMessage());
        }

        // Send notification email to admin with action buttons
        try {
            $adminEmail = config('mail.admin_email', 'shafik.abdulrahman@gmail.com');
            Mail::to($adminEmail)
                ->send(new VacationRequestNotification($vacation));
            \Log::info("Admin notification sent to: {$adminEmail}");
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