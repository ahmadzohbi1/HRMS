<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $department = Department::all();
        return view('admin.departments.index', compact('department'));
        
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.departments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
        ]);

        try {
            Department::create([
                'name' => $request->name,
            ]);

            return redirect()->route('departments.index')->with('success', 'Department added successfully!');
        } catch (\Exception $e) {
            Log::error('Error storing department', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()->with('error', 'An error occurred while adding the department.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $department = Department::findOrFail($id);
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:department,name,' . $id,
        ]);

        try {
            $department = Department::findOrFail($id);
            $department->update([
                'name' => $request->name,
            ]);

            return redirect()->route('departments.index')->with('success', 'Department updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating department', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()->with('error', 'An error occurred while updating the department.');
        }
    }
    public function destroy($id){
        Department::findOrFail($id)->delete();
        return redirect()->route('departments.index')->with('success', 'Department deleted successfully!');
    }

}
