<?php
/**
 * DashboardController — Dados do painel admin
 */
class DashboardController
{
    public function index(array $params, array $body): void
    {
        $db = Connection::getInstance();
        $hoje = DateHelper::today();

        // Agendamentos de hoje
        $stmt = $db->prepare('SELECT COUNT(*) as total FROM agendamentos WHERE data = ?');
        $stmt->execute([$hoje]);
        $agendamentosHoje = $stmt->fetch()['total'];

        // Agendamentos confirmados hoje
        $stmt = $db->prepare('SELECT COUNT(*) as total FROM agendamentos WHERE data = ? AND status = "confirmado"');
        $stmt->execute([$hoje]);
        $confirmadosHoje = $stmt->fetch()['total'];

        // Total de clientes
        $stmt = $db->query('SELECT COUNT(*) as total FROM usuarios WHERE role = "cliente" AND ativo = 1');
        $totalClientes = $stmt->fetch()['total'];

        // Faturamento do mês
        $mesAtual = date('Y-m');
        $stmt = $db->prepare('
            SELECT COALESCE(SUM(s.preco), 0) as total
            FROM agendamentos a
            JOIN servicos s ON a.servico_id = s.id
            WHERE a.data LIKE ? AND a.status IN ("confirmado", "concluido")
        ');
        $stmt->execute(["$mesAtual%"]);
        $faturamentoMes = $stmt->fetch()['total'];

        // Próximos agendamentos de hoje
        $stmt = $db->prepare('
            SELECT a.id, a.hora, a.status, u.nome as cliente_nome, s.nome as servico_nome, p.nome as profissional_nome
            FROM agendamentos a
            JOIN usuarios u ON a.cliente_id = u.id
            JOIN servicos s ON a.servico_id = s.id
            JOIN profissionais p ON a.profissional_id = p.id
            WHERE a.data = ? AND a.hora >= ?
            ORDER BY a.hora ASC
            LIMIT 10
        ');
        $stmt->execute([$hoje, DateHelper::now()]);
        $proximos = $stmt->fetchAll();

        Response::success([
            'agendamentos_hoje'  => (int) $agendamentosHoje,
            'confirmados_hoje'   => (int) $confirmadosHoje,
            'total_clientes'     => (int) $totalClientes,
            'faturamento_mes'    => (float) $faturamentoMes,
            'proximos'           => $proximos,
        ]);
    }
}
