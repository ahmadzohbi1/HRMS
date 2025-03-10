<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Employees\Salary;
use App\Models\Employees\TimesheetEmployee;
use App\Models\Employees\HourRate;
use App\Models\TimeLog;
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

    // Pass the employee data along with the time logs and id to the view
    return view('admin.employees.timesheet.index', compact('employees_time', 'employee', 'id'));
}

    public function update(Request $request, $id)
    {
        // Retrieve the timelog entry by its ID and employee_id
        $timeLog = TimeLog::where('id', $id)->where('employee_id', $request->employee_id)->first();
    
        if ($timeLog) {
            // Update time log with the new data
            $timeLog->update([
                'date' => $request->date,
                'time_in' => $request->time_in,
                'time_out' => $request->time_out
            ]);
    
            return response()->json(['message' => 'Time Log updated successfully']);
        } else {
            return response()->json(['message' => 'Time Log not found for this employee'], 404);
        }
    }

    public function store(Request $request, $id)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'date' => 'required|date',
            'time_in' => 'required|date_format:H:i',
            'time_out' => 'required|date_format:H:i',
        ]);

        // Log the incoming request data for debugging
        \Log::info('Store TimeLog Request Data:', [
            'employee_id' => $id, // Use the ID from the URL
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

        // Create the time log for the employee with the ID from the URL
        $timeLog = TimeLog::create([
            'employee_id' => $id, // Use the ID from the URL
            'date' => $validatedData['date'],
            'time_in' => $validatedData['time_in'],
            'time_out' => $validatedData['time_out'],
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

        // Debugging log to check if employee_id is received
        \Log::info("Received employee_id: $employeeId");

        if (!$employeeId) {
            return response()->json(['error' => 'Employee ID is required.'], 400);
        }

        $today = Carbon::today()->format('Y-m-d');

        // Check if the employee has already started work today
        $existingLog = TimeLog::where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->first();

        if ($existingLog) {
            return response()->json(['error' => 'You have already started work today.'], 400);
        }

        // Create a new time log
        $timeLog = TimeLog::create([
            'employee_id' => $employeeId,
            'date' => $today,
            'time_in' => Carbon::now()->format('H:i:s'),
            'time_out' => null
        ]);

        return response()->json([
            'message' => 'Work started successfully',
            'time_in' => $timeLog->time_in
        ]);
    }

    public function stopWork(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $today = Carbon::today()->toDateString();

        // Find the time log for today
        $timeLog = TimeLog::where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->first();

        if (!$timeLog || $timeLog->time_out) {
            return response()->json(['error' => 'You have already stopped work today.'], 400);
        }

        // Update the time_out field for today
        $timeLog->update(['time_out' => Carbon::now()->format('H:i:s')]);

        // Fix the function calls
        $this->updateTimesheetEmployee($employeeId, Carbon::now()->month, Carbon::now()->year);
        $this->updateSalary($employeeId);

        return response()->json(['time_out' => $timeLog->time_out]);
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
        $totalHours = TimesheetEmployee::where('employee_id', $employeeId)
            ->where('month', Carbon::now()->format('Y-m'))
            ->value('total_hours_month');

        $hourRate = HourRate::where('employee_id', $employeeId)
            ->value('hour_rate');

        $totalHours = $totalHours ?? '00:00:00';
        $hourRate = $hourRate ?? 0;

        \Log::info("Total Hours: $totalHours");
        \Log::info("Hourly Rate: $hourRate");

        list($hours, $minutes, $seconds) = explode(":", $totalHours);

        $totalHoursNumeric = $hours + ($minutes / 60) + ($seconds / 3600);

        \Log::info("Total Hours Numeric: $totalHoursNumeric");

        $salary = $totalHoursNumeric * $hourRate;

        \Log::info("Calculated Salary: $salary");

        $salaryEmployeeExist = Salary::where('employee_id', $employeeId)
            ->where('month', Carbon::now()->format('Y-m'))
            ->first();

        if ($salaryEmployeeExist) {
            $salaryEmployeeExist->update([
                'salary' => $salary,
            ]);
        } else {
            Salary::create([
                'employee_id' => $employeeId,
                'salary' => $salary,
                'month' => Carbon::now()->format('Y-m'),
            ]);
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