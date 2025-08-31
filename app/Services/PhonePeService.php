<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PhonePeService
{
    private string $merchantId;
    private string $saltKey;
    private int $saltIndex;
    private string $apiEndpoint;
    private string $redirectUrl;
    private string $callbackUrl;

    public function __construct()
    {
        $this->merchantId = config('services.phonepe.merchant_id');
        $this->saltKey = config('services.phonepe.salt_key');
        $this->saltIndex = config('services.phonepe.salt_index', 1);
        $this->apiEndpoint = config('services.phonepe.api_endpoint');
        $this->redirectUrl = config('services.phonepe.redirect_url');
        $this->callbackUrl = config('services.phonepe.callback_url');
    }

    /**
     * Initiate payment using PhonePe API
     */
    public function initiatePayment(string $merchantOrderId, int $amount, ?string $redirectUrl = null): array
    {
        try {
            // Prepare payment request data
            $paymentData = [
                'merchantId' => $this->merchantId,
                'merchantTransactionId' => $merchantOrderId,
                'merchantUserId' => 'USER_' . time(),
                'amount' => $amount, // Amount should already be in paise
                'redirectUrl' => $redirectUrl ?: $this->redirectUrl,
                'redirectMode' => 'POST',
                'callbackUrl' => $this->callbackUrl,
                'paymentInstrument' => [
                    'type' => 'PAY_PAGE'
                ]
            ];

            // Encode payload
            $jsonPayload = json_encode($paymentData);
            $base64Payload = base64_encode($jsonPayload);

            // Generate checksum
            $checksum = hash('sha256', $base64Payload . '/pg/v1/pay' . $this->saltKey) . '###' . $this->saltIndex;

            // Make API request
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-VERIFY' => $checksum
            ])->post($this->apiEndpoint . '/pg/v1/pay', [
                'request' => $base64Payload
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                if ($responseData['success'] && isset($responseData['data']['instrumentResponse']['redirectInfo']['url'])) {
                    return [
                        'success' => true,
                        'payment_url' => $responseData['data']['instrumentResponse']['redirectInfo']['url'],
                        'order_id' => $responseData['data']['merchantTransactionId'] ?? null,
                        'merchant_order_id' => $merchantOrderId
                    ];
                }
            }

            Log::error('PhonePe payment initiation failed', [
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            return [
                'success' => false,
                'error' => 'Payment initialization failed'
            ];

        } catch (\Exception $e) {
            Log::error('PhonePe payment initiation error: ' . $e->getMessage(), [
                'merchant_order_id' => $merchantOrderId,
                'amount' => $amount
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check order status using PhonePe API
     */
    public function checkOrderStatus(string $merchantOrderId, bool $details = false): array
    {
        try {
            // Generate checksum for status check
            $checksum = hash('sha256', "/pg/v1/status/{$this->merchantId}/{$merchantOrderId}" . $this->saltKey) . '###' . $this->saltIndex;

            // Make API request
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-VERIFY' => $checksum,
                'X-MERCHANT-ID' => $this->merchantId
            ])->get($this->apiEndpoint . "/pg/v1/status/{$this->merchantId}/{$merchantOrderId}");

            if ($response->successful()) {
                $responseData = $response->json();
                
                if ($responseData['success']) {
                    return [
                        'success' => true,
                        'merchant_order_id' => $merchantOrderId,
                        'state' => $responseData['data']['state'] ?? 'UNKNOWN',
                        'amount' => $responseData['data']['amount'] ?? 0,
                        'transaction_details' => $responseData['data'] ?? [],
                        'payment_attempts' => $responseData['data']['paymentInstrument'] ?? []
                    ];
                }
            }

            Log::error('PhonePe order status check failed', [
                'response' => $response->json(),
                'status' => $response->status(),
                'merchant_order_id' => $merchantOrderId
            ]);

            return [
                'success' => false,
                'error' => 'Order status check failed'
            ];

        } catch (\Exception $e) {
            Log::error('PhonePe order status check error: ' . $e->getMessage(), [
                'merchant_order_id' => $merchantOrderId
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify callback response using PhonePe API
     */
    public function verifyCallback(string $responseBody, string $checksum): array
    {
        try {
            // Verify checksum
            $expectedChecksum = hash('sha256', $responseBody . $this->saltKey) . '###' . $this->saltIndex;
            
            if ($checksum !== $expectedChecksum) {
                Log::error('PhonePe callback checksum mismatch', [
                    'received' => $checksum,
                    'expected' => $expectedChecksum
                ]);
                
                return [
                    'success' => false,
                    'error' => 'Checksum verification failed'
                ];
            }

            // Decode response
            $decodedResponse = json_decode(base64_decode($responseBody), true);
            
            if (!$decodedResponse) {
                return [
                    'success' => false,
                    'error' => 'Invalid response format'
                ];
            }

            return [
                'success' => true,
                'merchant_order_id' => $decodedResponse['data']['merchantTransactionId'] ?? null,
                'transaction_status' => $decodedResponse['data']['state'] ?? 'UNKNOWN',
                'amount' => $decodedResponse['data']['amount'] ?? 0,
                'response_data' => $decodedResponse
            ];

        } catch (\Exception $e) {
            Log::error('PhonePe callback verification error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get merchant ID
     */
    public function getMerchantId(): string
    {
        return $this->merchantId;
    }
}
