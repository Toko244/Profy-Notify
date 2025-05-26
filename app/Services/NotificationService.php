<?php

namespace App\Services;

use App\Mail\DefaultMail;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public Notification $notification;
    public $customers;

    /**
     * Class constructor.
     */
    public function __construct(Notification $notification, $customers)
    {
        $this->notification = $notification;
        $this->customers = $customers;
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
            $phone = $customer['phone'];
            $content = $this->notification->content;

            $content = str_replace('{first_name}', $customer['first_name'], $content);
            $content = str_replace('{last_name}', $customer['last_name'], $content);

            $result = $smscoService->send($phone, $content);

            if ($result['success']) {
                echo "SMS sent! Used credits: {$result['used_credits']}, SMS ID: {$result['sms_id']}";
            } else {
                echo "Failed to send SMS: {$result['message']}";
            }
        }
    }
}
