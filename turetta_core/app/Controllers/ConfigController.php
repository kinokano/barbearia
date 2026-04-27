<?php
/**
 * ConfigController — Configurações do tenant
 */
class ConfigController
{
    /**
     * Config pública (nome, logo, horários) — sem auth
     */
    public function public(array $params, array $body): void
    {
        $tenant = config('tenant');

        Response::success([
            'nome'                  => $tenant['nome'],
            'slogan'                => $tenant['slogan'],
            'logo'                  => $tenant['logo'],
            'telefone'              => $tenant['telefone'],
            'whatsapp'              => $tenant['whatsapp'],
            'endereco'              => $tenant['endereco'],
            'horario_funcionamento' => $tenant['horario_funcionamento'],
            'intervalo_slot'        => $tenant['intervalo_slot'],
        ]);
    }

    /**
     * Config completa (admin)
     */
    public function index(array $params, array $body): void
    {
        $db = Connection::getInstance();
        $stmt = $db->query('SELECT chave, valor FROM configuracoes ORDER BY chave ASC');
        $configs = $stmt->fetchAll();

        // Merge com config do arquivo
        $tenant = config('tenant');

        Response::success([
            'tenant' => $tenant,
            'db'     => $configs,
        ]);
    }

    /**
     * Atualizar configuração (admin)
     */
    public function update(array $params, array $body): void
    {
        $db = Connection::getInstance();

        foreach ($body as $chave => $valor) {
            $chave = Sanitizer::string($chave);
            $valor = Sanitizer::string($valor);

            $stmt = $db->prepare('
                INSERT INTO configuracoes (chave, valor, updated_at) VALUES (?, ?, NOW())
                ON DUPLICATE KEY UPDATE valor = ?, updated_at = NOW()
            ');
            $stmt->execute([$chave, $valor, $valor]);
        }

        Response::success(null, 'Configurações atualizadas.');
    }
}
