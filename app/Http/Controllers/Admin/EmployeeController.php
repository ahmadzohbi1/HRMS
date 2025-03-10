<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Employee::with(['position', 'departments']);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('position', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('departments', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $data = $query->paginate(10);

        return view('admin.employees.index', compact('data'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::all();
        $positions = Position::all();
        return view('admin.employees.create', compact('departments', 'positions'));
    }
    public function store(Request $request)
    {
        try {
            // Validate the input data
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'gender' => 'required|string|max:255',
                'blood_type' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'place_of_birth' => 'nullable|string|max:255',
                'nationality' => 'required|string|max:255',
                'marital_status' => 'required|string|max:255',
                'phone' => 'required|string|max:15',
                'email' => 'required|email|unique:employees,email',
                'address' => 'required|string|max:500',
                'date_of_birth' => 'required|date',
                'pin' => 'required|string|size:4',
                'position_id' => 'required|exists:positions,id',
                'department_ids' => 'nullable|array',
                'department_ids.*' => 'exists:departments,id'
            ]);
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('/employees', 'public');
            }
            Log::info('Validation Passed', $validated);

            $employee = Employee::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'address' => $validated['address'],
                'date_of_birth' => $validated['date_of_birth'],
                'pin' => $validated['pin'],
                'gender' => $validated['gender'],
                'marital_status' => $validated['marital_status'],
                'nationality' => $validated['nationality'],
                'place_of_birth' => $validated['place_of_birth'],
                'blood_type' => $validated['blood_type'],
                'image_url' => $imagePath,
                'position_id' => $validated['position_id']
            ]);
            if (isset($validated['department_ids'])) {
                $employee->departments()->sync($validated['department_ids']);
            }
            Log::info('Employee Created', ['employee_id' => $employee->id]);
            return redirect()->route('employees.index')->with('success', 'Employee created successfully');

        } catch (\Exception $e) {
            // Log any errors
            Log::error('Error in Employee Store Method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Failed to create employee. Check logs for details.');
        }
    }

    public function show($id)
    {
        $employee = Employee::with('position')->findOrFail($id);
        return view('admin.employees.show', compact('employee'));
    }
    public function show_department($id)
    {
        $employee = Employee::with('departments')->findOrFail($id);
        return view('admin.employees.departments.index', compact('employee'));
    }


    public function edit(string $id)
    {
        $departments = Department::all();
        $positions = Position::all();
        $employee = Employee::findOrFail($id);
        return view('admin.employees.edit', compact('employee', 'departments', 'positions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate input data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'blood_type' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'place_of_birth' => 'nullable|string|max:255',
            'nationality' => 'required|string|max:255',
            'marital_status' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|unique:employees,email,' . $id,
            'address' => 'required|string|max:500',
            'date_of_birth' => 'required|date',
            'pin' => 'required|string|size:4',
            'position_id' => 'required|exists:positions,id',
            'department_ids' => 'nullable|array',
            'department_ids.*' => 'exists:departments,id'
        ]);

        // Find the employee
        $employee = Employee::findOrFail($id);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('/employees', 'public');
        } else {
            $imagePath = $employee->image_url; // Keep the existing image if no new image is uploaded
        }

        // Update employee details
        $employee->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'date_of_birth' => $validated['date_of_birth'],
            'pin' => $validated['pin'],
            'gender' => $validated['gender'],
            'marital_status' => $validated['marital_status'],
            'nationality' => $validated['nationality'],
            'place_of_birth' => $validated['place_of_birth'],
            'blood_type' => $validated['blood_type'],
            'image_url' => $imagePath,
            'position_id' => $validated['position_id']
        ]);

        // If department_ids is present, update the department relationships

        if ($request->has('department_ids')) {
            $departmentIds = $validated['department_ids'] ?? [];
            if (count($departmentIds) > 0) {
                $employee->departments()->sync($departmentIds); // Sync selected departments
            } else {
                // If no departments selected, remove the current departments (optional behavior)
                $employee->departments()->detach(); // Detach departments without affecting others
            }
        }


        // Redirect back to the employee list with success message
        return redirect()->route('employees.index')->with('success', 'Employee details updated successfully.');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


}
