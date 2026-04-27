<?php
/**
 * TenantMiddleware — Carrega configuração do tenant ativo
 * 
 * ★ PONTO DE EXTENSÃO SAAS
 * Hoje: carrega config/tenant.php fixo.
 * Futuro: resolver tenant por subdomínio, header X-Tenant, ou path.
 */
class TenantMiddleware
{
    public static function handle()
    {
        // Hoje: tenant fixo (config/tenant.php já carregado no bootstrap)
        // Futuro: resolver tenant dinamicamente
        //
        // Exemplo de evolução:
        // $host = $_SERVER['HTTP_HOST'];
        // $subdomain = explode('.', $host)[0];
        // $tenant = DB::query("SELECT * FROM tenants WHERE slug = ?", [$subdomain]);
        // if (!$tenant) { Response::error('Barbearia não encontrada.', 404); }
        // $GLOBALS['config']['tenant'] = $tenant;

        return true;
    }
}
