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
                'name' => 'Temo Kasaburi',
                'email' => 'admin@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('admin')
            ],
            [
                'name' => 'Profy Prod',
                'email' => 'prod@profy.ge',
                'email_verified_at' => now(),
                'password' => bcrypt('e8kT0YblltrHRHgm5Z2h')
            ],
            [
                'name' => 'Nika Nadiradze',
                'email' => 'n.nadiradze@profy.ge',
                'email_verified_at' => now(),
                'password' => bcrypt('Prof!5@5')
            ],
            [
                'name' => 'Elina Oganesiani',
                'email' => 'elinaoganesiani@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('Prof!5@5')
            ],

        ];

        foreach ($users as $user) {
            User::firstOrCreate([
                'email' => $user['email']
            ], $user);
        }
    }
}
