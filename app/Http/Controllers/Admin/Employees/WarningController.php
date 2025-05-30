<?php

namespace App\Http\Controllers\Admin\Employees;

use App\Http\Controllers\Controller;
use App\Models\Warning;
use App\Models\Employee;
use Illuminate\Http\Request;

class WarningController extends Controller
{
    public function index()
    {
        // Get all employees with their warning counts
        $employees = Employee::withCount('warnings')->get();
        return view("admin.warnings.index", compact("employees"));
    }

    public function employeeWarnings($employeeId)
    {
        $employee = Employee::with(['warnings' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->findOrFail($employeeId);
        
        $warnings = $employee->warnings;
        $warningsCount = $warnings->count();
        
        return view("admin.warnings.employee-warnings", compact("employee", "warnings", "warningsCount"));
    }

    public function create()
    {
        $employees = Employee::all();
        return view("admin.warnings.create", compact("employees"));
    }

    public function show($id)
    {
        $warning = Warning::with('employee')->find($id);
        return view("admin.warnings.show", compact("warning"));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'warning_title' => 'required',
            'warning_description' => 'required',
        ]);

        Warning::create($request->all());
        return redirect()->route('warnings.index');
    }

    public function edit($id)
    {
        $employees = Employee::all();
        $warning = Warning::find($id);
        return view("admin.warnings.edit", compact("warning", "employees"));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required',
            'warning_title' => 'required',
            'warning_description' => 'required',
        ]);

        Warning::find($id)->update($request->all());
        return redirect()->route('warnings.index');
    }

    public function destroy($id)
    {
        Warning::find($id)->delete();
        return redirect()->route('warnings.index');
    }
}