<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PositionsController extends Controller
{
    public function index()
    {
        $postions_data = Position::all();
        return view('admin.positions.index', compact('postions_data'));
    }
    public function create()
    {
        return view('admin.positions.create');
    }
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255|unique:positions,name',
        ]);

        try {
            Position::create([
                'name' => $request->name,
                'created_at' => now()
            ]);

            return redirect()->route('positions.index')->with('success', 'Position added successfully!');
        } catch (\Exception $e) {
            Log::error('Error storing position', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()->with('error', 'An error occurred while adding the department.');
        }
    }
    public function delete($id){
        $position = Position::find($id);
        $position->delete();
        return redirect()->route('positions.index')->with('success', 'Position deleted successfully!');
    }

}
