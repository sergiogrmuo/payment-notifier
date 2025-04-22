<?php
namespace App\Infrastructure\Http;

use App\Infrastructure\Jwt\FirebaseJwtValidator;

class RequestParser
{
    public function __construct(private FirebaseJwtValidator $validator) {}

    public function parse(string $rawBody, array $headers): array
    {
        $payload = json_decode($rawBody, true);
        $signature = $headers['Signature'] ?? null;

        if (!$signature) {
            throw new \InvalidArgumentException("Falta la cabecera Signature");
        }

        if (!$this->validator->verify($signature, $payload)) {
            throw new \RuntimeException("La firma JWT no es válida");
        }

        return $payload;
    }
}