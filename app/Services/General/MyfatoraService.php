<?php

namespace App\Services\General;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MyfatoraService
{
    private array $header;

    private ?string $baseUrl;

    public function __construct()
    {
        $this->header = [
            'authorization' => 'Bearer ' . config('services.myfatoorah.api_key', env('MYFATOORAH_API_KEY')),
        ];
        $this->baseUrl = rtrim((string) config('services.myfatoorah.base_url', env('MYFATOORAH_BASE_URL', '')), '/') . '/';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function handelRequest(string $url, array $data = []): ?array
    {
        if ($data === [] || $this->baseUrl === '/') {
            Log::warning('MyFatoorah is not configured (missing base URL or API key)');

            return null;
        }

        $response = Http::withHeaders($this->header)
            ->acceptJson()
            ->timeout(30)
            ->withoutVerifying()
            ->post($this->baseUrl . $url, $data);

        if (!$response->successful()) {
            Log::warning('MyFatoorah HTTP error', [
                'url' => $url,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return null;
        }

        $body = $response->json();

        if (!is_array($body) || !($body['IsSuccess'] ?? false)) {
            Log::warning('MyFatoorah API error', [
                'url' => $url,
                'body' => $body,
            ]);

            return null;
        }

        return $body;
    }

    public function createInvoice(array $data): ?array
    {
        return $this->handelRequest('SendPayment', $data);
    }

    public function checkInvoice(array $data): ?array
    {
        return $this->handelRequest('GetPaymentStatus', $data);
    }
}
