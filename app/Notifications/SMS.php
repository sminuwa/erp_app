<?php
namespace App\Notifications;
use Illuminate\Support\Facades\Http;

class SMS{
    public static function sendSms($to, $message, $from = 'BetaLife', $useCase = null)
    {
        $apiKey = 'UGJNcExKTUhlbGhGdE1vcG9ub0M'; // Replace with your actual API key
        
        $url = 'https://sms.arkesel.com/sms/api';
        
        $queryParams = [
            'action' => 'send-sms',
            'api_key' => $apiKey,
            'to' => $to,
            'from' => $from,
            'sms' => $message,
        ];
        
        if ($useCase) {
            $queryParams['use_case'] = $useCase;
        }
        
        $response = Http::get($url, $queryParams);
        
        return $response->body();
    }
}
