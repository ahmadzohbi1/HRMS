<?php

namespace App\Http\Controllers\Admin\Employees;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShiftRule;
class ShiftRuleController extends Controller
{
    public function index()
    {
        $shifts_rules = ShiftRule::all();
        return view("admin.shift_rules.index", compact("shifts_rules"));
    }
    public function create()
    {
        return view("admin.shift_rules.create");
    }
    public function store(Request $request)
    {
        $request->validate([
            'shift_name' => 'required|string|max:255',
            'time_in_after' => 'required|integer|min:0',
            'time_out_after' => 'required|integer|min:0',
            'hour_range' => 'nullable|integer|min:1|max:8',
            'warning_text' => 'nullable|string|max:500',
        ]);

        ShiftRule::create([
            'shift_title' => $request->shift_name,
            'time_in_apply' => $request->time_in_after,
            'time_out_apply' => $request->time_out_after,
            'deduct_hours' => $request->has('deduct_salary'),
            'day_hours_deduction' => $request->hour_range,
            'give_warning' => $request->has('give_warning'),
            'warning_description' => $request->warning_text,
        ]);

        return redirect()->route('shifts-rules.index')->with('success', 'Shift Rule created successfully.');
    }
    public function edit($id)
    {
        $shiftRule = ShiftRule::findOrFail($id);
        return view('admin.shift_rules.edit', compact('shiftRule'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'shift_name' => 'required|string|max:255',
            'time_in_after' => 'required|integer|min:0',
            'time_out_after' => 'required|integer|min:0',
            'hour_range' => 'nullable|integer|min:1|max:8',
            'warning_text' => 'nullable|string|max:500',
        ]);

        $shiftRule = ShiftRule::findOrFail($id);
        $shiftRule->update([
            'shift_title' => $request->shift_name,
            'time_in_apply' => $request->time_in_after,
            'time_out_apply' => $request->time_out_after,
            'deduct_hours' => $request->has('deduct_salary'),
            'day_hours_deduction' => $request->hour_range,
            'give_warning' => $request->has('give_warning'),
            'warning_description' => $request->warning_text,
        ]);

        return redirect()->route('shifts-rules.index')->with('success', 'Shift Rule updated successfully.');
    }
    public function destroy($id){
        ShiftRule::find($id)->delete();
        return redirect()->route('shifts-rules.index');
    }


}
