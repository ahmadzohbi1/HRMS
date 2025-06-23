<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
class Salary extends Model
{
    protected $table = 'salaries';
    
    protected $fillable = [
        'employee_id',
        'fixed_salary',
        'effective_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'fixed_salary' => 'decimal:2',
        'effective_date' => 'date'
    ];

    // Relationship with Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // Relationship with Advances
    public function advances()
    {
        return $this->hasMany(Advance::class, 'salary_id');
    }

    // Relationship with Bonuses
    public function bonuses()
    {
        return $this->hasMany(Bonus::class, 'salary_id');
    }
}
