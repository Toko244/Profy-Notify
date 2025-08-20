<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('admin')
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate([
                'email' => $user['email']
            ], $user);
        }
    }
}
