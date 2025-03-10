<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class HourRate extends Model
{
    protected $table = 'hour_rates';
    protected $fillable = [
        'employee_id',
        'hour_rate',
        'currency',
        'created_at',
        'updated_at'

    ];

    public function employee(){
        return $this->belongsTo(Employee::class);
    }
}
