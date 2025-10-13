<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VacationType;

class VacationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vacationTypes = [
            [
                'name' => 'Annual Leave',
                'is_paid' => true,
                'max_days_allowed' => 21,
            ],
            [
                'name' => 'Sick Leave',
                'is_paid' => true,
                'max_days_allowed' => 15,
            ],
            [
                'name' => 'Personal Leave',
                'is_paid' => false,
                'max_days_allowed' => 5,
            ],
            [
                'name' => 'Emergency Leave',
                'is_paid' => true,
                'max_days_allowed' => 3,
            ],
            [
                'name' => 'Unpaid Leave',
                'is_paid' => false,
                'max_days_allowed' => null, // Unlimited
            ],
            [
                'name' => 'Maternity Leave',
                'is_paid' => true,
                'max_days_allowed' => 90,
            ],
            [
                'name' => 'Paternity Leave',
                'is_paid' => true,
                'max_days_allowed' => 14,
            ],
            [
                'name' => 'Study Leave',
                'is_paid' => false,
                'max_days_allowed' => 10,
            ],
            [
                'name' => 'Bereavement Leave',
                'is_paid' => true,
                'max_days_allowed' => 5,
            ],
        ];

        foreach ($vacationTypes as $type) {
            VacationType::updateOrCreate(
                ['name' => $type['name']], // Find by name
                $type // Update or create with these values
            );
        }

        $this->command->info('✅ Vacation types seeded successfully!');
    }
}
