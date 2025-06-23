<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
class Advance extends Model
{
    protected $table = 'advances';
    
    protected $fillable = [
        'salary_id',
        'amount',
        'advance_date',
        'reason',
        'status',
        'deduction_start_date',
        'installments',
        'remaining_amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'advance_date' => 'date',
        'deduction_start_date' => 'date'
    ];

    // Relationship with Salary
    public function salary()
    {
        return $this->belongsTo(Salary::class, 'salary_id');
    }

    // Get employee through salary relationship
    public function employee()
    {
        return $this->hasOneThrough(Employee::class, Salary::class, 'id', 'id', 'salary_id', 'employee_id');
    }
}