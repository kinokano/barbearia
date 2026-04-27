<?php
/**
 * ClienteValidator
 */
class ClienteValidator
{
    public static function validate(array $data): array
    {
        $errors = [];

        if (empty($data['nome']) || strlen($data['nome']) < 2) {
            $errors['nome'] = 'Nome deve ter no mínimo 2 caracteres.';
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'E-mail inválido.';
        }

        if (empty($data['telefone']) || strlen($data['telefone']) < 10) {
            $errors['telefone'] = 'Telefone deve ter no mínimo 10 dígitos.';
        }

        return $errors;
    }
}
