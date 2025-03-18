<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vacation extends Model
{
    protected $fillable = [
        'employee_id',
        'vacation_type_id',
        'start_date',
        'end_date',
        'status',
    ];
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    /**
     * Get the employee who owns the vacation.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the vacation type associated with the vacation.
     */
    public function vacationType()
    {
        return $this->belongsTo(VacationType::class);
    }
}
