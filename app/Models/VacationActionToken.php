<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VacationActionToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'vacation_id',
        'token',
        'action',
        'used',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'used' => 'boolean',
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * Relationship with Vacation
     */
    public function vacation(): BelongsTo
    {
        return $this->belongsTo(Vacation::class);
    }

    /**
     * Generate a unique token for the vacation action
     */
    public static function generateToken(int $vacationId, string $action): self
    {
        // Delete any existing unused tokens for this vacation and action
        self::where('vacation_id', $vacationId)
            ->where('action', $action)
            ->where('used', false)
            ->delete();

        return self::create([
            'vacation_id' => $vacationId,
            'token' => Str::random(64),
            'action' => $action,
            'expires_at' => Carbon::now()->addDays(7), // Token expires in 7 days
        ]);
    }

    /**
     * Check if token is valid (not used and not expired)
     */
    public function isValid(): bool
    {
        return !$this->used && $this->expires_at->isFuture();
    }

    /**
     * Mark token as used
     */
    public function markAsUsed(): void
    {
        $this->update([
            'used' => true,
            'used_at' => Carbon::now(),
        ]);
    }

    /**
     * Scope to get valid tokens only
     */
    public function scopeValid($query)
    {
        return $query->where('used', false)
                    ->where('expires_at', '>', Carbon::now());
    }

    /**
     * Find valid token by token string
     */
    public static function findValidToken(string $token): ?self
    {
        return self::where('token', $token)
                   ->valid()
                   ->first();
    }
}
