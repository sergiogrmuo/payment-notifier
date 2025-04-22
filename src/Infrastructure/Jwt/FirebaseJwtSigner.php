<?php
namespace App\Infrastructure\Jwt;

use Firebase\JWT\JWT;

class FirebaseJwtSigner
{
    public function __construct(private string $secret) {}

    public function sign(array $payload): string
    {
        return JWT::encode($payload, $this->secret, 'HS256');
    }
}