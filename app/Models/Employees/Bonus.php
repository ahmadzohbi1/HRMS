<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
class Bonus extends Model
{
    protected $table = 'bonuses';
    
    protected $fillable = [
        'salary_id',
        'amount',
        'bonus_date',
        'type',
        'reason',
        'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'bonus_date' => 'date'
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