<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmscoService
{
    private string $username;
    private string $password;
    private string $baseUrl;
    private string $endpointSendSms;

    public function __construct()
    {
        $this->username = config('sms.smsco.username');
        $this->password = config('sms.smsco.password');
        $this->baseUrl = rtrim(config('sms.smsco.url'), '/');
        $this->endpointSendSms = config('sms.smsco.endpoint_send_sms', '/sendsms.php');
    }

    /**
     * Send SMS message or check balance.
     */
    public function send(string $to, string $message, ?string $schedule = null, bool $checkBalance = false): array
    {
        $to = $this->sanitizeRecipients($to);

        $params = [
            'username'  => $this->username,
            'password'  => $this->password,
            'recipient' => $to,
            'message'   => $message,
        ];

        if ($checkBalance) {
            $params['balance'] = 'true';
        }

        if ($schedule) {
            $params['schedule'] = $schedule;
        }

        return $this->handleSendRequest($params);
    }

    /**
     * Format recipients: keep only digits and commas.
     */
    private function sanitizeRecipients(string $to): string
    {
        return preg_replace('/[^\d,]/', '', $to);
    }

    /**
     * Handle send request.
     */
    private function handleSendRequest(array $params): array
    {
        $url = $this->baseUrl . $this->endpointSendSms;
        $response = Http::asForm()->post($url, $params);

        if (!$response->ok()) {
            Log::error("SMS Send Failed", ['status' => $response->status(), 'body' => $response->body()]);
            return $this->errorResponse('HTTP request failed with status ' . $response->status());
        }

        $body = trim($response->body());
        $errorCodes = config('sms.error_codes');

        if (isset($errorCodes[$body])) {
            return $this->errorResponse($errorCodes[$body]);
        }

        if (str_starts_with($body, 'OK')) {
            return $this->parseSuccessResponse($body);
        }

        return $this->errorResponse('Unexpected response: ' . $body);
    }

    /**
     * Parse success response body.
     */
    private function parseSuccessResponse(string $body): array
    {
        $parts = explode(' ', $body);

        return [
            'success' => true,
            'message' => 'SMS sent successfully',
            'used_credits' => isset($parts[1]) ? (int)$parts[1] : null,
            'sms_id' => $parts[2] ?? null,
        ];
    }

    /**
     * Return a standardized error response.
     */
    private function errorResponse(string $message): array
    {
        return [
            'success' => false,
            'message' => $message,
            'sms_id' => null,
            'used_credits' => null,
        ];
    }
}
