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
                'name' => 'Natia',
                'email' => 'natia.sikarulidze955@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('Aa123456!')
            ],
            [
                'name' => 'Toko',
                'email' => 'tokogochashvili887@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('Toko2005')
            ],
            [
                'name' => 'Admin',
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
