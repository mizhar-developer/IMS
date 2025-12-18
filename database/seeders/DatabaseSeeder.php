<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        $more = [
            ['first_name' => 'John', 'last_name' => 'Doe', 'email' => 'john.doe@example.com'],
            ['first_name' => 'Alice', 'last_name' => 'Smith', 'email' => 'alice.smith@example.com'],
            ['first_name' => 'Robert', 'last_name' => 'Johnson', 'email' => 'robert.johnson@example.com'],
            ['first_name' => 'Linda', 'last_name' => 'Nguyen', 'email' => 'linda.nguyen@example.com'],
            ['first_name' => 'Carlos', 'last_name' => 'Garcia', 'email' => 'carlos.garcia@example.com'],
        ];

        foreach ($more as $pData) {
            $createdPatients[] = \App\Models\Patient::create($pData);
        }

        // Admin
        User::factory()->create([
            'first_name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Accountant
        User::factory()->create([
            'first_name' => 'Accountant User',
            'email' => 'accountant@example.com',
            'password' => bcrypt('password'),
            'role' => 'accountant',
        ]);

        // Radiologists (sample staff)
        User::factory()->create([
            'first_name' => 'Radiologist User',
            'email' => 'radiologist@example.com',
            'password' => bcrypt('password'),
            'role' => 'radiologist',
        ]);
        User::factory()->create([
            'first_name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => bcrypt('password'),
            'role' => 'manager',
        ]);

        // Doctors (sample staff)
        User::factory()->create([
            'first_name' => 'Doctor User',
            'email' => 'doctor@example.com',
            'password' => bcrypt('password'),
            'role' => 'doctor',
        ]);

        // Receptionist
        User::factory()->create([
            'first_name' => 'Receptionist User',
            'email' => 'receptionist@example.com',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
        ]);

    }
}
