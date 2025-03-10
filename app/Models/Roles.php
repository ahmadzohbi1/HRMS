<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $table = 'roles';  // Specify the table name if needed

    protected $fillable = ['role',];
      // Define which columns are mass assignable
      public function users()
{
    return $this->hasMany(User::class);
}
public function permissions()
{
    return $this->belongsToMany(Permissions::class, 'role_permission')
                ->withPivot('CRUD_access')
                ->withTimestamps();
}

}


