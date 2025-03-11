<?php

namespace App\Http\Controllers\Admin\Employees;

use App\Models\Employees\Shift;
use App\Models\Employee;
use App\Models\ShiftRule;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::with('shiftRules')->get();
        return view('admin.shifts.index', compact('shifts'));
    }
    public function create()
    {
        $departments = Department::all();  // Get all departments
        $employees = Employee::all();      // Get all employees
        $shiftRules = ShiftRule::all(); // Get all
        return view('admin.shifts.create', compact('departments', 'employees','shiftRules'));
    }


    public function store(Request $request)
    {

        // Validate input fields
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

        // Handle department shift
        if ($request->shift_type == 'department') {
            $shift->department_id = $request->department_id;
        }

        // Handle employee shift
        if ($request->shift_type == 'employee') {
            $shift->employee_id = $request->employee_id;
        }

        // Handle company-wide shift
        if ($request->shift_type == 'company') {
            $shift->company_shift = true;
        }

        $shift->save();

        return redirect()->route('shifts.index')->with('success', 'Shift created successfully!');
    }
    // In ShiftController.php

    public function edit($shiftId)
    {
        // Retrieve the shift by ID
        $shift = Shift::findOrFail($shiftId);

        // Pass the shift and any other necessary data to the view
        $departments = Department::all();
        $employees = Employee::all();

        return view('admin.shifts.edit', compact('shift', 'departments', 'employees'));
    }
    public function update(Request $request, Shift $shift)
    {
        // Validate input fields
        $request->validate([
            'shift_name' => 'required|string|max:255',
            'time_in' => 'required|date_format:H:i',
            'time_out' => 'required|date_format:H:i',
            'shift_type' => 'required|in:company,department,employee',
            'department_id' => 'nullable|required_if:shift_type,department|exists:departments,id',
            'employee_id' => 'nullable|required_if:shift_type,employee|exists:employees,id',
        ]);

        // Update the shift details
        $shift->shift_name = $request->shift_name;
        $shift->time_in = $request->time_in;
        $shift->time_out = $request->time_out;
        $shift->shift_type = $request->shift_type;

        // Reset department and employee fields to avoid conflicts
        $shift->department_id = null;
        $shift->employee_id = null;
        $shift->company_shift = false;

        // Handle department shift
        if ($request->shift_type == 'department') {
            $shift->department_id = $request->department_id;
        }

        // Handle employee shift
        if ($request->shift_type == 'employee') {
            $shift->employee_id = $request->employee_id;
        }

        // Handle company-wide shift
        if ($request->shift_type == 'company') {
            $shift->company_shift = true;
        }

        // Save the updated shift
        $shift->save();

        return redirect()->route('shifts.index')->with('success', 'Shift updated successfully!');
    }
    public function showRules($shiftId)
    {
        $shift = Shift::with('shiftRules')->findOrFail($shiftId); // Fetch shift with its rules
        return view('admin.shifts.rules.show', compact('shift'));
    }

}
