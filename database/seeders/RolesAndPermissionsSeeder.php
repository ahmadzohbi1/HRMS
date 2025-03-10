<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create default roles if they don't exist
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);

        // List of all permissions (view, create, edit, update, delete permissions)
        $permissions = [
            'view:dashboard',
            // companies permissions
            'view companies',
            'create companies',
            'edit companies',
            'update companies',
            'delete companies',
            // Admin permissions
            'view admins',
            'create admins',
            'edit admins',
            'update admins',
            'delete admins',
            // Role permissions
            'view roles',
            'create roles',
            'edit roles',
            'update roles',
            'delete roles',
            // Employee permissions
            'view employees',
            'create employees',
            'edit employees',
            'update employees',
            'delete employees',
            // Department permissions
            'view departments',
            'create departments',
            'edit departments',
            'update departments',
            'delete departments',
            // Position permissions
            'view positions',
            'create positions',
            'edit positions',
            'update positions',
            'delete positions',
            // Hourly Rate permissions
            'view hour_rate',
            'create hour_rate',
            'edit hour_rate',
            'update hour_rate',
            'delete hour_rate',
            // salaries permissions
            'view salaries',
            'create salaries',
            'edit salaries',
            'update salaries',
            'delete salaries',
            // salaries permissions
            'view shifts',
            'create shifts',
            'edit shifts',
            'update shifts',
            'delete shifts',
        ];

        // Create permissions in the database if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to Super Admin role
        $superAdminRole->givePermissionTo(Permission::all());

        // Assign Super Admin role to user with ID 1
        $user = User::find(1);
        if ($user) {
            $user->assignRole('Super Admin');
        }
    }
}
