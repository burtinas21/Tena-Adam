<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class ChapaService
{
    private string $baseUrl;

    private string $secretKey;

    public function __construct()
    {
        $this->baseUrl = config('chapa.base_url');
        $this->secretKey = config('chapa.secret_key');
    }

    /**
     * ------------------------------------------------------------
     * HTTP Client
     * ------------------------------------------------------------
     */
    private function client()
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type'  => 'application/json',
        ]);
    }

    /**
     * ------------------------------------------------------------
     * Initialize Payment
     * ------------------------------------------------------------
     */
    public function initializePayment(array $paymentData)
    {
        $response = $this->client()->post(
            "{$this->baseUrl}/transaction/initialize",
            $paymentData
        );

        if ($response->successful()) {
            return $response->json();
        }

        throw new Exception(
            $response->body()
        );
    }

    /**
     * ------------------------------------------------------------
     * Verify Payment
     * ------------------------------------------------------------
     */
    public function verifyPayment(string $transactionReference)
    {
        $response = $this->client()->get(
            "{$this->baseUrl}/transaction/verify/{$transactionReference}"
        );

        if ($response->successful()) {
            return $response->json();
        }

        throw new Exception(
            $response->body()
        );
    }

    /**
     * ------------------------------------------------------------
     * Get Transaction
     * ------------------------------------------------------------
     */
    public function getTransaction(string $transactionReference)
    {
        $response = $this->client()->get(
            "{$this->baseUrl}/transaction/verify/{$transactionReference}"
        );

        if ($response->successful()) {
            return $response->json();
        }

        throw new Exception(
            $response->body()
        );
    }

    /**
     * ------------------------------------------------------------
     * Refund Payment
     * ------------------------------------------------------------
     */
    public function refundPayment(array $refundData)
    {
        $response = $this->client()->post(
            "{$this->baseUrl}/refund",
            $refundData
        );

        if ($response->successful()) {
            return $response->json();
        }

        throw new Exception(
            $response->body()
        );
    }
}