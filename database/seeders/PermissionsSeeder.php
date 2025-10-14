<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;


class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Basic permissions
        Permission::firstOrCreate(['name' => 'create']);
        Permission::firstOrCreate(['name' => 'edit']);
        Permission::firstOrCreate(['name' => 'delete']);
        
        // Time Log PIN permissions
        Permission::firstOrCreate(['name' => 'view time log pin']);
        Permission::firstOrCreate(['name' => 'create time log pin']);
        Permission::firstOrCreate(['name' => 'edit time log pin']);
        Permission::firstOrCreate(['name' => 'update time log pin']);
        Permission::firstOrCreate(['name' => 'delete time log pin']);
    }

}
