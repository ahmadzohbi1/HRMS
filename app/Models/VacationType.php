<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VacationType extends Model
{
    protected $fillable = [
        'name',
        'is_paid',
        'max_days_allowed'
    ];
}
