<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use App\Models\Department;
use App\Models\ShiftRule;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_name',
        'time_in',
        'time_out',
        'shift_type',
        'employee_id',
        'department_id',
        'company_shift'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function shiftRules()
{
    return $this->belongsToMany(ShiftRule::class, 'shift_has_rules', 'shift_id', 'shift_rule_id')
        ->withTimestamps();
}



}
