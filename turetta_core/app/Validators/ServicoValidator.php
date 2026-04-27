<?php
/**
 * ServicoValidator
 */
class ServicoValidator
{
    public static function validate(array $data): array
    {
        $errors = [];

        if (empty($data['nome']) || strlen($data['nome']) < 2) {
            $errors['nome'] = 'Nome do serviço é obrigatório.';
        }

        if (empty($data['duracao_minutos']) || $data['duracao_minutos'] < 5) {
            $errors['duracao_minutos'] = 'Duração deve ser de no mínimo 5 minutos.';
        }

        if (!isset($data['preco']) || $data['preco'] < 0) {
            $errors['preco'] = 'Preço deve ser maior ou igual a zero.';
        }

        return $errors;
    }
}
