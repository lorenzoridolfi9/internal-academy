<?php

namespace Database\Seeders;

use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin
        $admin = User::factory()->create([
            'name' => 'Admin Academy',
            'email' => 'admin@academy.it',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Employee
        $employees = User::factory(10)->create([
            'role' => 'employee',
            'password' => bcrypt('password'),
        ]);

        // Workshop con posti disponibili
        $workshops = Workshop::factory(5)->create([
            'created_by' => $admin->id,
            'capacity' => 10,
        ]);

        // Workshop pieno (per testare la waiting list)
        $fullWorkshop = Workshop::factory()->create([
            'created_by' => $admin->id,
            'capacity' => 3,
        ]);

        // Iscrivi tutti gli employee al workshop pieno
        foreach ($employees->take(3) as $employee) {
            Registration::create([
                'user_id' => $employee->id,
                'workshop_id' => $fullWorkshop->id,
                'status' => 'confirmed',
                'registered_at' => now(),
            ]);
        }

        // Metti i restanti in waiting list
        foreach ($employees->skip(3)->take(3) as $employee) {
            Registration::create([
                'user_id' => $employee->id,
                'workshop_id' => $fullWorkshop->id,
                'status' => 'waitlisted',
                'registered_at' => now(),
            ]);
        }

        // Iscrivi alcuni employee ai workshop normali
        foreach ($workshops as $workshop) {
            foreach ($employees->take(3) as $employee) {
                Registration::create([
                    'user_id' => $employee->id,
                    'workshop_id' => $workshop->id,
                    'status' => 'confirmed',
                    'registered_at' => now(),
                ]);
            }
        }
    }
}
