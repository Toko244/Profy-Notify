<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmscoService
{
    private $username;
    private $password;
    private $url;

    /**
     * Class constructor.
     */
    public function __construct(){
        $this->username = config('sms.smsco.username');
        $this->password = config('sms.smsco.password');
        $this->url = config('sms.smsco.url');
    }

    private function getUrl($action, $params = [])
    {
        $url = $this->url . '/' . $action . '.php?username=' . $this->username . '&password=' . $this->password;

        foreach ($params as $param => $value) {
            $url .= '&' . $param . '=' . urlencode($value);
        }

        return $url;
    }

    public function send($to, $message)
    {
        $to = preg_replace('#[^\d]*#si', '', $to);

        $url = $this->getUrl('smsapi2', ['recipient' => $to, 'message' => $message]);

        $response = Http::get($url);
        dd($response->body());
    }
}
