<?php

namespace App\Services\General;

use Illuminate\Support\Facades\Http;

class MyfatoraService
{
    private $header;
    private $baseUrl;

    public function __construct()
    {
        $this->header = [
            'authorization' => 'Bearer ' . env('MYFATOORAH_API_KEY'),
        ];
        $this->baseUrl = env('MYFATOORAH_BASE_URL');
    }

    public function handelRequest($url, $data = [])
    {
        if (empty($data)) {
            return false;
        }
        $response = Http::withHeaders($this->header)
            ->acceptJson()
            ->timeout(30)
            ->withoutVerifying()
            ->post($this->baseUrl . $url, $data);
        if ($response->successful()) {
            return $response->json();
        }
        return false;
    }


    public function createInvoice($data)
    {
        return $this->handelRequest('SendPayment', $data);
    }

    public function checkInvoice($data)
    {
        return $this->handelRequest('GetPaymentStatus', $data);
    }
}
