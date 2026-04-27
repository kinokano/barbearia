<?php
/**
 * RelatorioService — Agregações para relatórios
 */
class RelatorioService
{
    /**
     * Faturamento total em um período
     */
    public static function faturamento(string $de, string $ate): float
    {
        $db = Connection::getInstance();
        $stmt = $db->prepare('
            SELECT COALESCE(SUM(s.preco), 0) as total
            FROM agendamentos a
            JOIN servicos s ON a.servico_id = s.id
            WHERE a.data BETWEEN ? AND ? AND a.status IN ("confirmado", "concluido")
        ');
        $stmt->execute([$de, $ate]);
        return (float) $stmt->fetch()['total'];
    }

    /**
     * Taxa de ocupação (% de slots usados vs total)
     */
    public static function taxaOcupacao(string $de, string $ate): float
    {
        $db = Connection::getInstance();

        // Contar agendamentos no período
        $stmt = $db->prepare('SELECT COUNT(*) as total FROM agendamentos WHERE data BETWEEN ? AND ? AND status != "cancelado"');
        $stmt->execute([$de, $ate]);
        $agendados = (int) $stmt->fetch()['total'];

        // Calcular slots totais (simplificado)
        $dias = (strtotime($ate) - strtotime($de)) / 86400 + 1;
        $slotsPerDay = 20; // Estimativa: 10h de funcionamento / 30min = 20 slots

        $stmt = $db->query('SELECT COUNT(*) as total FROM profissionais WHERE ativo = 1');
        $profissionais = (int) $stmt->fetch()['total'];

        $totalSlots = $dias * $slotsPerDay * max($profissionais, 1);

        return $totalSlots > 0 ? round(($agendados / $totalSlots) * 100, 1) : 0;
    }
}
