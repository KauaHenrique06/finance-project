<?php

namespace App\Helper;

use App\Exceptions\ApiException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class RequestHelper
{
    private const ALLOWED_METHODS = ['GET', 'POST', 'PUT', 'DELETE'];

    public static function request(
        string $method,
        string $url,
        ?string $asaasAccessToken = null,
        array $payload = [],
    ): Response {
        $method = strtoupper($method);

        if (!in_array($method, self::ALLOWED_METHODS, true))
        {
            throw new ApiException("This method is not permitted!");
        }

        $isProduction = app()->isProduction();

        $baseUrl = $isProduction
            ? config('services.asaas.url_production')
            : config('services.asaas.url');

        $defaultKey = $isProduction
            ? config('services.asaas.key_production')
            : config('services.asaas.key');

        return Http::timeout(60)
            ->withHeader('access_token', $asaasAccessToken ?? $defaultKey)
            ->{strtolower($method)}($baseUrl . $url, $payload)
            ->throw();
    }
}
