<?php
/**
 * AuthMiddleware — Verifica JWT válido
 */
class AuthMiddleware
{
    private static ?array $currentUser = null;

    /**
     * Executa a verificação de autenticação
     */
    public static function handle()
    {
        require_once APP_PATH . '/Services/AuthService.php';

        $token = self::extractToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Token de autenticação não fornecido.',
            ];
        }

        $payload = AuthService::validateToken($token);

        if (!$payload) {
            http_response_code(401);
            return [
                'success' => false,
                'message' => 'Token inválido ou expirado.',
            ];
        }

        // Armazena o usuário para uso nos controllers
        self::$currentUser = [
            'id'   => $payload['sub'],
            'nome' => $payload['nome'],
            'role' => $payload['role'],
        ];

        return true;
    }

    /**
     * Retorna o usuário autenticado
     */
    public static function getUser(): ?array
    {
        return self::$currentUser;
    }

    /**
     * Extrai o token do header Authorization: Bearer {token}
     */
    private static function extractToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION']
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            ?? '';

        if (preg_match('/Bearer\s+(.+)$/i', $header, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
