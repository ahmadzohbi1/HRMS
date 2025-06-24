<?php

namespace App\Models;

use App\Models\Employees\Salary;
use App\Models\Employees\Bonus;
use App\Models\Employees\Advance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $table = 'employees';
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'date_of_birth',
        'pin',
        'place_of_birth',
        'gender',
        'marital_status',
        'nationality',
        'blood_type',
        'image_url',
        'created_at',
        'updated_at',
        'position_id'
    ];

    public function timeLog()
    {
        return $this->hasMany(TimeLog::class, 'employee_id');
    }
    
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'employee_departments', 'employee_id', 'department_id');
    }
    
    public function warnings()
    {
        return $this->hasMany(Warning::class);
    }

    // Salary Relationships
    public function salaries()
    {
        return $this->hasMany(Salary::class, 'employee_id');
    }

    // Get current active salary
    public function currentSalary()
    {
        return $this->hasOne(Salary::class, 'employee_id')
                    ->where('status', 'active')
                    ->orderBy('effective_date', 'desc');
    }

    // Get advances through salaries
    public function advances()
    {
        return $this->hasManyThrough(Advance::class, Salary::class, 'employee_id', 'salary_id');
    }

    // Get bonuses through salaries
    public function bonuses()
    {
        return $this->hasManyThrough(Bonus::class, Salary::class, 'employee_id', 'salary_id');
    }

    // Vacation Relationships
    public function vacations(): HasMany
    {
        return $this->hasMany(Vacation::class);
    }

    public function vacationBalances(): HasMany
    {
        return $this->hasMany(EmployeeVacationBalance::class);
    }

    // Vacation Helper Methods
    public function getVacationBalance(int $vacationTypeId, int $year = null): ?EmployeeVacationBalance
    {
        $year = $year ?? now()->year;
        
        return $this->vacationBalances()
            ->where('vacation_type_id', $vacationTypeId)
            ->where('year', $year)
            ->first();
    }

    public function approvedVacations(): HasMany
    {
        return $this->vacations()->approved();
    }

    public function pendingVacations(): HasMany
    {
        return $this->vacations()->pending();
    }

    public function vacationsForYear(int $year): HasMany
    {
        return $this->vacations()->forYear($year);
    }

    public function getTotalVacationDaysUsed(int $vacationTypeId, int $year = null): int
    {
        $year = $year ?? now()->year;
        
        return $this->vacations()
            ->approved()
            ->where('vacation_type_id', $vacationTypeId)
            ->forYear($year)
            ->get()
            ->sum('duration_in_days');
    }

    public function hasVacationBalance(int $vacationTypeId, int $days, int $year = null): bool
    {
        $balance = $this->getVacationBalance($vacationTypeId, $year);
        return $balance ? $balance->hasBalance($days) : false;
    }
}