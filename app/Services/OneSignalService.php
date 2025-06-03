<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OneSignalService
{
    protected string $appId;
    protected string $apiKey;
    protected string $apiUrl;

    public function __construct(string $app = 'customer')
    {
        $config = config("onesignal.{$app}");

        $this->appId = $config['app_id'];
        $this->apiKey = $config['api_key'];
        $this->apiUrl = config('onesignal.rest_api_url') . '/api/v1/notifications';
    }

    public function send(array $playerIds, string $title, string $content, array $data = []): array
    {
        if (!is_array($data)) {
            $data = [];
        }

        $payload = [
            'app_id' => $this->appId,
            'include_player_ids' => $playerIds,
            'headings' => ['en' => $title],
            'contents' => ['en' => $content],
            'data' => (object) $data,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl, $payload);

        return $response->json();
    }
}
