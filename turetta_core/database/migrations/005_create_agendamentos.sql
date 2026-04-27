-- ══════════════════════════════════════════════
-- Migration 005: Tabela de Agendamentos
-- ══════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    profissional_id INT NOT NULL,
    servico_id INT NOT NULL,
    data DATE NOT NULL,
    hora TIME NOT NULL,
    status ENUM('confirmado', 'concluido', 'cancelado', 'nao_compareceu') NOT NULL DEFAULT 'confirmado',
    observacoes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (cliente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (profissional_id) REFERENCES profissionais(id) ON DELETE RESTRICT,
    FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE RESTRICT,

    INDEX idx_data (data),
    INDEX idx_profissional_data (profissional_id, data),
    INDEX idx_cliente (cliente_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
