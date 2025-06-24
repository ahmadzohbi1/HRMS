<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeVacationBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'vacation_type_id',
        'balance',
        'year',
    ];

    protected $casts = [
        'balance' => 'integer',
        'year' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function vacationType(): BelongsTo
    {
        return $this->belongsTo(VacationType::class);
    }

    public function hasBalance(int $days): bool
    {
        return $this->balance >= $days;
    }

    public function deductBalance(int $days): void
    {
        $this->balance -= $days;
        $this->save();
    }

    public function addBalance(int $days): void
    {
        $this->balance += $days;
        $this->save();
    }

    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }
}