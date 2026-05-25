<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Users
        $roleAdmin = \App\Models\Role::create(['name' => 'Admin']);
        $roleOperator = \App\Models\Role::create(['name' => 'Operator']);
        $roleSupir = \App\Models\Role::create(['name' => 'Supir']);
        $rolePimpinan = \App\Models\Role::create(['name' => 'Pimpinan']);

        $userAdmin = \App\Models\User::create([
            'name' => 'Administrator',
            'email' => 'admin@erickman.com',
            'password' => bcrypt('password'),
            'role_id' => $roleAdmin->id,
        ]);

        $userSupir1 = \App\Models\User::create([
            'name' => 'Budi Supir',
            'email' => 'budi@erickman.com',
            'password' => bcrypt('password'),
            'role_id' => $roleSupir->id,
        ]);

        $userSupir2 = \App\Models\User::create([
            'name' => 'Agus Supir',
            'email' => 'agus@erickman.com',
            'password' => bcrypt('password'),
            'role_id' => $roleSupir->id,
        ]);

        // 2. Drivers
        $driver1 = \App\Models\Driver::create([
            'user_id' => $userSupir1->id,
            'nik' => '3171010101010001',
            'phone' => '081234567890',
        ]);

        $driver2 = \App\Models\Driver::create([
            'user_id' => $userSupir2->id,
            'nik' => '3171010101010002',
            'phone' => '081234567891',
        ]);

        // 3. Vehicles
        $vehicle1 = \App\Models\Vehicle::create([
            'license_plate' => 'B 1234 CD',
            'type' => 'Engkel',
            'capacity' => 100,
            'status' => 'on_trip',
            'tax_date' => '2027-01-01',
        ]);

        $vehicle2 = \App\Models\Vehicle::create([
            'license_plate' => 'B 5678 EF',
            'type' => 'Fuso',
            'capacity' => 300,
            'status' => 'on_trip',
            'tax_date' => '2027-02-01',
        ]);

        // 4. Trips
        $trip1 = \App\Models\Trip::create([
            'vehicle_id' => $vehicle1->id,
            'driver_id' => $driver1->id,
            'start_time' => now()->subHours(1),
            'status' => 'ongoing',
        ]);

        $trip2 = \App\Models\Trip::create([
            'vehicle_id' => $vehicle2->id,
            'driver_id' => $driver2->id,
            'start_time' => now()->subMinutes(30),
            'status' => 'ongoing',
        ]);

        // 5. GPS Logs (Simulate Jakarta locations)
        // Monas area for trip 1
        \App\Models\GpsLog::create([
            'trip_id' => $trip1->id,
            'latitude' => -6.1754,
            'longitude' => 106.8272,
            'speed' => 45.5,
            'recorded_at' => now(),
        ]);

        // SCBD area for trip 2
        \App\Models\GpsLog::create([
            'trip_id' => $trip2->id,
            'latitude' => -6.2272,
            'longitude' => 106.8093,
            'speed' => 0.0, // Stopped
            'recorded_at' => now(),
        ]);
    }
}
