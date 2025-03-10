<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
class Salary extends Model
{
    protected $table = 'salaries';
    protected $fillable = [
        'employee_id',
        'salary',
        'month',
        'created_at',
        'updated_at'

    ];
    public function employee(){
        return $this->belongsTo(Employee::class);
    }
}
