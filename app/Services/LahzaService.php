<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LahzaService
{
    private string $publicKey;
    private string $secretKey;
    private string $currency;
    private string $baseUrl = 'https://api.lahza.io';

    public function __construct()
    {
        $this->publicKey = config('services.lahza.public_key') ?? '';
        $this->secretKey = config('services.lahza.secret_key') ?? '';
        $this->currency  = config('services.lahza.currency', 'ILS');
    }

    /**
     * Initialize a Lahza transaction for the given order.
     * Returns ['authorization_url' => ..., 'reference' => ...] on success.
     * Throws \RuntimeException on failure.
     */
    public function initialize(Order $order, string $callbackUrl): array
    {
        // Amount must be in smallest currency unit (× 100 for ILS)
        $amountInAgorot = (int) round($order->total * 100);

        $payload = [
            'email'        => $order->customer_email ?: 'customer@alfared.ps',
            'mobile'       => $order->customer_phone,
            'amount'       => $amountInAgorot,
            'currency'     => $this->currency,
            'ref'          => $order->order_number,
            'callback_url' => $callbackUrl,
            'metadata'     => [
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
                'customer'     => $order->customer_name,
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Cache-Control' => 'no-cache',
            'Content-Type'  => 'application/json',
        ])->post("{$this->baseUrl}/transaction/initialize", $payload);

        if (! $response->successful()) {
            Log::error('Lahza initialize failed', [
                'order'    => $order->order_number,
                'status'   => $response->status(),
                'body'     => $response->body(),
            ]);
            throw new \RuntimeException('فشل تهيئة الدفع. حاول مجدداً أو اختر الدفع عند الاستلام.');
        }

        $data = $response->json();

        if (! ($data['status'] ?? false)) {
            throw new \RuntimeException($data['message'] ?? 'فشل تهيئة الدفع.');
        }

        return [
            'authorization_url' => $data['data']['authorization_url'],
            'reference'         => $data['data']['reference'],
        ];
    }

    /**
     * Verify a Lahza transaction by reference.
     * Returns the full data array from Lahza.
     */
    public function verify(string $reference): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Cache-Control' => 'no-cache',
        ])->get("{$this->baseUrl}/transaction/verify/{$reference}");

        if (! $response->successful()) {
            Log::error('Lahza verify failed', [
                'reference' => $reference,
                'status'    => $response->status(),
                'body'      => $response->body(),
            ]);
            throw new \RuntimeException('فشل التحقق من الدفع.');
        }

        $data = $response->json();

        if (! ($data['status'] ?? false)) {
            throw new \RuntimeException($data['message'] ?? 'فشل التحقق من الدفع.');
        }

        return $data['data'];
    }

    /**
     * Validate the webhook signature from Lahza.
     */
    public function validateWebhookSignature(string $payload, string $signature): bool
    {
        $computed = hash_hmac('sha256', $payload, $this->secretKey);
        return hash_equals($computed, $signature);
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }
}
