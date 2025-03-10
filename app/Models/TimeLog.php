<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employees\Shift;
class TimeLog extends Model
{
    use HasFactory;
    protected $table = 'time_logs';
    protected $fillable = ['employee_id', 'date', 'time_in', 'time_out'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function shift(){
        return $this->belongsTo(Shift::class);
    }
}