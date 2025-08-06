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
                'profy_id' => 1,
                'first_name' => 'Tornike',
                'last_name' => 'Gochashvili',
                'email' => 'tokogochashvili887@gmail.com',
                'phone' => '995551534340',
                'allow_notification' => true,
                'language' => 'en',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate([
                'email' => $customer['email']
            ], $customer);
        }
    }
}
