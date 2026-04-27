# SaaS Roadmap — Barbearia Turetta

## Visão

Evoluir a plataforma de uma instância single-tenant (Barbearia Turetta) para um SaaS multi-tenant onde qualquer barbearia pode criar sua própria instância.

## Fases

### Fase 1: Single-Tenant (Atual) ✅
- [x] Frontend vanilla (HTML/CSS/JS)
- [x] Backend PHP puro
- [x] Config isolada em `config/tenant.php`
- [x] Branding centralizado em `tokens.css`
- [x] Deploy HostGator shared hosting

### Fase 2: Multi-Config
- [ ] Migrar `config/tenant.php` para tabela `tenants` no banco
- [ ] `TenantMiddleware` resolve tenant por subdomínio
- [ ] Frontend carrega branding dinamicamente via `GET /config`
- [ ] `tokens.css` gerado via API (CSS custom properties injetadas)

### Fase 3: Multi-Tenant Isolado
- [ ] Tabela `tenants` com slug, plano, dados de branding
- [ ] Toda tabela recebe coluna `tenant_id` (FK)
- [ ] Middleware injeta `WHERE tenant_id = ?` automaticamente
- [ ] Painel super-admin para gerenciar tenants
- [ ] Subdomínios: `turetta.agendabarbearia.com.br`

### Fase 4: Onboarding Self-Service
- [ ] Landing page SaaS com planos
- [ ] Formulário de cadastro de nova barbearia
- [ ] Provisionamento automático (DB, subdomínio, branding)
- [ ] Integração de pagamento (Stripe/PagSeguro)

### Fase 5: Funcionalidades Premium
- [ ] Notificação WhatsApp (API oficial ou Twilio)
- [ ] App mobile (PWA)
- [ ] Programa de fidelidade (a cada N cortes = 1 grátis)
- [ ] Integração com Google Calendar
- [ ] Avaliação pós-atendimento
- [ ] Relatórios avançados com gráficos

## Pontos de Extensão Atuais

| Arquivo | Responsabilidade | Evolução SaaS |
|---|---|---|
| `config/tenant.php` | Dados da barbearia | → Tabela `tenants` |
| `TenantMiddleware.php` | Carrega tenant fixo | → Resolve por subdomínio |
| `tokens.css` | Design tokens P&B | → Gerado dinamicamente |
| `Navbar.render()` | Exibe logo/nome fixo | → Carrega do tenant via API |

## Estimativa de Esforço

| Fase | Complexidade | Tempo Estimado |
|---|---|---|
| Fase 2 | Baixa | 1-2 semanas |
| Fase 3 | Média | 3-4 semanas |
| Fase 4 | Alta | 4-6 semanas |
| Fase 5 | Variável | Contínuo |
