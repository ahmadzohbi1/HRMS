<?php

namespace App\Models;

use App\Models\Employees\Salary;
use App\Models\Employees\Bonus;
use App\Models\Employees\Advance;
use Illuminate\Database\Eloquent\Model;

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

    // New Salary Relationship
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
}