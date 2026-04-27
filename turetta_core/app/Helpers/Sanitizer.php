<?php
/**
 * Sanitização de Inputs
 */
class Sanitizer
{
    /**
     * Sanitiza string removendo tags HTML e trimming
     */
    public static function string(?string $value): string
    {
        if ($value === null) return '';
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitiza email
     */
    public static function email(?string $value): string
    {
        return filter_var(trim($value ?? ''), FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitiza número inteiro
     */
    public static function int($value): int
    {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Sanitiza número decimal
     */
    public static function float($value): float
    {
        return (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * Extrai apenas dígitos (útil para telefone)
     */
    public static function digits(?string $value): string
    {
        return preg_replace('/\D/', '', $value ?? '');
    }

    /**
     * Sanitiza um array de dados
     */
    public static function all(array $data, array $rules): array
    {
        $sanitized = [];
        foreach ($rules as $key => $type) {
            $value = $data[$key] ?? null;
            switch ($type) {
                case 'string': $sanitized[$key] = self::string($value); break;
                case 'email':  $sanitized[$key] = self::email($value); break;
                case 'int':    $sanitized[$key] = self::int($value); break;
                case 'float':  $sanitized[$key] = self::float($value); break;
                case 'digits': $sanitized[$key] = self::digits($value); break;
                default:       $sanitized[$key] = self::string($value);
            }
        }
        return $sanitized;
    }
}
