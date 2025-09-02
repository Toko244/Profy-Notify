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

    public function email(): int
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
        return $sentCount;
    }

    public function sms(): int
    {
        $smscoService = new SmscoService();
        $sentCount = 0;

        foreach ($this->customers as $customer) {
            if (empty($customer['allow_notification'])) {
                Log::info("Not sending notification to customer due to disabled notifications.");
                continue;
            }

            $translation = $this->getTranslationForCustomer($customer);
            $content = str_replace(
                ['{first_name}', '{last_name}'],
                [$customer['first_name'], $customer['last_name']],
                $translation['content']
            );

            $result = $smscoService->send($customer['phone'], $content);

            if ($result['success']) {
                $sentCount++;
            } else {
                Log::error("Failed to send SMS to {$customer['phone']}: {$result['message']}");
            }
        }
        Log::info("customers count is: " . $sentCount);

        return $sentCount;
    }

    public function push(): int
    {
        $onesignalService = new OneSignalService();
        $sentCount = 0;

        foreach ($this->customers as $customer) {
            if (empty($customer['allow_notification'])) {
                Log::info("Not sending notification to customer due to disabled notifications.");
                continue;
            }

            $translation = $this->getTranslationForCustomer($customer);
            $content = str_replace(
                ['{first_name}', '{last_name}'],
                [$customer['first_name'], $customer['last_name']],
                $translation['content']
            );

            $result = $onesignalService->send(
                [$customer['onesignal_player_id']],
                $translation['subject'],
                $content,
                [
                    'notification_id' => $this->notification->id,
                    'item_type' => 'product',
                ]
            );

            if (!isset($result['errors'])) {
                $sentCount++;
            } else {
                Log::error("Failed to send PUSH to {$customer}: {$result}");
            }
        }

        return $sentCount;
    }
}
