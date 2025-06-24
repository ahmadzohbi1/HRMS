<?php

// app/Models/VacationType.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VacationType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_paid',
        'max_days_allowed',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'max_days_allowed' => 'integer',
    ];

    public function vacations(): HasMany
    {
        return $this->hasMany(Vacation::class);
    }

    public function employeeVacationBalances(): HasMany
    {
        return $this->hasMany(EmployeeVacationBalance::class);
    }

    public function isUnlimited(): bool
    {
        return is_null($this->max_days_allowed);
    }
}