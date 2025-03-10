<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Model;

class Warning extends Model
{
    protected $table = ['warnings'];

    protected $fillable = [
        'employee_id',
        'warning_title',
        'warning_type',
        'warning_description',
        'created_at',
        'updated_at'
    ];
}
