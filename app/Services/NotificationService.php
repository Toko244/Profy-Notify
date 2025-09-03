<?php

namespace App\Services;

use App\Mail\DefaultMail;
use App\Models\Notification;
use App\Models\NotificationTranslation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Services\SmscoService;
use App\Services\OneSignalService;

class NotificationService
{
    public Notification $notification;
    public $customers;

    public const LANGUAGE_LOCALE_TO_ID = [
        'en' => 1,
        'ka' => 2,
    ];

    public const DEFAULT_LANGUAGE_ID = 1;

    /**
     * Class constructor.
     */
    public function __construct(Notification $notification, $customers)
    {
        $this->notification = $notification;
        $this->customers = $customers;
    }

    /**
     * Get subject and content translation based on customer language.
     */
    protected function getTranslationForCustomer(array $customer): array
    {
        $locale = strtolower($customer['language'] ?? 'en');

        $languageId = self::LANGUAGE_LOCALE_TO_ID[$locale] ?? self::DEFAULT_LANGUAGE_ID;

        $translation = $this->notification->translations
            ->where('language_id', $languageId)
            ->first();

        return [
            'subject' => $translation ? $translation->subject : '',
            'content' => $translation ? $translation->content : '',
        ];
    }

    /**
     * Sends an SMS to a single customer and logs the result.
     *
     * @param array $customer
     * @param string $content
     * @return bool True on success, false on failure.
     */
    private function sendSmsForCustomer(array $customer, string $content): bool
    {
        $smscoService = new SmscoService();
        $result = $smscoService->send($customer['phone'], $content);

        if ($result['success']) {
            return true;
        } else {
            Log::error("Failed to send SMS to {$customer['phone']}: {$result['message']}");
            return false;
        }
    }

    /**
     * Sends a push notification to a single customer and logs the result.
     *
     * @param array $customer
     * @param array $translation
     * @return bool True on success, false on failure.
     */
    private function sendPushForCustomer(array $customer, array $translation): bool
    {
        $onesignalService = new OneSignalService();
        $content = str_replace(
            ['{first_name}', '{last_name}'],
            [$customer['first_name'], $customer['last_name']],
            $translation['content']
        );

        $subject = $translation['subject'] ?? '';

        $result = $onesignalService->send(
            [$customer['onesignal_player_id']],
            $subject,
            $content,
            [
                'notification_id' => $this->notification->id,
                'item_type' => 'product',
            ]
        );

        if (!isset($result['errors'])) {
            return true;
        } else {
            Log::error("Failed to send PUSH to {$customer['email']}: " . json_encode($result));
            return false;
        }
    }

    /**
     * @return array
     */
    public function email(): array
    {
        $sentCount = 0;
        foreach ($this->customers as $customer) {
            if (empty($customer['allow_notification'])) {
                Log::info("Not sending notification to customer due to disabled notifications.");
                continue;
            }

            $email = $customer['email'];
            $mailTemplate = 'mail.' . $this->notification->email_template;

            $translation = $this->getTranslationForCustomer($customer);
            $content = str_replace(
                ['{first_name}', '{last_name}'],
                [$customer['first_name'], $customer['last_name']],
                $translation['content']
            );

            $mailData = [
                'subject' => $translation['subject'],
                'content' => $content,
                'mailTemplate' => $mailTemplate,
            ];

            try {
                Mail::to($email)->send(new DefaultMail($mailData));
                $sentCount++;
            } catch (\Throwable $e) {
                Log::error("Failed to send email to {$email}: " . $e->getMessage());
            }
        }
        Log::info("customers count is: " . $sentCount);
        return ['email' => $sentCount];
    }

    /**
     * @return array
     */
    public function sms(): array
    {
        $smsSentCount = 0;
        $pushSentCount = 0;

        foreach ($this->customers as $customer) {
            if (empty($customer['allow_notification'])) {
                Log::info("Not sending notification to customer due to disabled notifications.");
                continue;
            }

            if (empty($customer['phone'])) {
                $translation = $this->getTranslationForCustomer($customer);
                if ($this->sendPushForCustomer($customer, $translation)) {
                    $pushSentCount++;
                }
                continue;
            }

            $translation = $this->getTranslationForCustomer($customer);
            $content = str_replace(
                ['{first_name}', '{last_name}'],
                [$customer['first_name'], $customer['last_name']],
                $translation['content']
            );

            if ($this->sendSmsForCustomer($customer, $content)) {
                $smsSentCount++;
            }
        }
        Log::info("customers count is: " . ($smsSentCount + $pushSentCount));

        return ['sms' => $smsSentCount, 'push' => $pushSentCount];
    }

    /**
     * @return array
     */
    public function push(): array
    {
        $pushSentCount = 0;
        $smsSentCount = 0;

        foreach ($this->customers as $customer) {
            if (empty($customer['allow_notification']) && $this->notification->send_sms_if_push_disabled) {
                $translation = $this->getTranslationForCustomer($customer);
                $content = str_replace(
                    ['{first_name}', '{last_name}'],
                    [$customer['first_name'], $customer['last_name']],
                    trim((string)$translation['subject'] . ' - ' . (string)$translation['content'])
                );

                if ($this->sendSmsForCustomer($customer, $content)) {
                    $smsSentCount++;
                }
                continue;
            }

            if (empty($customer['allow_notification'])) {
                continue;
            }

            $translation = $this->getTranslationForCustomer($customer);
            if ($this->sendPushForCustomer($customer, $translation)) {
                $pushSentCount++;
            }
        }

        return ['push' => $pushSentCount, 'sms' => $smsSentCount];
    }
}
