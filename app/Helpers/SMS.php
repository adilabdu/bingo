<?php

namespace App\Helpers;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class SMS
{
    const SEND_SMS_URL = "https://api.afromessage.com/api/send";

    /**
     * @throws ConnectionException
     */
    public static function send(
        string $to,
        string $message
    ): PromiseInterface|Response
    {
        return Http::acceptJson()
            ->withToken(self::secretKey())
            ->post(self::SEND_SMS_URL, [
                'to' => $to,
                'message' => $message
            ]);
    }

    private static function secretKey(): string
    {
        return config('sms.secret_key');
    }
}
