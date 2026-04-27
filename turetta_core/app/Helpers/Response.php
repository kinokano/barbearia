<?php
/**
 * Helper de Resposta JSON Padronizada
 */
class Response
{
    /**
     * Resposta de sucesso
     */
    public static function success($data = null, string $message = 'OK', int $code = 200): void
    {
        http_response_code($code);
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Resposta de erro
     */
    public static function error(string $message = 'Erro interno.', int $code = 400, $errors = null): void
    {
        http_response_code($code);
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Resposta de criação
     */
    public static function created($data = null, string $message = 'Criado com sucesso.'): void
    {
        self::success($data, $message, 201);
    }

    /**
     * Resposta sem conteúdo
     */
    public static function noContent(): void
    {
        http_response_code(204);
        exit;
    }

    /**
     * Não autorizado
     */
    public static function unauthorized(string $message = 'Não autorizado.'): void
    {
        self::error($message, 401);
    }

    /**
     * Proibido
     */
    public static function forbidden(string $message = 'Acesso negado.'): void
    {
        self::error($message, 403);
    }

    /**
     * Não encontrado
     */
    public static function notFound(string $message = 'Recurso não encontrado.'): void
    {
        self::error($message, 404);
    }

    /**
     * Validação falhou
     */
    public static function validationError(array $errors, string $message = 'Dados inválidos.'): void
    {
        self::error($message, 422, $errors);
    }
}
