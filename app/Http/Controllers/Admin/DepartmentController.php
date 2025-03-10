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

    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $data = Department::select(['id', 'name', 'created_at'])->get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->setRowClass(fn($row) => 'align-middle')
                    ->editColumn('created_at', fn($row) => $row->created_at->format('Y-m-d H:i:s'))
                    ->addColumn('action', function ($row) {
                        return '<div class="d-flex">
                                    <a href="' . route('departments.edit', $row->id) . '" class="btn btn-sm btn-warning me-1">Edit</a>
                                </div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } catch (\Exception $e) {
                // Log the error
                Log::error('AJAX Error in DepartmentController@index', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json(['error' => 'An error occurred while fetching the data. Check logs for details.'], 500);
            }
        }
        return view('admin.departments.index');
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

}
