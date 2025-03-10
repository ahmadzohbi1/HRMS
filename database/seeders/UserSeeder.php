<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //admin 
        User::firstOrCreate(
            [
                "email" => "admin@energica.com.lb",
                "name" => "Super Admin",
                "phone" => '',
            ],
            [
                'is_admin' => true,
                "password" => bcrypt("admin",)
            ]
        );

        

    }
}
