<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use Carbon\Carbon;

class Salary extends Model
{
    protected $table = 'salaries';
    
    protected $fillable = [
        'employee_id',
        'version',
        'fixed_salary',
        'effective_date',
        'end_date',
        'status',
        'is_current',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'fixed_salary' => 'decimal:2',
        'effective_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean'
    ];

    // Relationship with Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // Relationship with Advances
    public function advances()
    {
        return $this->hasMany(Advance::class, 'salary_id');
    }

    // Relationship with Bonuses
    public function bonuses()
    {
        return $this->hasMany(Bonus::class, 'salary_id');
    }

    // Relationship with Deductions
    public function deductions()
    {
        return $this->hasMany(Deduction::class, 'salary_id');
    }

    // Relationship with User who created this salary (optional)
    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // Scopes for easier querying
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeEffectiveAt($query, $date)
    {
        $date = Carbon::parse($date);
        return $query->where('effective_date', '<=', $date)
                    ->where(function($q) use ($date) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>', $date);
                    });
    }

    public function scopeForMonth($query, $year, $month)
    {
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        
        return $query->where('effective_date', '<=', $endOfMonth)
                    ->where(function($q) use ($startOfMonth) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>', $startOfMonth);
                    });
    }

    // Helper methods
    public function isActiveAt($date)
    {
        $date = Carbon::parse($date);
        return $this->effective_date <= $date && 
               ($this->end_date === null || $this->end_date > $date);
    }

    public function getNextVersion()
    {
        return self::where('employee_id', $this->employee_id)
                   ->max('version') + 1;
    }

    public function getPreviousVersion()
    {
        return self::where('employee_id', $this->employee_id)
                   ->where('version', '<', $this->version)
                   ->orderBy('version', 'desc')
                   ->first();
    }

    public function getNextVersionRecord()
    {
        return self::where('employee_id', $this->employee_id)
                   ->where('version', '>', $this->version)
                   ->orderBy('version', 'asc')
                   ->first();
    }

    // Static methods for salary management
    public static function getCurrentSalaryForEmployee($employeeId, $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();
        
        return self::forEmployee($employeeId)
                   ->effectiveAt($date)
                   ->active()
                   ->orderBy('effective_date', 'desc')
                   ->first();
    }

    public static function getSalaryForMonth($employeeId, $year, $month)
    {
        return self::forEmployee($employeeId)
                   ->forMonth($year, $month)
                   ->active()
                   ->orderBy('effective_date', 'desc')
                   ->first();
    }

    public static function createNewVersion($employeeId, $data)
    {
        // Get the current salary to determine version and end date
        $currentSalary = self::forEmployee($employeeId)
                            ->current()
                            ->active()
                            ->first();

        // Set end date for current salary if it exists
        if ($currentSalary) {
            $currentSalary->update([
                'end_date' => Carbon::parse($data['effective_date'])->subDay(),
                'is_current' => false
            ]);
            
            $nextVersion = $currentSalary->version + 1;
        } else {
            $nextVersion = 1;
        }

        // Create new salary version
        return self::create(array_merge($data, [
            'employee_id' => $employeeId,
            'version' => $nextVersion,
            'is_current' => true,
            'status' => $data['status'] ?? 'active'
        ]));
    }

    /**
     * Calculate net salary for a specific month
     */
    public function getNetSalaryForMonth($year, $month)
    {
        // Get advances for the month
        $advances = $this->advances()
            ->whereYear('advance_date', $year)
            ->whereMonth('advance_date', $month)
            ->whereIn('status', ['approved', 'paid'])
            ->sum('amount');
        
        // Get bonuses for the month
        $bonuses = $this->bonuses()
            ->whereYear('bonus_date', $year)
            ->whereMonth('bonus_date', $month)
            ->whereIn('status', ['approved', 'paid'])
            ->sum('amount');
        
        // Get deductions for the month
        $deductions = $this->deductions()
            ->whereYear('deduction_date', $year)
            ->whereMonth('deduction_date', $month)
            ->whereIn('status', ['approved', 'deducted'])
            ->sum('amount');
        
        // Calculate: Fixed Salary + Bonuses - Advances - Deductions
        return $this->fixed_salary + $bonuses - $advances - $deductions;
    }

    /**
     * Get all salary components for a month
     */
    public function getSalaryBreakdownForMonth($year, $month)
    {
        $advances = $this->advances()
            ->whereYear('advance_date', $year)
            ->whereMonth('advance_date', $month)
            ->whereIn('status', ['approved', 'paid'])
            ->get();
        
        $bonuses = $this->bonuses()
            ->whereYear('bonus_date', $year)
            ->whereMonth('bonus_date', $month)
            ->whereIn('status', ['approved', 'paid'])
            ->get();
        
        $deductions = $this->deductions()
            ->whereYear('deduction_date', $year)
            ->whereMonth('deduction_date', $month)
            ->whereIn('status', ['approved', 'deducted'])
            ->get();
        
        return [
            'fixed_salary' => $this->fixed_salary,
            'advances' => $advances,
            'advances_total' => $advances->sum('amount'),
            'bonuses' => $bonuses,
            'bonuses_total' => $bonuses->sum('amount'),
            'deductions' => $deductions,
            'deductions_total' => $deductions->sum('amount'),
            'net_salary' => $this->getNetSalaryForMonth($year, $month),
        ];
    }

    /**
     * Get effective month-year as formatted string
     */
    public function getEffectiveMonthYearAttribute()
    {
        return $this->effective_date->format('Y-m');
    }

    /**
     * Boot method to handle automatic versioning
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($salary) {
            if (empty($salary->version)) {
                $salary->version = self::where('employee_id', $salary->employee_id)
                                      ->max('version') + 1 ?: 1;
            }
        });

        static::created(function ($salary) {
            if ($salary->is_current) {
                // Mark other salaries for this employee as not current
                self::where('employee_id', $salary->employee_id)
                    ->where('id', '!=', $salary->id)
                    ->update(['is_current' => false]);
            }
        });
    }
}
