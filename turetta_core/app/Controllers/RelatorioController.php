<?php
/**
 * RelatorioController
 */
class RelatorioController
{
    public function index(array $params, array $body): void
    {
        $db = Connection::getInstance();

        $de = $_GET['de'] ?? date('Y-m-01');
        $ate = $_GET['ate'] ?? DateHelper::today();

        // Faturamento por dia
        $stmt = $db->prepare('
            SELECT a.data, COUNT(a.id) as total, COALESCE(SUM(s.preco), 0) as faturamento
            FROM agendamentos a
            JOIN servicos s ON a.servico_id = s.id
            WHERE a.data BETWEEN ? AND ? AND a.status IN ("confirmado", "concluido")
            GROUP BY a.data
            ORDER BY a.data ASC
        ');
        $stmt->execute([$de, $ate]);
        $porDia = $stmt->fetchAll();

        // Ranking de serviços
        $stmt = $db->prepare('
            SELECT s.nome, COUNT(a.id) as total, SUM(s.preco) as receita
            FROM agendamentos a
            JOIN servicos s ON a.servico_id = s.id
            WHERE a.data BETWEEN ? AND ? AND a.status IN ("confirmado", "concluido")
            GROUP BY s.id
            ORDER BY total DESC
        ');
        $stmt->execute([$de, $ate]);
        $porServico = $stmt->fetchAll();

        // Ranking de profissionais
        $stmt = $db->prepare('
            SELECT p.nome, COUNT(a.id) as total
            FROM agendamentos a
            JOIN profissionais p ON a.profissional_id = p.id
            WHERE a.data BETWEEN ? AND ? AND a.status IN ("confirmado", "concluido")
            GROUP BY p.id
            ORDER BY total DESC
        ');
        $stmt->execute([$de, $ate]);
        $porProfissional = $stmt->fetchAll();

        Response::success([
            'periodo'          => ['de' => $de, 'ate' => $ate],
            'por_dia'          => $porDia,
            'por_servico'      => $porServico,
            'por_profissional' => $porProfissional,
        ]);
    }
}
