<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SMSService
{
    public function send($number, $message)
    {
        $response = Http::post('https://api.semaphore.co/api/v4/messages', [
            'apikey'     => env('SEMAPHORE_API_KEY'),
            'number'     => $number,
            'message'    => $message,
            'sendername' => env('SEMAPHORE_SENDER_NAME'),
        ]);
        
        \Log::info('SMS Raw Response:', ['body' => $response->body()]);

        return $response->json();
    }
}
