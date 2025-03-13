<?php

namespace App\Http\Controllers\Admin\Employees;

use App\Models\Employees\Shift;
use App\Models\ShiftRule;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::all();
        return view('admin.shifts.index', compact('shifts'));
    }
    public function create()
    {
        $departments = Department::all();
        $employees = Employee::all();
        return view('admin.shifts.create', compact('departments', 'employees'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'shift_name' => 'required|string|max:255',
            'time_in' => 'required|date_format:H:i',
            'time_out' => 'required|date_format:H:i',
            'department_id' => 'nullable|exists:departments,id',
            'employee_id' => 'nullable|exists:employees,id',
        ]);
        $shift = new Shift([
            'shift_name' => $request->shift_name,
            'time_in' => $request->time_in,
            'time_out' => $request->time_out,
            'shift_type' => $request->shift_type,
        ]);

        if ($request->shift_type == 'department') {
            $shift->department_id = $request->department_id;
        }
        if ($request->shift_type == 'employee') {
            $shift->employee_id = $request->employee_id;
        }
        if ($request->shift_type == 'company') {
            $shift->company_shift = true;
        }

        $shift->save();

        return redirect()->route('shifts.index')->with('success', 'Shift created successfully!');
    }

    public function edit($shiftId)
    {
        $shift = Shift::findOrFail($shiftId);
        $departments = Department::all();
        $employees = Employee::all();
        $shiftRules = ShiftRule::all(); // Get all shift rules

        return view('admin.shifts.edit', compact('shift', 'departments', 'employees', 'shiftRules'));
    }
    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'shift_name' => 'required|string|max:255',
            'time_in' => 'required|date_format:H:i',
            'time_out' => 'required|date_format:H:i',
            'shift_type' => 'required|in:company,department,employee',
            'department_id' => 'nullable|required_if:shift_type,department|exists:departments,id',
            'employee_id' => 'nullable|required_if:shift_type,employee|exists:employees,id',
            'shift_rules' => 'nullable|array',
            'shift_rules.*' => 'exists:shift_rules,id'
        ]);

        $shift->update([
            'shift_name' => $request->shift_name,
            'time_in' => $request->time_in,
            'time_out' => $request->time_out,
            'shift_type' => $request->shift_type,
            'department_id' => $request->shift_type == 'department' ? $request->department_id : null,
            'employee_id' => $request->shift_type == 'employee' ? $request->employee_id : null,
            'company_shift' => $request->shift_type == 'company',
        ]);

        // Sync shift rules (update pivot table)
        $shift->shiftRules()->sync($request->shift_rules ?? []);

        return redirect()->route('shifts.index')->with('success', 'Shift updated successfully!');
    }


    public function destroy($id)
    {
        $shift = Shift::findOrFail($id);
        $shift->delete();
        return redirect()->route('shifts.index')->with('success', 'Shift deleted successfully.');
    }



}
