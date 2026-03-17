<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Role;
use App\Models\Service;
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
        Role::create([
            'name' => 'Customer',
            'description' => 'Regular customer with no special permissions.'
        ]);
        Role::create([
            'name' => 'Admin',
            'description' => 'Administrator with full permissions.'
        ]);
        Role::create([
            'name' => 'Staff',
            'description' => 'Staff member with limited permissions.'
        ]);
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'role_id' => 2
        ]);

        $this->call(WeekSeeder::class);
    }
}
