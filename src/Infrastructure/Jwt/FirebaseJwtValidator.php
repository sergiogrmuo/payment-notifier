<?php
namespace App\Infrastructure\Jwt;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class FirebaseJwtValidator
{
    public function __construct(private string $secret) {}

    public function verify(string $token, array $payload): bool
    {
        $decoded = (array) JWT::decode($token, new Key($this->secret, 'HS256'));
        return $decoded === $payload;
    }
}