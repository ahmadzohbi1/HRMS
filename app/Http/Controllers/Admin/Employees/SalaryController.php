<?php

namespace App\Http\Controllers\Admin\Employees;

use App\Http\Controllers\Controller;
use App\Models\Employees\Salary;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Employees\TimesheetEmployee;
use App\Models\Employees\HourRate;
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
        $salaries = $query->paginate(10);

        return view('admin.salaries.index', compact('salaries', 'month', 'year'));
    }


    public function updateAllSalaries()
    {
        $employees = Employee::all();
        $updatedCount = 0;

        foreach ($employees as $employee) {
            $updated = $this->updateEmployeeSalary($employee->id);
            if ($updated) {
                $updatedCount++;
            }
        }

        return redirect()->route('salary.index')->with('success', "{$updatedCount} employee salaries have been updated.");
    }

    private function updateEmployeeSalary($employeeId)
    {
        $totalHours = TimesheetEmployee::where('employee_id', $employeeId)
            ->where('month', Carbon::now()->format('Y-m'))
            ->value('total_hours_month');

        $hourRate = HourRate::where('employee_id', $employeeId)
            ->value('hour_rate');

        if (!$totalHours || !$hourRate) {
            return false;
        }

        list($hours, $minutes, $seconds) = explode(":", $totalHours);
        $totalHoursNumeric = $hours + ($minutes / 60) + ($seconds / 3600);
        $salary = $totalHoursNumeric * $hourRate;

        $salaryRecord = Salary::updateOrCreate(
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
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
