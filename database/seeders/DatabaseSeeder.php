<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\InternalHrSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create test user if not exists
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        // Create admin user if not exists
        if (!User::where('email', 'admin')->exists()) {
            User::create([
                'name' => 'admin',
                'email' => 'admin',
                'password' => bcrypt('admin123'),
                'role' => 'superadmin',
            ]);
        }

        // Create admin production user if not exists
        if (!User::where('email', 'adminprod')->exists()) {
            User::create([
                'name' => 'Admin Production',
                'email' => 'adminprod',
                'password' => bcrypt('adminprod123'),
                'role' => 'admin_prod',
            ]);
        }

        // Seed internal HR user
        $this->call(InternalHrSeeder::class);
    }
}
