<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class InternalHrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!User::where('email', 'internal.hr@example.com')->exists()) {
            User::create([
                'name' => 'Internal HR',
                'email' => 'internal.hr@example.com',
                'password' => bcrypt('internalhr123'),
                'role' => 'internal_hr',
            ]);
        }
    }
}
