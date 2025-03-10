<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Models\Role;

class User extends Authenticatable implements HasMedia
{
    use Notifiable, SoftDeletes, InteractsWithMedia, HasApiTokens, HasFactory, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'role_id',
        'company_id',
        'password',
        'remember_token',
        'address',
        'is_admin',
        'fcm_token',
        'email_verified_at',
        'created_at',
        'updated_at',
    ];
    protected $dates = ['deleted_at'];
    

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function getCreatedAtAttribute($value)
    {
        return formatDate($value);
    }

    public function getProfileAttribute()
    {
        return getAvatar($this);
    }

    // Scopes
    public function scopeAdmin($query)
    {
        return $query->where('is_admin', 1);
    }


    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    

}
