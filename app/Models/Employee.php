<?php

namespace App\Models;

use App\Models\Employees\HourRate;
use App\Models\Employees\Shift;
use App\Models\Employees\EmployeeHasShifts;
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
    public function hourRate()
    {
        return $this->hasOne(HourRate::class);
    }
    public function warnings()
    {
        return $this->hasMany(Warning::class);
    }
    public function shifts()
    {
        return $this->belongsToMany(Shift::class, 'employee_has_shifts')
            ->using(EmployeeHasShifts::class); // Using the custom pivot model
    }

}
