<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employees\Shift;

class ShiftRule extends Model
{
    use HasFactory;

    protected $table = 'shift_rules';

    protected $fillable = [
        'shift_title',
        'time_in_apply',
        'time_out_apply',
        'deduct_hours',
        'day_hours_deduction',
        'give_warning',
        'warning_description',
    ];

    /**
     * The shifts that this rule applies to.
     */
    public function shifts()
    {
        return $this->belongsToMany(Shift::class, 'shift_has_rules', 'shift_rule_id', 'shift_id')
                    ->withTimestamps();
    }
}
