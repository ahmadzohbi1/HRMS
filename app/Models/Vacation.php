<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Vacation extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'vacation_type_id',
        'start_date',
        'end_date',
        'status',
        'applicant_name',
        'applicant_phone',
        'applicant_email',
        'reason',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function vacationType(): BelongsTo
    {
        return $this->belongsTo(VacationType::class);
    }

    public function getDurationInDaysAttribute(): int
    {
        return Carbon::parse($this->start_date)->diffInDays(Carbon::parse($this->end_date)) + 1;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeForYear($query, int $year)
    {
        return $query->whereYear('start_date', $year);
    }
    public function getEmployeeNameAttribute()
    {
        return $this->employee ? $this->employee->name : $this->applicant_name;
    }

    // Add accessor for employee email
    public function getEmployeeEmailAttribute()
    {
        return $this->employee ? $this->employee->email : $this->applicant_email;
    }
}