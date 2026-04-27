<?php
/**
 * DisponibilidadeService — Cálculo de slots livres
 */
class DisponibilidadeService
{
    /**
     * Retorna os horários disponíveis de um profissional em uma data
     */
    public static function getAvailableSlots(int $profissionalId, string $data): array
    {
        $db = Connection::getInstance();
        $config = config('tenant');

        // Obter dia da semana
        $diaSemana = DateHelper::dayOfWeek($data);
        $horarioFuncionamento = $config['horario_funcionamento'][$diaSemana] ?? null;

        // Se não há horário de funcionamento (ex: domingo), retorna vazio
        if (!$horarioFuncionamento) {
            return [];
        }

        $intervalo = $config['intervalo_slot'];
        [$abertura, $fechamento] = $horarioFuncionamento;

        // Verificar horários de trabalho específicos do profissional
        $stmt = $db->prepare('
            SELECT hora_inicio, hora_fim
            FROM horarios_trabalho
            WHERE profissional_id = ? AND dia_semana = ? AND ativo = 1
        ');
        $stmt->execute([$profissionalId, date('w', strtotime($data))]);
        $horarioProfissional = $stmt->fetch();

        if ($horarioProfissional) {
            $abertura = $horarioProfissional['hora_inicio'];
            $fechamento = $horarioProfissional['hora_fim'];
        }

        // Gerar todos os slots possíveis
        $todosSlots = DateHelper::generateTimeSlots($abertura, $fechamento, $intervalo);

        // Buscar agendamentos existentes (não cancelados)
        $stmt = $db->prepare('
            SELECT a.hora, s.duracao_minutos
            FROM agendamentos a
            JOIN servicos s ON a.servico_id = s.id
            WHERE a.profissional_id = ?
              AND a.data = ?
              AND a.status NOT IN ("cancelado")
        ');
        $stmt->execute([$profissionalId, $data]);
        $agendamentos = $stmt->fetchAll();

        // Buscar bloqueios
        $stmt = $db->prepare('
            SELECT hora_inicio, hora_fim
            FROM bloqueios
            WHERE (profissional_id = ? OR profissional_id IS NULL)
              AND data = ?
        ');
        $stmt->execute([$profissionalId, $data]);
        $bloqueios = $stmt->fetchAll();

        // Marcar slots como disponíveis ou não
        $result = [];
        $agora = ($data === DateHelper::today()) ? date('H:i') : '00:00';
        $antecedencia = $config['antecedencia_minima'];

        foreach ($todosSlots as $slot) {
            $available = true;

            // Verificar se o slot já passou (hoje)
            if ($data === DateHelper::today()) {
                $slotComAntecedencia = date('H:i', strtotime($slot) - ($antecedencia * 3600));
                if ($agora > $slotComAntecedencia) {
                    $available = false;
                }
            }

            // Verificar conflito com agendamentos existentes
            foreach ($agendamentos as $ag) {
                $fimAg = DateHelper::addMinutes($ag['hora'], $ag['duracao_minutos']);
                if ($slot >= substr($ag['hora'], 0, 5) && $slot < substr($fimAg, 0, 5)) {
                    $available = false;
                    break;
                }
            }

            // Verificar bloqueios
            foreach ($bloqueios as $bloqueio) {
                if ($slot >= substr($bloqueio['hora_inicio'], 0, 5) && $slot < substr($bloqueio['hora_fim'], 0, 5)) {
                    $available = false;
                    break;
                }
            }

            $result[] = [
                'time'      => $slot,
                'available' => $available,
            ];
        }

        return $result;
    }
}
