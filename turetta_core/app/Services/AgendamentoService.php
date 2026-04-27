<?php
/**
 * AgendamentoService — Regras de negócio de agendamento
 */
class AgendamentoService
{
    /**
     * Verifica conflito de horário
     * 
     * Um horário está em conflito se já existe um agendamento para o mesmo
     * profissional na mesma data cujo intervalo [hora, hora+duração] sobrepõe.
     */
    public static function checkConflict(int $profissionalId, string $data, string $hora, int $servicoId): bool
    {
        $db = Connection::getInstance();

        // Obter duração do serviço solicitado
        $stmt = $db->prepare('SELECT duracao_minutos FROM servicos WHERE id = ?');
        $stmt->execute([$servicoId]);
        $servico = $stmt->fetch();
        if (!$servico) return true; // Serviço inválido = conflito

        $duracaoNovo = $servico['duracao_minutos'];
        $fimNovo = DateHelper::addMinutes($hora, $duracaoNovo);

        // Buscar agendamentos existentes do profissional na data
        $stmt = $db->prepare('
            SELECT a.hora, s.duracao_minutos
            FROM agendamentos a
            JOIN servicos s ON a.servico_id = s.id
            WHERE a.profissional_id = ?
              AND a.data = ?
              AND a.status NOT IN ("cancelado")
        ');
        $stmt->execute([$profissionalId, $data]);
        $existentes = $stmt->fetchAll();

        // Verificar sobreposição
        foreach ($existentes as $existente) {
            $inicioExistente = $existente['hora'];
            $fimExistente = DateHelper::addMinutes($inicioExistente, $existente['duracao_minutos']);

            // Sobreposição: novo_inicio < existente_fim && novo_fim > existente_inicio
            if ($hora < $fimExistente && $fimNovo > $inicioExistente) {
                return true; // Conflito!
            }
        }

        return false;
    }

    /**
     * Cria um agendamento
     */
    public static function create(array $data): array
    {
        $db = Connection::getInstance();

        $stmt = $db->prepare('
            INSERT INTO agendamentos (cliente_id, profissional_id, servico_id, data, hora, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ');
        $stmt->execute([
            $data['cliente_id'],
            $data['profissional_id'],
            $data['servico_id'],
            $data['data'],
            $data['hora'],
            $data['status'],
        ]);

        $id = $db->lastInsertId();

        // Retornar o agendamento com dados relacionados
        $stmt = $db->prepare('
            SELECT a.*, u.nome as cliente_nome, p.nome as profissional_nome, s.nome as servico_nome, s.preco
            FROM agendamentos a
            JOIN usuarios u ON a.cliente_id = u.id
            JOIN profissionais p ON a.profissional_id = p.id
            JOIN servicos s ON a.servico_id = s.id
            WHERE a.id = ?
        ');
        $stmt->execute([$id]);

        return $stmt->fetch();
    }
}
