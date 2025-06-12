<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class SyncUsersFromApi extends Command
{
    protected $signature = 'sync:users';
    protected $description = 'Sync users from external Yii API';

    public function handle()
    {
        $limit = 500;
        $offset = 0;
        $hasMore = true;

        while ($hasMore) {
            $this->info("Fetching users with offset $offset and limit $limit...");

            $response = Http::get('https://apitest.profy.ge/api/laravel-sync/send-users', [
                'limit' => $limit,
                'offset' => $offset,
            ]);

            if ($response->failed()) {
                $this->error("Failed to fetch data: " . $response->body());
                return 1;
            }

            $data = $response->json();

            if (empty($data['users'])) {
                $this->info("No more users to fetch.");
                break;
            }

            foreach ($data['users'] as $userData) {
                $this->saveOrUpdateUser($userData);
            }

            $count = count($data['users']);
            $this->info("Processed $count users.");

            $offset += $count;
            if ($count < $limit) {
                $hasMore = false;
            }
        }

        $this->info("User sync completed.");
        return 0;
    }

    protected function saveOrUpdateUser(array $userData)
    {
        $createdAt = is_numeric($userData['created_at']) ? date('Y-m-d H:i:s', $userData['created_at']) : null;

        $allowNotification = isset($userData['allow_notification']) ? (bool)$userData['allow_notification'] : false;

        Customer::updateOrCreate(
            ['profy_id' => $userData['profy_id']],
            [
                'first_name' => $userData['first_name'] ?? null,
                'last_name' => $userData['last_name'] ?? null,
                'email' => $userData['email'] ?? null,
                'phone' => $userData['phone'] ?? null,
                'disable_push_notifications' => $allowNotification ? 0 : 1,
                'onesignal_player_id' => $userData['player_id'] ?? null,
                'created_at' => $createdAt,
            ]
        );
    }
}
