<?php

namespace App\Services;

use App\Models\User;
use Exception;

class JwtService
{
    /**
     * Secret key used for signing JWTs.
     */
    protected string $secret;

    /**
     * Default token expiration in seconds (24 hours).
     */
    protected int $ttl = 86400;

    public function __construct()
    {
        $this->secret = config('app.key') ?: 'nexus-secret-key-32-chars-length!';
    }

    /**
     * Generate a signed JWT for a user.
     */
    public function generateToken(User $user, array $customClaims = []): string
    {
        $now = time();
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT',
        ];

        $payload = array_merge([
            'iss' => config('app.url', 'http://localhost'),
            'sub' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'roles' => $user->roles ? $user->roles->pluck('name')->toArray() : [],
            'iat' => $now,
            'exp' => $now + $this->ttl,
        ], $customClaims);

        $base64Header = $this->base64UrlEncode(json_encode($header));
        $base64Payload = $this->base64UrlEncode(json_encode($payload));

        $signature = hash_hmac('sha256', "$base64Header.$base64Payload", $this->secret, true);
        $base64Signature = $this->base64UrlEncode($signature);

        return "$base64Header.$base64Payload.$base64Signature";
    }

    /**
     * Validate and decode a JWT token.
     *
     * @throws Exception
     */
    public function validateToken(string $jwt): array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            throw new Exception('Token con estructura inválida.');
        }

        [$base64Header, $base64Payload, $base64Signature] = $parts;

        $expectedSig = hash_hmac('sha256', "$base64Header.$base64Payload", $this->secret, true);
        if (!hash_equals($expectedSig, $this->base64UrlDecode($base64Signature))) {
            throw new Exception('Firma de token inválida.');
        }

        $payload = json_decode($this->base64UrlDecode($base64Payload), true);
        if (!$payload) {
            throw new Exception('Payload de token corrupto.');
        }

        if (isset($payload['exp']) && $payload['exp'] < time()) {
            throw new Exception('El token ha expirado.');
        }

        return $payload;
    }

    /**
     * Helper to base64url encode.
     */
    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Helper to base64url decode.
     */
    protected function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
