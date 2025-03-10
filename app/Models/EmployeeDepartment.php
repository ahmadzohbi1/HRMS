<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDepartment extends Model
{
    protected $table = ['employee_departments'];

    protected $fillable = [
        'employee_id',
        'department_id',
        'created_at',
        'updated_at',
    ];
}
