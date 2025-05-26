<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmscoService
{
    private $username;
    private $password;
    private $baseUrl;

    public function __construct()
    {
        $this->username = config('sms.smsco.username');
        $this->password = config('sms.smsco.password');
        $this->baseUrl = config('sms.smsco.url');
    }

    /**
     * Send SMS message
     *
     * @param string $to Recipient phone number(s), comma separated
     * @param string $message Text message (latin characters only)
     * @param string|null $schedule Optional schedule date "YYYY-MM-DD HH:mm:ss"
     * @param bool $checkBalance Whether to check credit balance instead of sending SMS
     * @return array ['success' => bool, 'message' => string, 'sms_id' => ?string, 'used_credits' => ?int]
     */
    public function send(string $to, string $message, ?string $schedule = null, bool $checkBalance = false): array
    {
        $to = preg_replace('/[^\d,]/', '', $to);

        $params = [
            'username' => $this->username,
            'password' => $this->password,
            'recipient' => $to,
            'message' => $message,
        ];

        if ($checkBalance) {
            $params['balance'] = 'true';
        }

        if ($schedule) {
            $params['schedule'] = $schedule;
        }

        $url = $this->baseUrl . '/sendsms.php';

        $response = Http::asForm()->post($url, $params);

        if (!$response->ok()) {
            return [
                'success' => false,
                'message' => 'HTTP request failed with status ' . $response->status(),
                'sms_id' => null,
                'used_credits' => null,
            ];
        }

        $body = trim($response->body());

        $errors = [
            '2904' => 'SMS Sending Failed',
            '2905' => 'Invalid username/password combination',
            '2906' => 'Credit exhausted',
            '2907' => 'Gateway unavailable',
            '2908' => 'Invalid schedule date format',
            '2909' => 'Unable to schedule',
            '2910' => 'Username is empty',
            '2911' => 'Password is empty',
            '2912' => 'Recipient is empty',
            '2913' => 'Message is empty',
            '2914' => 'Sender is empty',
            '2915' => 'One or more required fields are empty',
        ];

        if (array_key_exists($body, $errors)) {
            return [
                'success' => false,
                'message' => $errors[$body],
                'sms_id' => null,
                'used_credits' => null,
            ];
        }

        if (str_starts_with($body, 'OK')) {
            $parts = explode(' ', $body);
            return [
                'success' => true,
                'message' => 'SMS sent successfully',
                'used_credits' => isset($parts[1]) ? (int)$parts[1] : null,
                'sms_id' => $parts[2] ?? null,
            ];
        }

        return [
            'success' => false,
            'message' => 'Unexpected response: ' . $body,
            'sms_id' => null,
            'used_credits' => null,
        ];
    }

    /**
     * Check SMS status by SMS ID
     *
     * @param string $smsId
     * @return array ['success' => bool, 'status_code' => ?int, 'status_message' => ?string]
     */
    public function checkStatus(string $smsId): array
    {
        $params = [
            'username' => $this->username,
            'password' => $this->password,
            'mes_id' => $smsId,
        ];

        $url = $this->baseUrl . '/getstatus.php';

        $response = Http::asForm()->post($url, $params);

        if (!$response->ok()) {
            return [
                'success' => false,
                'status_code' => null,
                'status_message' => 'HTTP request failed with status ' . $response->status(),
            ];
        }

        $body = trim($response->body());

        $statuses = [
            '0' => 'Sent',
            '1' => 'Delivered',
            '2' => 'In process',
            '3' => 'Failed',
            '4' => 'Deleted',
            '5' => 'Expired',
            '6' => 'Rejected',
            '7' => 'Canceled',
        ];

        if (isset($statuses[$body])) {
            return [
                'success' => true,
                'status_code' => (int)$body,
                'status_message' => $statuses[$body],
            ];
        }

        return [
            'success' => false,
            'status_code' => null,
            'status_message' => 'Unexpected response: ' . $body,
        ];
    }
}
