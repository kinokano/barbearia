-- ══════════════════════════════════════════════
-- Seed: Usuário Admin Padrão
-- Senha: turetta@admin (alterar no primeiro acesso!)
-- ══════════════════════════════════════════════

INSERT INTO usuarios (nome, email, telefone, senha, role, ativo, created_at)
VALUES (
    'Administrador',
    'admin@turetta.com.br',
    '11999999999',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: turetta@admin
    'admin',
    1,
    NOW()
);
