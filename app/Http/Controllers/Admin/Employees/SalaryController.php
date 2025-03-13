<?php

namespace App\Http\Controllers\Admin\Employees;

use App\Http\Controllers\Controller;
use App\Models\Employees\Salary;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Employees\TimesheetEmployee;
use App\Models\Employees\HourRate;
use App\Models\Warning; // Make sure this model is imported
use Carbon\Carbon;

class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get current month and year if not provided in the request
        $month = $request->input('month', date('m'));  // Default to current month
        $year = $request->input('year', date('Y'));  // Default to current year

        // Ensure the month is two digits (01, 02, ..., 12)
        $month = str_pad($month, 2, '0', STR_PAD_LEFT);

        // Build the query to filter by month and year
        $query = Salary::with('employee');

        // Apply the filter if month and year are provided
        if ($month && $year) {
            $query->where('month', "{$year}-{$month}");
        }

        // Paginate the results (10 per page)
        $salaries = $query->paginate(15);

        return view('admin.salaries.index', compact('salaries', 'month', 'year'));
    }

    public function updateAllSalaries()
    {
        $employees = Employee::all();
        $updatedCount = 0;

        foreach ($employees as $employee) {
            // Check if there's a deduction warning before updating salary
            $updated = $this->updateEmployeeSalary($employee->id);
            if ($updated) {
                $updatedCount++;
            }
        }

        return redirect()->route('salary.index')->with('success', "{$updatedCount} employee salaries have been updated.");
    }

    private function updateEmployeeSalary($employeeId)
    {
        // Fetch the employee's total hours for the current month
        $totalHours = TimesheetEmployee::where('employee_id', $employeeId)
            ->where('month', Carbon::now()->format('Y-m'))
            ->value('total_hours_month');
        
        // Fetch the employee's hour rate
        $hourRate = HourRate::where('employee_id', $employeeId)
            ->value('hour_rate');
        
        // Check if total hours and hourly rate exist
        if (!$totalHours || !$hourRate) {
            return false;
        }

        // Get any warnings that may have a salary deduction impact
        $warnings = Warning::where('employee_id', $employeeId)
            ->whereMonth('created_at', Carbon::now()->month)
            ->get();

        // Initialize total deduction hours
        $totalDeductionHours = 0;

        // Calculate deduction hours based on warnings
        foreach ($warnings as $warning) {
            // Assuming the warning is related to a deduction (you can add more logic to handle different warning types)
            $totalDeductionHours += $warning->deduct_hours;  // This assumes you have a 'deduct_hours' column in the Warning model
        }

        // If there are deduction hours, subtract them from total hours worked
        if ($totalDeductionHours > 0) {
            list($hours, $minutes, $seconds) = explode(":", $totalHours);
            $totalHoursNumeric = $hours + ($minutes / 60) + ($seconds / 3600);

            // Deduct the total deduction hours from the total hours worked
            $totalHoursNumeric -= $totalDeductionHours;

            // Ensure total hours doesn't go negative
            $totalHoursNumeric = max(0, $totalHoursNumeric);

            // Recalculate the salary based on the new total hours
            $salary = $totalHoursNumeric * $hourRate;
        } else {
            // If no deduction, simply calculate the salary based on the total hours
            list($hours, $minutes, $seconds) = explode(":", $totalHours);
            $totalHoursNumeric = $hours + ($minutes / 60) + ($seconds / 3600);
            $salary = $totalHoursNumeric * $hourRate;
        }

        // Update or create the salary record for this employee
        Salary::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'month' => Carbon::now()->format('Y-m'),
            ],
            [
                'salary' => $salary,
            ]
        );

        return true;
    }

    /**
     * Show the form for editing the specified resource.
     */
    }
