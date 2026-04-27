<?php
/**
 * AdminMiddleware — Verifica role admin
 */
class AdminMiddleware
{
    public static function handle()
    {
        $user = AuthMiddleware::getUser();

        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            return [
                'success' => false,
                'message' => 'Acesso restrito a administradores.',
            ];
        }

        return true;
    }
}
