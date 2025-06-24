<?php

// app/Http/Controllers/EmployeeVacationBalanceController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeVacationBalance;
use App\Models\Employee;
use App\Models\VacationType;
use Illuminate\Http\Request;

class EmployeeVacationBalanceController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeVacationBalance::with(['employee', 'vacationType']);
        
        // Filter by year
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        } else {
            $query->where('year', now()->year);
        }
        
        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        
        // Filter by vacation type
        if ($request->filled('vacation_type_id')) {
            $query->where('vacation_type_id', $request->vacation_type_id);
        }
        
        $balances = $query->get();
        $employees = Employee::all();
        $vacationTypes = VacationType::all();
        $years = range(now()->year - 2, now()->year + 1);
        
        return view('admin.vacation-balances.index', compact('balances', 'employees', 'vacationTypes', 'years'));
    }

    public function create()
    {
        $employees = Employee::all();
        $vacationTypes = VacationType::all();
        $years = range(now()->year, now()->year + 1);
        
        // Create empty balance object for the form
        $balance = (object) [
            'id' => null,
            'employee_id' => old('employee_id'),
            'vacation_type_id' => old('vacation_type_id'),
            'balance' => old('balance', 0),
            'total_days' => old('total_days', 0),
            'used_days' => old('used_days', 0),
            'remaining_days' => old('remaining_days', 0),
            'year' => old('year', now()->year),
        ];
        
        return view('admin.vacation-balances.create', compact('employees', 'vacationTypes', 'years', 'balance'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'vacation_type_id' => 'required|exists:vacation_types,id',
            'balance' => 'required|integer|min:0',
            'total_days' => 'nullable|integer|min:0',
            'used_days' => 'nullable|integer|min:0',
            'year' => 'required|integer|min:' . (now()->year - 5) . '|max:' . (now()->year + 5),
        ]);

        // Check if balance already exists for this combination
        $existingBalance = EmployeeVacationBalance::where([
            'employee_id' => $request->employee_id,
            'vacation_type_id' => $request->vacation_type_id,
            'year' => $request->year,
        ])->first();

        if ($existingBalance) {
            return back()->withErrors(['error' => 'Balance already exists for this employee, vacation type, and year combination.']);
        }

        // Calculate remaining days
        $usedDays = $request->used_days ?? 0;
        $totalDays = $request->total_days ?? $request->balance;
        $remainingDays = $totalDays - $usedDays;

        EmployeeVacationBalance::create([
            'employee_id' => $request->employee_id,
            'vacation_type_id' => $request->vacation_type_id,
            'total_days' => $totalDays,
            'used_days' => $usedDays,
            'remaining_days' => $remainingDays,
            'year' => $request->year,
        ]);

        return redirect()->route('vacation-balances.index')
            ->with('success', 'Vacation balance created successfully.');
    }

    public function show($id)
    {
        $balance = EmployeeVacationBalance::with(['employee', 'vacationType'])->findOrFail($id);
        return view('admin.vacation-balances.show', compact('balance'));
    }

    public function edit($id)
    {
        $balance = EmployeeVacationBalance::with(['employee', 'vacationType'])->findOrFail($id);
        $employees = Employee::all();
        $vacationTypes = VacationType::all();
        $years = range(now()->year - 2, now()->year + 1);
        
        return view('admin.vacation-balances.edit', compact('balance', 'employees', 'vacationTypes', 'years'));
    }

    public function update(Request $request, $id)
    {
        $balance = EmployeeVacationBalance::findOrFail($id);
        
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'vacation_type_id' => 'required|exists:vacation_types,id',
            'balance' => 'required|integer|min:0',
            'total_days' => 'nullable|integer|min:0',
            'used_days' => 'nullable|integer|min:0',
            'year' => 'required|integer|min:' . (now()->year - 5) . '|max:' . (now()->year + 5),
        ]);

        // Check if balance already exists for this combination (excluding current record)
        $existingBalance = EmployeeVacationBalance::where([
            'employee_id' => $request->employee_id,
            'vacation_type_id' => $request->vacation_type_id,
            'year' => $request->year,
        ])->where('id', '!=', $id)->first();

        if ($existingBalance) {
            return back()->withErrors(['error' => 'Balance already exists for this employee, vacation type, and year combination.']);
        }

        // Calculate remaining days
        $usedDays = $request->used_days ?? 0;
        $totalDays = $request->total_days ?? $request->balance;
        $remainingDays = $totalDays - $usedDays;

        $balance->update([
            'employee_id' => $request->employee_id,
            'vacation_type_id' => $request->vacation_type_id,
            'total_days' => $totalDays,
            'used_days' => $usedDays,
            'remaining_days' => $remainingDays,
            'year' => $request->year,
        ]);

        return redirect()->route('vacation-balances.index')
            ->with('success', 'Vacation balance updated successfully.');
    }

    public function destroy($id)
    {
        $balance = EmployeeVacationBalance::findOrFail($id);
        $balance->delete();

        return redirect()->route('vacation-balances.index')
            ->with('success', 'Vacation balance deleted successfully.');
    }

    public function bulkCreate()
    {
        $employees = Employee::all();
        $vacationTypes = VacationType::all();
        $years = range(now()->year, now()->year + 1);
        
        // Create empty balance object for bulk creation
        $balance = (object) [
            'id' => null,
            'employee_id' => null,
            'vacation_type_id' => old('vacation_type_id'),
            'balance' => old('balance', 0),
            'total_days' => old('total_days', 0),
            'used_days' => old('used_days', 0),
            'remaining_days' => old('remaining_days', 0),
            'year' => old('year', now()->year),
        ];
        
        return view('admin.vacation-balances.bulk-create', compact('employees', 'vacationTypes', 'years', 'balance'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'vacation_type_id' => 'required|exists:vacation_types,id',
            'balance' => 'required|integer|min:0',
            'total_days' => 'nullable|integer|min:0',
            'used_days' => 'nullable|integer|min:0',
            'year' => 'required|integer|min:' . (now()->year - 5) . '|max:' . (now()->year + 5),
        ]);

        $created = 0;
        $skipped = 0;
        $usedDays = $request->used_days ?? 0;
        $totalDays = $request->total_days ?? $request->balance;
        $remainingDays = $totalDays - $usedDays;

        foreach ($request->employee_ids as $employeeId) {
            $existingBalance = EmployeeVacationBalance::where([
                'employee_id' => $employeeId,
                'vacation_type_id' => $request->vacation_type_id,
                'year' => $request->year,
            ])->first();

            if (!$existingBalance) {
                EmployeeVacationBalance::create([
                    'employee_id' => $employeeId,
                    'vacation_type_id' => $request->vacation_type_id,
                    'total_days' => $totalDays,
                    'used_days' => $usedDays,
                    'remaining_days' => $remainingDays,
                    'year' => $request->year,
                ]);
                $created++;
            } else {
                $skipped++;
            }
        }

        $message = "Created {$created} vacation balances.";
        if ($skipped > 0) {
            $message .= " Skipped {$skipped} existing balances.";
        }

        return redirect()->route('vacation-balances.index')
            ->with('success', $message);
    }
}