<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = ['Admin', 'Editor', 'Viewer'];

        foreach ($roles as $roleName) {
            Role::updateOrCreate(['name' => $roleName]); // Avoid duplicates
        }
    }
}
