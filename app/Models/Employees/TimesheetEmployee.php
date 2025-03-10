<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Model;

class TimesheetEmployee extends Model
{
    protected $table = 'timesheet_employees';
    protected $fillable = [
        'employee_id',
        'total_hours_month',
        'month',
        'created_at',
        'updated_at'
    ];
}
