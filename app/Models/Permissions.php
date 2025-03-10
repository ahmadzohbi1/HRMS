<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permissions extends Model
{
    protected $fillable = [
        'permission',
        'created_at',
        'edited_at'
    ];

    public function roles()
{
    return $this->belongsToMany(Roles::class, 'role_permission')
                ->withPivot('CRUD_access')
                ->withTimestamps();
}
}
