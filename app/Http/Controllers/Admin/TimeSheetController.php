<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Employees\Salary;
use App\Models\Employees\TimesheetEmployee;
use App\Models\Employees\HourRate;
use App\Models\TimeLog;
use App\Models\Holiday;
use App\Models\Warning;
use App\Models\Employees\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
class TimeSheetController extends Controller
{
    public function show_employee_timesheet($id)
    {
        // Retrieve the employee data
        $employee = Employee::findOrFail($id);

        // Get the employee's time logs for the current year
        $employees_time = TimeLog::where('employee_id', $id)
            ->whereBetween('date', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])
            ->get();

        // Get holidays for the current year
        $holidays = Holiday::whereBetween('date', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])
            ->get()
            ->pluck('date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->toArray();

        // Get approved vacations for the employee for the current year
        $vacations = $employee->vacations()
            ->approved()
            ->whereBetween('start_date', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])
            ->get();

        // Create an array of vacation dates
        $vacation_dates = [];
        foreach ($vacations as $vacation) {
            $start = Carbon::parse($vacation->start_date);
            $end = Carbon::parse($vacation->end_date);

            while ($start <= $end) {
                $vacation_dates[] = $start->format('Y-m-d');
                $start->addDay();
            }
        }

        // Get warnings for the employee for the current year and create array of dates
        $warning_dates = [];
        $warningRecords = Warning::where('employee_id', $id)
            ->whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])
            ->whereIn('warning_title', ['Late Arrival', 'Early Checkout'])
            ->get();

        // For each warning, get the date it was created (assuming warning is created on the same day as the incident)
        foreach ($warningRecords as $warning) {
            $warning_dates[] = Carbon::parse($warning->created_at)->format('Y-m-d');
        }

        // Remove duplicates
        $warning_dates = array_unique($warning_dates);

        // Pass all data to the view
        return view('admin.employees.timesheet.index', compact(
            'employees_time',
            'employee',
            'id',
            'holidays',
            'vacation_dates',
            'warning_dates'
        ));
    }

    public function update(Request $request, $id)
    {
        // Retrieve the timelog entry by its ID
        $timeLog = TimeLog::where('id', $id)->first();

        if ($timeLog) {
            // Prepare data for update - allow null for time_out
            $updateData = [
                'time_in' => $request->time_in,
                'time_out' => $request->time_out ?: null // Set to null if empty
            ];

            // Update time log with the new data
            $timeLog->update($updateData);

            Log::info('Update Request Data:', [
                'time_in' => $request->time_in,
                'time_out' => $request->time_out,
            ]);

            return response()->json(['message' => 'Time Log updated successfully']);
        } else {
            return response()->json(['message' => 'Time Log not found for this employee'], 404);
        }
    }

    public function store(Request $request, $id)
    {
        // Validate the request data - make time_out nullable
        $validatedData = $request->validate([
            'date' => 'required|date',
            'time_in' => 'required|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i', // Changed to nullable
        ]);

        // Log the incoming request data for debugging
        \Log::info('Store TimeLog Request Data:', [
            'employee_id' => $id,
            'date' => $validatedData['date'],
            'time_in' => $validatedData['time_in'],
            'time_out' => $validatedData['time_out'],
        ]);

        // Check if the employee exists using the ID from the URL
        $employee = Employee::find($id);
        if (!$employee) {
            \Log::error('Employee not found for ID: ' . $id);
            return response()->json(['error' => 'Employee not found'], 404);
        }

        // Create the time log - time_out can be null
        $timeLog = TimeLog::create([
            'employee_id' => $id,
            'date' => $validatedData['date'],
            'time_in' => $validatedData['time_in'],
            'time_out' => $validatedData['time_out'] ?: null, // Allow null
        ]);

        // Log the newly created time log for debugging
        \Log::info('TimeLog Created:', [
            'time_log' => $timeLog
        ]);

        return response()->json(['message' => 'Time Log created successfully']);
    }

    public function startWork(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $time_in = Carbon::now(); // Get current time for the employee's time in
        $today = $time_in->format('Y-m-d'); // Get today's date

        if (!$employeeId) {
            Log::error('Employee ID is required.');
            return response()->json(['error' => 'Employee ID is required.'], 400);
        }

        // Check if today is a holiday
        $isHoliday = Holiday::whereDate('date', $today)->exists();

        // Check if employee has approved vacation today
        $employee = Employee::findOrFail($employeeId);
        $hasVacationToday = $employee->vacations()
            ->approved()
            ->where(function ($query) use ($today) {
                $query->whereDate('start_date', '<=', $today)
                    ->whereDate('end_date', '>=', $today);
            })
            ->exists();

        // Check if the employee has already started work today
        $existingLog = TimeLog::where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->first();

        if ($existingLog) {
            Log::info("Employee $employeeId has already started work today.");
            return response()->json(['error' => 'You have already started work today.'], 400);
        }

        // Select the correct shift for the employee
        $employeeShift = Shift::where('employee_id', $employeeId)->first();

        if ($employeeShift) {
            Log::info("Employee $employeeId has a personal shift.", ['shift' => $employeeShift]);
            $shift = $employeeShift; // Use the employee's personal shift
        } else {
            $shift = Shift::where('company_shift', 1)->first(); // Fallback to company shift
            Log::info("Employee $employeeId does not have a personal shift. Using company shift.", ['shift' => $shift]);
        }

        if (!$shift) {
            Log::error("No shift found for employee $employeeId.");
            return response()->json(['error' => 'No shift found.'], 404);
        }

        // Log selected shift details
        Log::info('Selected shift:', ['shift' => $shift]);

        // Get shift rules
        $shiftRules = $shift->shiftRules;

        // Log shift rules
        Log::info('Shift rules:', ['rules' => $shiftRules]);

        // Get shift time_in as Carbon instance
        $shiftTimeIn = Carbon::createFromFormat('H:i:s', $shift->time_in);

        // Log time_in
        Log::info("Employee $employeeId time_in: $time_in");

        // Only check shift rules for late arrival if it's NOT a holiday and employee doesn't have vacation
        if (!$isHoliday && !$hasVacationToday) {
            foreach ($shiftRules as $rule) {
                Log::info("Checking rule: " . $rule->shift_title);

                // Calculate the lateness window based on the rule
                $lateThreshold = $shiftTimeIn->addMinutes($rule->time_in_apply); // Shift time_in + late minutes

                if ($time_in > $lateThreshold) {
                    Log::info("Late arrival detected for employee $employeeId.");

                    if ($rule->deduct_hours) {
                        Log::info("Deducting hours for employee $employeeId based on rule.");
                        // Apply time deduction
                        $this->applyTimeDeduction($employeeId, $rule->day_hours_deduction);
                    } else {
                        Log::info("Issuing warning for employee $employeeId.");
                        // Add a warning if no deduction is applied
                        Warning::create([
                            'employee_id' => $employeeId,
                            'warning_title' => 'Late Arrival',
                            'warning_description' => $rule->warning_description ?: 'Late for shift'
                        ]);
                    }
                }
            }
        } else {
            if ($isHoliday) {
                Log::info("Employee $employeeId is working on a holiday - no warnings applied.");
            }
            if ($hasVacationToday) {
                Log::info("Employee $employeeId has approved vacation today - no warnings applied.");
            }
        }

        // Create the time log
        $timeLog = TimeLog::create([
            'employee_id' => $employeeId,
            'date' => $today,
            'time_in' => $time_in->format('H:i:s'),
            'time_out' => null
        ]);

        Log::info("Time log created for employee $employeeId with time_in: " . $time_in->format('H:i:s'));

        return response()->json([
            'message' => 'Work started successfully',
            'time_in' => $timeLog->time_in
        ]);
    }


    public function stopWork(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $today = Carbon::today()->toDateString();

        // Find today's time log
        $timeLog = TimeLog::where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->first();

        if (!$timeLog || $timeLog->time_out) {
            return response()->json(['error' => 'You have already stopped work today.'], 400);
        }

        // Select the correct shift
        $employee_shift = Shift::where('employee_id', $employeeId)->first();
        $shift = $employee_shift ?: Shift::where('company_shift', 1)->first();

        if (!$shift) {
            return response()->json(['error' => 'No shift found.'], 404);
        }

        // Get shift rules
        $shiftRules = $shift->shiftRules;
        $time_out = Carbon::now()->format('H:i:s');

        // Check for early checkout
        foreach ($shiftRules as $rule) {
            if ($rule->time_out_apply && $time_out < $shift->time_out) {
                if ($rule->deduct_hours) {
                    // Apply time deduction for early checkout
                    Log::info("Deducting hours for employee $employeeId based on early checkout rule.");
                    $this->applyTimeDeduction($employeeId, $rule->day_hours_deduction);
                } else {
                    // Add a warning for early checkout if no deduction is applied
                    Log::info("Issuing warning for early checkout for employee $employeeId.");
                    Warning::create([
                        'employee_id' => $employeeId,
                        'warning_title' => 'Early Checkout',
                        'warning_description' => $rule->warning_description ?: 'Left shift early'
                    ]);
                }
            }
        }

        // Update the time_out field for today
        $timeLog->update(['time_out' => $time_out]);

        // Update timesheet and salary after logging time out
        $this->updateTimesheetEmployee($employeeId, Carbon::now()->month, Carbon::now()->year);
        $this->updateSalary($employeeId);

        return response()->json(['time_out' => $timeLog->time_out]);
    }

    private function applyTimeDeduction($employeeId, $deductedHours)
    {
        // Retrieve the current timesheet entry for the employee
        $timesheet = TimesheetEmployee::where('employee_id', $employeeId)
            ->where('month', Carbon::now()->format('Y-m'))
            ->first();

        if ($timesheet) {
            // Extract current total hours in HH:MM:SS format
            list($hours, $minutes, $seconds) = explode(":", $timesheet->total_hours_month);

            // Convert current hours, minutes, and seconds into a numeric value (hours)
            $totalHoursNumeric = $hours + ($minutes / 60) + ($seconds / 3600);

            // Apply deduction, ensuring it doesn't go below zero
            $newTotalHours = max(0, $totalHoursNumeric - $deductedHours);

            // Convert the updated total hours back to HH:MM:SS format
            $newTotalTime = gmdate("H:i:s", $newTotalHours * 3600);

            // Update the timesheet with the new total hours
            $timesheet->update(['total_hours_month' => $newTotalTime]);

            // Also update the salary after the deduction
            $this->updateSalary($employeeId);
        }
    }

    public function updateAllHours(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        $employees = Employee::all();

        foreach ($employees as $employee) {
            $this->updateTimesheetEmployee($employee->id, $month, $year);
        }

        return redirect()->back()->with('success', 'Total hours updated for all employees.');
    }

    private function updateTimesheetEmployee($employeeId, $month, $year)
    {
        // Get all time logs for the specified month and year
        $timeLogs = TimeLog::where('employee_id', $employeeId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        // Initialize total time in seconds
        $totalSeconds = 0;

        foreach ($timeLogs as $log) {
            if ($log->time_in && $log->time_out) {
                $timeIn = Carbon::parse($log->time_in);
                $timeOut = Carbon::parse($log->time_out);
                // Add the duration in seconds
                $totalSeconds += $timeIn->diffInSeconds($timeOut);
            }
        }

        // Check for any warnings related to time deductions for the employee
        $warnings = Warning::where('employee_id', $employeeId)
            ->where('warning_title', 'Late Arrival')
            ->orWhere('warning_title', 'Early Checkout')
            ->get();

        // Initialize the deduction in hours (in seconds)
        $totalDeductionSeconds = 0;

        foreach ($warnings as $warning) {
            // If the warning involves time deduction, subtract the hours from total time
            if ($warning->warning_title === 'Late Arrival' && $warning->deduct_hours) {
                $totalDeductionSeconds += $warning->day_hours_deduction * 3600; // Convert hours to seconds
            }

            if ($warning->warning_title === 'Early Checkout' && $warning->deduct_hours) {
                $totalDeductionSeconds += $warning->day_hours_deduction * 3600; // Convert hours to seconds
            }
        }

        // Apply deduction to the total time (if any)
        $totalSeconds = max(0, $totalSeconds - $totalDeductionSeconds); // Avoid negative time

        // Convert total seconds to hours, minutes, and seconds
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        // Format as HH:MM:SS
        $totalTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

        // Find or create a timesheet entry for the employee
        $timesheetEmployee = TimesheetEmployee::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'month' => Carbon::createFromDate($year, $month, 1)->format('Y-m'),
            ],
            [
                'total_hours_month' => $totalTime,
            ]
        );

        // Optionally, update salary after updating timesheet
        $this->updateSalary($employeeId);
    }

    public function updateSalary($employeeId)
    {
        // Get total hours for current month from timesheet_employees table
        $currentMonth = Carbon::now()->format('Y-m');

        $timesheetEmployee = TimesheetEmployee::where('employee_id', $employeeId)
            ->where('month', $currentMonth)
            ->first();

        $totalHours = $timesheetEmployee ? $timesheetEmployee->total_hours_month : '00:00:00';

        // Get hourly rate for the employee
        $hourRate = HourRate::where('employee_id', $employeeId)
            ->value('hour_rate');

        $hourRate = $hourRate ?? 0;

        \Log::info("Employee $employeeId - Total Hours: $totalHours, Hourly Rate: $hourRate");

        // Convert time to numeric hours
        list($hours, $minutes, $seconds) = explode(":", $totalHours);
        $totalHoursNumeric = $hours + ($minutes / 60) + ($seconds / 3600);

        $calculatedSalary = $totalHoursNumeric * $hourRate;

        \Log::info("Employee $employeeId - Calculated Salary: $calculatedSalary");

        // Check what columns your Salary table actually has
        // For now, just update the basic salary without month filtering
        $salaryEmployee = Salary::where('employee_id', $employeeId)->first();

        if ($salaryEmployee) {
            $salaryEmployee->update([
                'salary' => $calculatedSalary,
            ]);
            \Log::info("Updated salary for employee $employeeId to $calculatedSalary");
        } else {
            Salary::create([
                'employee_id' => $employeeId,
                'salary' => $calculatedSalary,
            ]);
            \Log::info("Created new salary record for employee $employeeId with salary $calculatedSalary");
        }
    }
    public function getTimeLogs(Request $request)
    {
        $employeeIds = $request->input('employee_ids');
        $today = Carbon::today()->toDateString();

        $timeLogs = TimeLog::whereIn('employee_id', $employeeIds)
            ->whereDate('date', $today)
            ->get()
            ->keyBy('employee_id');

        return response()->json($timeLogs);
    }
    public function verifyPin(Request $request)
    {
        $employee = Employee::find($request->employee_id);

        // Check if the employee exists and the PIN matches
        if ($employee && $employee->pin === $request->pin) {
            return response()->json(['success' => true]);
        }

        // Return an error if the PIN doesn't match
        return response()->json(['success' => false, 'message' => 'Incorrect PIN.'], 400);
    }
}