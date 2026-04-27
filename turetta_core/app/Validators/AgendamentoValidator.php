<?php
/**
 * AgendamentoValidator
 */
class AgendamentoValidator
{
    public static function validate(array $data): array
    {
        $errors = [];

        if (empty($data['profissional_id'])) {
            $errors['profissional_id'] = 'Profissional é obrigatório.';
        }

        if (empty($data['servico_id'])) {
            $errors['servico_id'] = 'Serviço é obrigatório.';
        }

        if (empty($data['data']) || !DateHelper::isValid($data['data'])) {
            $errors['data'] = 'Data inválida.';
        } elseif (!DateHelper::isFuture($data['data']) && $data['data'] !== DateHelper::today()) {
            $errors['data'] = 'A data deve ser hoje ou no futuro.';
        }

        if (empty($data['hora']) || !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $data['hora'])) {
            $errors['hora'] = 'Horário inválido.';
        }

        // Verificar antecedência mínima
        if (!empty($data['data']) && !empty($data['hora'])) {
            if (!DateHelper::isDatetimeFuture($data['data'], $data['hora'])) {
                $errors['hora'] = 'O horário já passou.';
            }
        }

        return $errors;
    }
}
