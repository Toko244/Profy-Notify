<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'profy_id' => 1154,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@gmail.com',
                'phone' => '1234567890',
                'allow_notification' => true,
            ],
            [
                'profy_id' => 7534,
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'email' => 'jane.doe@gmail.com',
                'phone' => '0987654321',
                'allow_notification' => true,
            ],
            [
                'profy_id' => 84166,
                'first_name' => 'Nika',
                'last_name' => 'Nadiradze',
                'email' => 'nnadiradze.nika@gmail.com',
                'phone' => '571200064',
                'allow_notification' => true,
            ],
            [
                'profy_id' => 84165,
                'first_name' => 'Nika',
                'last_name' => 'Beruashvili',
                'email' => 'beruashvilinika6@gmail.com',
                'phone' => '557724869',
                'allow_notification' => true,
            ]
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate([
                'email' => $customer['email']
            ], $customer);
        }
    }
}
