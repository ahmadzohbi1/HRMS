<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vacation;
use App\Models\VacationType;
use App\Models\Employee;
use Illuminate\Http\Request;

class VacationController extends Controller
{
    public function index()
    {
        $vacations = Vacation::with('employee', 'vacationType')->get();
        return view('admin.vacations.index', compact('vacations'));
    }
    public function create()
    {
        $employees = Employee::all();
        $vacationTypes = VacationType::all();
        return view('admin.vacations.create', compact('vacationTypes', 'employees'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'vacation_type_id' => 'required|exists:vacation_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:pending,approved,rejected',
            'reason' => 'required|string|max:255',
        ]);

        Vacation::create($request->all());

        return redirect()->route('vacations.index')->with('success', 'Vacation request created successfully.');
    }
    public function show($id)
    {
        $vacation = Vacation::with('employee', 'vacationType')->findOrFail($id);
        return view('admin.vacations.show', compact('vacation'));
    }
    public function edit($id)
    {
        $vacation = Vacation::findOrFail($id);
        $employees = Employee::all();
        $vacationTypes = VacationType::all();
        return view('admin.vacations.edit', compact('vacation', 'employees', 'vacationTypes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'vacation_type_id' => 'required|exists:vacation_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:pending,approved,rejected',
            'reason' => 'required|string|max:255',
        ]);

        $vacation = Vacation::findOrFail($id);
        $vacation->update($request->all());

        return redirect()->route('vacations.index')->with('success', 'Vacation request updated successfully.');
    }

    public function destroy($id)
    {
        $vacation = Vacation::findOrFail($id);
        $vacation->delete();

        return redirect()->route('vacations.index')->with('success', 'Vacation request deleted successfully.');
    }

}
