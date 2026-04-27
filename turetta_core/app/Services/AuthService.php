<?php
/**
 * AuthService — JWT Token Management
 */
class AuthService
{
    /**
     * Gera token JWT
     */
    public static function generateToken(array $user): string
    {
        $secret = config('auth.secret');
        $ttl = config('auth.ttl', 3600);

        $header = self::base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = self::base64UrlEncode(json_encode([
            'sub'  => $user['id'],
            'nome' => $user['nome'],
            'role' => $user['role'],
            'iat'  => time(),
            'exp'  => time() + $ttl,
        ]));

        $signature = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", $secret, true)
        );

        return "$header.$payload.$signature";
    }

    /**
     * Gera refresh token
     */
    public static function generateRefreshToken(array $user): string
    {
        $secret = config('auth.secret');
        $ttl = config('auth.refresh_ttl', 604800);

        $header = self::base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = self::base64UrlEncode(json_encode([
            'sub'  => $user['id'],
            'type' => 'refresh',
            'iat'  => time(),
            'exp'  => time() + $ttl,
        ]));

        $signature = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", $secret, true)
        );

        return "$header.$payload.$signature";
    }

    /**
     * Valida token e retorna payload
     */
    public static function validateToken(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;

        [$header, $payload, $signature] = $parts;

        $secret = config('auth.secret');
        $expectedSig = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", $secret, true)
        );

        if (!hash_equals($expectedSig, $signature)) return null;

        $data = json_decode(self::base64UrlDecode($payload), true);
        if (!$data || !isset($data['exp']) || $data['exp'] < time()) return null;

        return $data;
    }

    /**
     * Valida refresh token
     */
    public static function validateRefreshToken(string $token): ?array
    {
        $payload = self::validateToken($token);
        if (!$payload || ($payload['type'] ?? '') !== 'refresh') return null;
        return $payload;
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
