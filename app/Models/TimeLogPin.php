<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TimeLogPin extends Model
{
    protected $fillable = [
        'pin',
        'last_reset_date',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'last_reset_date' => 'date',
    ];

    /**
     * Get the user who created the PIN
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the PIN
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Set the PIN attribute (hashed)
     */
    public function setPinAttribute($value)
    {
        $this->attributes['pin'] = Hash::make($value);
    }

    /**
     * Check if PIN matches
     */
    public function checkPin($pin)
    {
        return Hash::check($pin, $this->pin);
    }

    /**
     * Check if PIN needs daily reset
     */
    public function needsDailyReset()
    {
        if (!$this->last_reset_date) {
            return true;
        }
        
        return Carbon::parse($this->last_reset_date)->isToday() === false;
    }

    /**
     * Mark PIN as reset for today
     */
    public function markAsResetToday()
    {
        $this->last_reset_date = Carbon::today();
        $this->save();
    }

    /**
     * Get or create the active PIN record
     */
    public static function getActivePin()
    {
        return self::first();
    }
}
