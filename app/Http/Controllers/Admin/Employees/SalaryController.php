<?php

namespace App\Http\Controllers\Admin\Employees;

use App\Http\Controllers\Controller;
use App\Models\Employees\Salary;
use App\Models\Employees\Advance;
use App\Models\Employees\Bonus;
use App\Models\Employees\Deduction;
use Illuminate\Http\Request;
use App\Models\Employee;
use Carbon\Carbon;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        // Get the selected month or default to current month
        $selectedMonth = $request->get('month', Carbon::now()->format('Y-m'));
        $currentDate = Carbon::createFromFormat('Y-m', $selectedMonth);
        
        // Get unique employees with salaries
        $employees = Employee::whereHas('salaries')->get();
        
        // For each employee, get the applicable salary for the selected month
        $salaries = collect();
        foreach ($employees as $employee) {
            $applicableSalary = Salary::getSalaryForMonth($employee->id, $currentDate->year, $currentDate->month);
            if ($applicableSalary) {
                $salaries->push($applicableSalary);
            }
        }
        
        // Calculate previous and next months for navigation
        $previousMonth = $currentDate->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentDate->copy()->addMonth()->format('Y-m');
        
        return view('admin.salaries.index', compact('salaries', 'selectedMonth', 'previousMonth', 'nextMonth', 'currentDate'));
    }

    public function create()
    {
        $employees = Employee::whereDoesntHave('salaries', function($query) {
            $query->where('status', 'active');
        })->get();
        
        return view('admin.salaries.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'fixed_salary' => 'required|numeric|min:0',
            'effective_month' => 'required|date_format:Y-m',
            'status' => 'required|in:active,inactive,pending',
            'notes' => 'nullable|string'
        ]);

        // Convert month-year to first day of month
        $effectiveDate = Carbon::createFromFormat('Y-m', $request->effective_month)->startOfMonth();

        // Deactivate previous salaries for this employee
        if ($request->status === 'active') {
            Salary::where('employee_id', $request->employee_id)
                ->where('status', 'active')
                ->update(['status' => 'inactive']);
        }

        Salary::create([
            'employee_id' => $request->employee_id,
            'fixed_salary' => $request->fixed_salary,
            'effective_date' => $effectiveDate,
            'status' => $request->status,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('salaries.index')
            ->with('success', 'Salary created successfully for ' . $effectiveDate->format('F Y'));
    }

    public function show(Salary $salary, Request $request)
    {
        // Get the selected month or default to current month
        $selectedMonth = $request->get('month', Carbon::now()->format('Y-m'));
        $currentDate = Carbon::createFromFormat('Y-m', $selectedMonth);
        
        // Load salary with employee and filtered advances/bonuses/deductions for the selected month
        $salary->load([
            'employee',
            'advances' => function($query) use ($currentDate) {
                $query->whereMonth('advance_date', $currentDate->month)
                      ->whereYear('advance_date', $currentDate->year)
                      ->orderBy('advance_date', 'desc');
            },
            'bonuses' => function($query) use ($currentDate) {
                $query->whereMonth('bonus_date', $currentDate->month)
                      ->whereYear('bonus_date', $currentDate->year)
                      ->orderBy('bonus_date', 'desc');
            },
            'deductions' => function($query) use ($currentDate) {
                $query->whereMonth('deduction_date', $currentDate->month)
                      ->whereYear('deduction_date', $currentDate->year)
                      ->orderBy('deduction_date', 'desc');
            }
        ]);
        
        // Calculate previous and next months for navigation
        $previousMonth = $currentDate->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentDate->copy()->addMonth()->format('Y-m');
        
        // Get salary breakdown for the month
        $breakdown = $salary->getSalaryBreakdownForMonth($currentDate->year, $currentDate->month);
        
        return view('admin.salaries.show', compact('salary', 'selectedMonth', 'previousMonth', 'nextMonth', 'currentDate', 'breakdown'));
    }

    public function edit(Salary $salary)
    {
        $employees = Employee::all();
        return view('admin.salaries.edit', compact('salary', 'employees'));
    }

    public function update(Request $request, Salary $salary)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'fixed_salary' => 'required|numeric|min:0',
            'effective_month' => 'required|date_format:Y-m',
            'status' => 'required|in:active,inactive,pending',
            'notes' => 'nullable|string'
        ]);

        // Convert month-year to first day of month
        $newEffectiveDate = Carbon::createFromFormat('Y-m', $request->effective_month)->startOfMonth();
        $currentDate = Carbon::now();
        $originalEffectiveDate = $salary->effective_date;

        // If the new effective date is in the future (after current month)
        if ($newEffectiveDate->isAfter($currentDate->endOfMonth())) {
            
            // Create new salary version for future dates
            $newSalary = Salary::createNewVersion($request->employee_id, [
                'fixed_salary' => $request->fixed_salary,
                'effective_date' => $newEffectiveDate,
                'status' => $request->status,
                'notes' => $request->notes,
                'created_by' => auth()->id() // Optional: track who made the change
            ]);

            return redirect()->route('salaries.index')
                ->with('success', 'New salary version created effective ' . $newEffectiveDate->format('F Y') . '. Previous salary history preserved.');
        } 
        // If the effective date is moving forward (but not necessarily future)
        elseif ($newEffectiveDate->isAfter($originalEffectiveDate)) {
            
            // Create new salary version
            $newSalary = Salary::createNewVersion($request->employee_id, [
                'fixed_salary' => $request->fixed_salary,
                'effective_date' => $newEffectiveDate,
                'status' => $request->status,
                'notes' => $request->notes,
                'created_by' => auth()->id()
            ]);

            return redirect()->route('salaries.index')
                ->with('success', 'New salary version created effective ' . $newEffectiveDate->format('F Y') . '. Historical data preserved.');
        }
        else {
            // For same month updates or past month corrections, update the existing record
            $salary->update([
                'employee_id' => $request->employee_id,
                'fixed_salary' => $request->fixed_salary,
                'effective_date' => $newEffectiveDate,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            return redirect()->route('salaries.index')
                ->with('success', 'Salary record updated successfully.');
        }
    }

    public function destroy(Salary $salary)
    {
        $salary->delete();
        return redirect()->route('salaries.index')
            ->with('success', 'Salary deleted successfully.');
    }

    // Advance Methods
    public function createAdvance(Salary $salary)
    {
        return view('admin.salaries.advances.create', compact('salary'));
    }

    public function storeAdvance(Request $request, Salary $salary)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'advance_date' => 'required|date',
            'reason' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected,paid,completed',
            'deduction_start_date' => 'nullable|date',
            'installments' => 'required|integer|min:1'
        ]);

        $advance = new Advance($request->all());
        $advance->salary_id = $salary->id;
        $advance->remaining_amount = $request->amount;
        
        if (!$request->status) {
            $advance->status = 'approved';
        }
        
        $advance->save();

        $currentMonth = Carbon::parse($request->advance_date)->format('Y-m');
        
        return redirect()->route('salaries.show', ['salary' => $salary, 'month' => $currentMonth])
            ->with('success', 'Advance created successfully.');
    }

    public function editAdvance(Salary $salary, Advance $advance)
    {
        return view('admin.salaries.advances.edit', compact('salary', 'advance'));
    }

    public function updateAdvance(Request $request, Salary $salary, Advance $advance)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'advance_date' => 'required|date',
            'reason' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected,paid,completed',
            'deduction_start_date' => 'nullable|date',
            'installments' => 'required|integer|min:1',
            'remaining_amount' => 'nullable|numeric|min:0'
        ]);

        $advance->update($request->all());

        $currentMonth = Carbon::parse($request->advance_date)->format('Y-m');

        return redirect()->route('salaries.show', ['salary' => $salary, 'month' => $currentMonth])
            ->with('success', 'Advance updated successfully.');
    }

    public function destroyAdvance(Salary $salary, Advance $advance)
    {
        $currentMonth = $advance->advance_date->format('Y-m');
        $advance->delete();
        
        return redirect()->route('salaries.show', ['salary' => $salary, 'month' => $currentMonth])
            ->with('success', 'Advance deleted successfully.');
    }

    // Bonus Methods
    public function createBonus(Salary $salary)
    {
        return view('admin.salaries.bonuses.create', compact('salary'));
    }

    public function storeBonus(Request $request, Salary $salary)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'bonus_date' => 'required|date',
            'type' => 'required|in:performance,annual,project,attendance,special,other',
            'reason' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected,paid'
        ]);

        $bonus = new Bonus($request->all());
        $bonus->salary_id = $salary->id;
        
        if (!$request->status) {
            $bonus->status = 'approved';
        }
        
        $bonus->save();

        $currentMonth = Carbon::parse($request->bonus_date)->format('Y-m');

        return redirect()->route('salaries.show', ['salary' => $salary, 'month' => $currentMonth])
            ->with('success', 'Bonus created successfully.');
    }

    public function editBonus(Salary $salary, Bonus $bonus)
    {
        return view('admin.salaries.bonuses.edit', compact('salary', 'bonus'));
    }

    public function updateBonus(Request $request, Salary $salary, Bonus $bonus)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'bonus_date' => 'required|date',
            'type' => 'required|in:performance,annual,project,attendance,special,other',
            'reason' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected,paid'
        ]);

        $bonus->update($request->all());

        $currentMonth = Carbon::parse($request->bonus_date)->format('Y-m');

        return redirect()->route('salaries.show', ['salary' => $salary, 'month' => $currentMonth])
            ->with('success', 'Bonus updated successfully.');
    }

    public function destroyBonus(Salary $salary, Bonus $bonus)
    {
        $currentMonth = $bonus->bonus_date->format('Y-m');
        $bonus->delete();
        
        return redirect()->route('salaries.show', ['salary' => $salary, 'month' => $currentMonth])
            ->with('success', 'Bonus deleted successfully.');
    }

    // Deduction Methods
    public function createDeduction(Salary $salary)
    {
        return view('admin.salaries.deductions.create', compact('salary'));
    }

    public function storeDeduction(Request $request, Salary $salary)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'deduction_date' => 'required|date',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,approved,deducted'
        ]);

        $deduction = new Deduction($request->all());
        $deduction->salary_id = $salary->id;
        
        if (!$request->status) {
            $deduction->status = 'approved';
        }
        
        $deduction->save();

        $currentMonth = Carbon::parse($request->deduction_date)->format('Y-m');

        return redirect()->route('salaries.show', ['salary' => $salary, 'month' => $currentMonth])
            ->with('success', 'Deduction created successfully.');
    }

    public function editDeduction(Salary $salary, Deduction $deduction)
    {
        return view('admin.salaries.deductions.edit', compact('salary', 'deduction'));
    }

    public function updateDeduction(Request $request, Salary $salary, Deduction $deduction)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'deduction_date' => 'required|date',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,approved,deducted'
        ]);

        $deduction->update($request->all());

        $currentMonth = Carbon::parse($request->deduction_date)->format('Y-m');

        return redirect()->route('salaries.show', ['salary' => $salary, 'month' => $currentMonth])
            ->with('success', 'Deduction updated successfully.');
    }

    public function destroyDeduction(Salary $salary, Deduction $deduction)
    {
        $currentMonth = $deduction->deduction_date->format('Y-m');
        $deduction->delete();
        
        return redirect()->route('salaries.show', ['salary' => $salary, 'month' => $currentMonth])
            ->with('success', 'Deduction deleted successfully.');
    }

    /**
     * Generate monthly salary report for an employee
     */
    public function monthlyReport(Salary $salary, Request $request)
    {
        $selectedMonth = $request->get('month', Carbon::now()->format('Y-m'));
        $currentDate = Carbon::createFromFormat('Y-m', $selectedMonth);
        $year = $currentDate->year;
        $month = $currentDate->month;
        
        // Get salary breakdown
        $breakdown = $salary->getSalaryBreakdownForMonth($year, $month);
        
        // Get time logs for the month
        $timeLogs = $salary->employee->timeLog()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get();
        
        return view('admin.salaries.reports.monthly', compact('salary', 'breakdown', 'timeLogs', 'currentDate'));
    }

    /**
     * Generate yearly salary report for an employee
     */
    public function yearlyReport(Salary $salary, Request $request)
    {
        $selectedYear = $request->get('year', Carbon::now()->year);
        
        // Get all salary versions for this employee in the year
        $salaryVersions = Salary::where('employee_id', $salary->employee_id)
            ->whereYear('effective_date', '<=', $selectedYear)
            ->where(function($query) use ($selectedYear) {
                $query->whereYear('end_date', '>=', $selectedYear)
                      ->orWhereNull('end_date');
            })
            ->orderBy('effective_date')
            ->get();
        
        // Get monthly breakdown for each month
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $applicableSalary = Salary::getSalaryForMonth($salary->employee_id, $selectedYear, $month);
            
            if ($applicableSalary) {
                $monthlyData[$month] = $applicableSalary->getSalaryBreakdownForMonth($selectedYear, $month);
                $monthlyData[$month]['salary_version'] = $applicableSalary->version;
                $monthlyData[$month]['effective_date'] = $applicableSalary->effective_date;
            }
        }
        
        return view('admin.salaries.reports.yearly', compact('salary', 'salaryVersions', 'monthlyData', 'selectedYear'));
    }
}
