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
        Log::info('Customer info', [
            'language' => $customer['language'],
            'locale' => $locale,
        ]);
        $languageId = self::LANGUAGE_LOCALE_TO_ID[$locale] ?? self::DEFAULT_LANGUAGE_ID;

        $translation = $this->notification->translations
            ->where('language_id', $languageId)
            ->first();
        Log::info($translation);
        return [
            'subject' => $translation ? $translation->subject : '',
            'content' => $translation ? $translation->content : '',
        ];
    }

    public function email(): void
    {
        foreach ($this->customers as $customer) {
            $email = $customer['email'];
            $subject = $this->notification->subject;
            $content = $this->notification->content;
            $mailTemplate = 'mail.'.$this->notification->email_template;

            $content = str_replace('{first_name}', $customer['first_name'], $content);
            $content = str_replace('{last_name}', $customer['last_name'], $content);

            $mailData = [
                'subject' => $subject,
                'content' => $content,
                'mailTemplate' => $mailTemplate,
            ];

            Mail::to($email)->send(new DefaultMail($mailData));
        }
    }

    public function sms(): void
    {
        $smscoService = new SmscoService();

        foreach ($this->customers as $customer) {
            $translation = $this->getTranslationForCustomer($customer);
            $content = str_replace(
                ['{first_name}', '{last_name}'],
                [$customer['first_name'], $customer['last_name']],
                $translation['content']
            );
            Log::info($content);
            $result = $smscoService->send($customer['phone'], $content);

            if ($result['success']) {
                echo "SMS sent! Used credits: {$result['used_credits']}, SMS ID: {$result['sms_id']}";
            } else {
                echo "Failed to send SMS: {$result['message']}";
            }
        }
    }

    public function push(): void
    {
        $onesignalService = new OneSignalService();

        foreach ($this->customers as $customer) {
            $translation = $this->getTranslationForCustomer($customer);
            $content = str_replace(
                ['{first_name}', '{last_name}'],
                [$customer['first_name'], $customer['last_name']],
                $translation['content']
            );

            $onesignalService->send(
                [$customer['onesignal_player_id']],
                $translation['subject'],
                $content,
                [
                    'notification_id' => $this->notification->id,
                    'item_type' => 'product',
                ]
            );
        }
    }
}
