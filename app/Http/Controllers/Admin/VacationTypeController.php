<?php

// app/Http/Controllers/VacationTypeController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VacationType;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeVacationBalance;
use Illuminate\Support\Facades\DB;
class VacationTypeController extends Controller
{
    public function index()
    {
        $vacationTypes = VacationType::withCount(['vacations', 'employeeVacationBalances'])->get();
        return view('admin.vacation-types.index', compact('vacationTypes'));
    }

    public function create()
    {
        return view('admin.vacation-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:vacation_types,name',
            'is_paid' => 'required|boolean',
            'max_days_allowed' => 'nullable|integer|min:1',
            'auto_assign_balance' => 'nullable|boolean',
            'target_year' => 'nullable|integer|min:' . (now()->year - 1) . '|max:' . (now()->year + 5),
        ]);

        DB::transaction(function () use ($request) {
            // Create vacation type
            $vacationType = VacationType::create([
                'name' => $request->name,
                'is_paid' => $request->is_paid,
                'max_days_allowed' => $request->max_days_allowed,
            ]);

            // Auto-assign balance to all employees if requested
            if ($request->auto_assign_balance && $request->max_days_allowed) {
                $this->assignBalanceToAllEmployees($vacationType, $request->target_year ?? now()->year);
            }
        });

        return redirect()->route('vacation-types.index')
            ->with('success', 'Vacation type created successfully and balances assigned to employees.');
    }

    public function show($id)
    {
        $vacationType = VacationType::with(['vacations.employee', 'employeeVacationBalances.employee'])
            ->withCount(['vacations', 'employeeVacationBalances'])
            ->findOrFail($id);
            
        return view('admin.vacation-types.show', compact('vacationType'));
    }

    public function edit($id)
    {
        $vacationType = VacationType::findOrFail($id);
        return view('admin.vacation-types.edit', compact('vacationType'));
    }

    public function update(Request $request, $id)
    {
        $vacationType = VacationType::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:vacation_types,name,' . $id,
            'is_paid' => 'required|boolean',
            'max_days_allowed' => 'nullable|integer|min:1',
            'update_existing_balances' => 'nullable|boolean',
            'target_year' => 'nullable|integer|min:' . (now()->year - 1) . '|max:' . (now()->year + 5),
        ]);

        DB::transaction(function () use ($request, $vacationType) {
            $oldMaxDays = $vacationType->max_days_allowed;
            
            // Update vacation type
            $vacationType->update([
                'name' => $request->name,
                'is_paid' => $request->is_paid,
                'max_days_allowed' => $request->max_days_allowed,
            ]);

            // Update existing balances if requested and max_days changed
            if ($request->update_existing_balances && 
                $request->max_days_allowed && 
                $oldMaxDays !== $request->max_days_allowed) {
                
                $this->updateExistingBalances($vacationType, $request->target_year ?? now()->year);
            }

            // Create new balances for employees who don't have them
            if ($request->update_existing_balances && $request->max_days_allowed) {
                $this->assignBalanceToEmployeesWithoutBalance($vacationType, $request->target_year ?? now()->year);
            }
        });

        return redirect()->route('vacation-types.index')
            ->with('success', 'Vacation type updated successfully and balances have been updated.');
    }

    public function destroy($id)
    {
        $vacationType = VacationType::findOrFail($id);
        
        // Check if vacation type is being used
        if ($vacationType->vacations()->count() > 0 || $vacationType->employeeVacationBalances()->count() > 0) {
            return redirect()->route('vacation-types.index')
                ->with('error', 'Cannot delete vacation type as it is being used by employees or vacation requests.');
        }

        $vacationType->delete();

        return redirect()->route('vacation-types.index')
            ->with('success', 'Vacation type deleted successfully.');
    }

    /**
     * Assign balance to all employees for this vacation type
     */
    private function assignBalanceToAllEmployees(VacationType $vacationType, int $year)
    {
        $employees = Employee::all();
        $createdCount = 0;

        foreach ($employees as $employee) {
            $existingBalance = EmployeeVacationBalance::where([
                'employee_id' => $employee->id,
                'vacation_type_id' => $vacationType->id,
                'year' => $year,
            ])->first();

            if (!$existingBalance) {
                EmployeeVacationBalance::create([
                    'employee_id' => $employee->id,
                    'vacation_type_id' => $vacationType->id,
                    'balance' => $vacationType->max_days_allowed,
                    'year' => $year,
                ]);
                $createdCount++;
            }
        }

        return $createdCount;
    }

    /**
     * Update existing balances for this vacation type
     */
    private function updateExistingBalances(VacationType $vacationType, int $year)
    {
        EmployeeVacationBalance::where('vacation_type_id', $vacationType->id)
            ->where('year', $year)
            ->update(['balance' => $vacationType->max_days_allowed]);
    }

    /**
     * Assign balance to employees who don't have balance for this vacation type
     */
    private function assignBalanceToEmployeesWithoutBalance(VacationType $vacationType, int $year)
    {
        $employeesWithoutBalance = Employee::whereDoesntHave('vacationBalances', function ($query) use ($vacationType, $year) {
            $query->where('vacation_type_id', $vacationType->id)
                  ->where('year', $year);
        })->get();

        foreach ($employeesWithoutBalance as $employee) {
            EmployeeVacationBalance::create([
                'employee_id' => $employee->id,
                'vacation_type_id' => $vacationType->id,
                'balance' => $vacationType->max_days_allowed,
                'year' => $year,
            ]);
        }
    }

    /**
     * Bulk assign balances to selected employees
     */
    public function bulkAssignBalances(Request $request, $id)
    {
        $vacationType = VacationType::findOrFail($id);
        
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'year' => 'required|integer|min:' . (now()->year - 1) . '|max:' . (now()->year + 5),
            'override_existing' => 'nullable|boolean',
        ]);

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($request->employee_ids as $employeeId) {
            $existingBalance = EmployeeVacationBalance::where([
                'employee_id' => $employeeId,
                'vacation_type_id' => $vacationType->id,
                'year' => $request->year,
            ])->first();

            if ($existingBalance) {
                if ($request->override_existing) {
                    $existingBalance->update(['balance' => $vacationType->max_days_allowed]);
                    $updatedCount++;
                }
            } else {
                EmployeeVacationBalance::create([
                    'employee_id' => $employeeId,
                    'vacation_type_id' => $vacationType->id,
                    'balance' => $vacationType->max_days_allowed,
                    'year' => $request->year,
                ]);
                $createdCount++;
            }
        }

        $message = "Created {$createdCount} new balances";
        if ($updatedCount > 0) {
            $message .= " and updated {$updatedCount} existing balances";
        }
        $message .= ".";

        return redirect()->route('vacation-types.show', $id)
            ->with('success', $message);
    }
}