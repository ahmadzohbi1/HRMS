<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Employee;

class Deduction extends Model
{
    use HasFactory;
    
    protected $table = 'salary_deductions';
    
    protected $fillable = [
        'salary_id',
        'amount',
        'deduction_date',
        'reason',
        'description',
        'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'deduction_date' => 'date'
    ];

    /**
     * Relationship with Salary
     */
    public function salary()
    {
        return $this->belongsTo(Salary::class, 'salary_id');
    }

    /**
     * Get employee through salary relationship
     */
    public function employee()
    {
        return $this->hasOneThrough(
            Employee::class, 
            Salary::class, 
            'id', 
            'id', 
            'salary_id', 
            'employee_id'
        );
    }

    /**
     * Scope for approved deductions
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for specific month
     */
    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('deduction_date', $year)
                     ->whereMonth('deduction_date', $month);
    }
}
