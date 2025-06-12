<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Order;

class SyncOrdersFromApi extends Command
{
    protected $signature = 'sync:orders';
    protected $description = 'Sync orders from external API';

    public function handle()
    {
        $limit = 500;
        $offset = 0;
        $hasMore = true;

        while ($hasMore) {
            $this->info("Fetching orders with offset $offset and limit $limit...");

            $response = Http::get('https://apitest.profy.ge/api/laravel-sync/send-orders', [
                'limit' => $limit,
                'offset' => $offset,
            ]);

            if ($response->failed()) {
                $this->error("Failed to fetch data: " . $response->body());
                return 1;
            }

            $data = $response->json();

            if (empty($data['orders'])) {
                $this->info("No more orders to fetch.");
                break;
            }

            foreach ($data['orders'] as $orderData) {
                $this->saveOrUpdateOrder($orderData);
            }

            $count = count($data['orders']);
            $this->info("Processed $count orders.");

            $offset += $count;

            if ($count < $limit) {
                $hasMore = false;
            }
        }

        $this->info("Order sync completed.");
        return 0;
    }

    protected function saveOrUpdateOrder(array $orderData)
    {
        $completedAt = is_numeric($orderData['completed_at']) ? date('Y-m-d H:i:s', $orderData['completed_at']) : null;
        $createdAt = is_numeric($orderData['created_at']) ? date('Y-m-d H:i:s', $orderData['created_at']) : null;

        $taskerProfessionMap = [
            'Cleaner' => 1,
            'Handyman' => 2,
        ];
        $taskerProfession = $orderData['tasker_profession'] ?? null;
        $taskerProfessionId = $taskerProfessionMap[$taskerProfession] ?? null;

        Order::updateOrCreate(
            ['order_number' => $orderData['order_id']],
            [
                'customer_id' => $orderData['user_id'],
                'service_finished_at' => $completedAt,
                'price' => $orderData['price'],
                'type' => $taskerProfessionId,
                'created_at' => $createdAt,
            ]
        );
    }
}
