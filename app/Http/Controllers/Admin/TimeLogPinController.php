<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeLogPin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TimeLogPinController extends Controller
{
    /**
     * Display the PIN management page
     */
    public function index()
    {
        $pin = TimeLogPin::getActivePin();
        return view('admin.timelog-pin.index', compact('pin'));
    }

    /**
     * Store or update the PIN
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|digits:4|confirmed',
        ], [
            'pin.required' => 'PIN is required',
            'pin.digits' => 'PIN must be exactly 4 digits',
            'pin.confirmed' => 'PIN confirmation does not match',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $pin = TimeLogPin::getActivePin();
        
        if ($pin) {
            // Update existing PIN
            $pin->pin = $request->pin;
            $pin->updated_by = Auth::id();
            $pin->last_reset_date = now();
            $pin->save();
            
            $message = 'PIN updated successfully';
        } else {
            // Create new PIN
            TimeLogPin::create([
                'pin' => $request->pin,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'last_reset_date' => now(),
            ]);
            
            $message = 'PIN created successfully';
        }

        return redirect()->route('timelog-pin.index')
            ->with('success', $message);
    }

    /**
     * Reset the PIN
     */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_pin' => 'required|digits:4|confirmed',
        ], [
            'new_pin.required' => 'New PIN is required',
            'new_pin.digits' => 'PIN must be exactly 4 digits',
            'new_pin.confirmed' => 'PIN confirmation does not match',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $pin = TimeLogPin::getActivePin();
        
        if (!$pin) {
            return redirect()->back()
                ->with('error', 'No PIN found. Please create one first.');
        }

        $pin->pin = $request->new_pin;
        $pin->updated_by = Auth::id();
        $pin->last_reset_date = now();
        $pin->save();

        return redirect()->route('timelog-pin.index')
            ->with('success', 'PIN reset successfully');
    }
}
