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
    Permission::create(['name' => 'create']);
    Permission::create(['name' => 'edit']);
    Permission::create(['name' => 'delete']);
}

}
