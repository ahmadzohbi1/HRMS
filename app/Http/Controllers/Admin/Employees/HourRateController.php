<?php

namespace App\Http\Controllers\Admin\Employees;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Employees\HourRate;
use Illuminate\Http\Request;

class HourRateController extends Controller
{
    public function index()
    {
        $hour_data = HourRate::with('employee')->get();
        return view('admin.employees_hour_rate.index', compact('hour_data'));
    }
    public function create()
    {
        $employees = Employee::whereDoesntHave('hourRate')->get();
        return view('admin.employees_hour_rate.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|string|max:255',
            'hour_rate' => 'required|string|max:255',
            'currency' => 'required|string|max:255',
        ]);

        HourRate::create([
            'employee_id' => $validated['employee_id'],
            'hour_rate' => $validated['hour_rate'],
            'currency' => $validated['currency'],
        ]);

        // Redirect to index route instead of returning the view
        return redirect()->route('hour_rate.index')->with('success', 'Hour rate added successfully!');
    }
    public function edit($id)
    {
        $hourRate = HourRate::findOrFail($id);
        return view('admin.employees_hour_rate.edit', compact('hourRate'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'hour_rate' => 'required|numeric|min:0',
            'currency' => 'required|string|max:255',
        ]);

        $hourRate = HourRate::findOrFail($id);
        $hourRate->update($validated);

        return redirect()->route('hour_rate.index')->with('success', 'Hour rate updated successfully');
    }

}
