<?php
namespace App\Infrastructure\Http;

use App\Domain\Entity\Payment;
use App\Domain\Port\NotifierInterface;
use GuzzleHttp\Client;
use App\Infrastructure\Jwt\FirebaseJwtSigner;

class GuzzlePaymentNotifier implements NotifierInterface
{
    private Client $client;

    public function __construct(
        private string $endpoint,
        private FirebaseJwtSigner $signer
    ) {
        $this->client = new Client();
    }

    public function notify(Payment $payment): bool
    {
        $payload = $payment->toArray();
        $signature = $this->signer->sign($payload);

        $response = $this->client->post($this->endpoint, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Signature' => $signature,
            ],
            'json' => $payload,
            'verify' => false,
        ]);

        return $response->getStatusCode() === 200;
    }
}