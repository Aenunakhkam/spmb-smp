<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
        ]);

        // Default Settings
        Setting::create(['key' => 'academic_year', 'value' => '2026/2027']);
        Setting::create(['key' => 'registration_status', 'value' => 'open']);
        Setting::create(['key' => 'quota', 'value' => '200']);
    }
}
