<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $positions = [
            'Chief Executive Officer (CEO)',
            'Chief Operating Officer (COO)',
            'Chief Financial Officer (CFO)',
            'Chief Marketing Officer (CMO)',
            'Project Manager',
            'Operations Manager',
            'Human Resources Manager',
            'Finance Manager',
            'Software Engineer',
            'Graphic Designer',
            'Marketing Specialist',
            'Accountant',
            'Administrative Assistant',
            'Customer Support',
            'Sales Manager',
            'Business Analyst',
            'Product Manager',
            'Data Scientist',
        ];

        foreach ($positions as $position) {
            DB::table('positions')->insert([
                'name' => $position,
            ]);
        }
    }
}
