<?php

namespace App\Http\Controllers\Admin\Employees;

use App\Http\Controllers\Controller;
use App\Models\Employees\Salary;
use App\Models\Employees\Advance;
use App\Models\Employees\Bonus;
use Illuminate\Http\Request;
use App\Models\Employee;
use Carbon\Carbon;

class SalaryController extends Controller
{
    public function index()
    {
        $salaries = Salary::with(['employee', 'advances', 'bonuses'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.salaries.index', compact('salaries'));
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
            'effective_date' => 'required|date',
            'status' => 'required|in:active,inactive,pending',
            'notes' => 'nullable|string'
        ]);

        // Deactivate previous salaries for this employee
        if ($request->status === 'active') {
            Salary::where('employee_id', $request->employee_id)
                ->where('status', 'active')
                ->update(['status' => 'inactive']);
        }

        Salary::create($request->all());

        return redirect()->route('salaries.index')
            ->with('success', 'Salary created successfully.');
    }

    public function show(Salary $salary)
    {
        $salary->load(['employee', 'advances', 'bonuses']);
        return view('admin.salaries.show', compact('salary'));
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
            'effective_date' => 'required|date',
            'status' => 'required|in:active,inactive,pending',
            'notes' => 'nullable|string'
        ]);

        // Deactivate previous salaries for this employee if changing to active
        if ($request->status === 'active' && $salary->status !== 'active') {
            Salary::where('employee_id', $request->employee_id)
                ->where('id', '!=', $salary->id)
                ->where('status', 'active')
                ->update(['status' => 'inactive']);
        }

        $salary->update($request->all());

        return redirect()->route('salaries.index')
            ->with('success', 'Salary updated successfully.');
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
    
    // Set default status to 'approved' if not specified
    if (!$request->status) {
        $advance->status = 'approved';
    }
    
    $advance->save();

    return redirect()->route('salaries.show', $salary)
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

        return redirect()->route('salaries.show', $salary)
            ->with('success', 'Advance updated successfully.');
    }

    public function destroyAdvance(Salary $salary, Advance $advance)
    {
        $advance->delete();
        return redirect()->route('salaries.show', $salary)
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
    
    // Set default status to 'approved' if not specified
    if (!$request->status) {
        $bonus->status = 'approved';
    }
    
    $bonus->save();

    return redirect()->route('salaries.show', $salary)
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

        return redirect()->route('salaries.show', $salary)
            ->with('success', 'Bonus updated successfully.');
    }

    public function destroyBonus(Salary $salary, Bonus $bonus)
    {
        $bonus->delete();
        return redirect()->route('salaries.show', $salary)
            ->with('success', 'Bonus deleted successfully.');
    }
}