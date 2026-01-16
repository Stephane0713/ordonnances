<?php

namespace Database\Seeders;

use App\Models\Prescription;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (env("APP_ENV") === "prod")
            return;

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@stphdp.com',
            'password' => 'pass',
            'sms_token' => 'MySmsToken'
        ]);

        Prescription::factory(30)->create(['user_id' => 1]);
    }
}
