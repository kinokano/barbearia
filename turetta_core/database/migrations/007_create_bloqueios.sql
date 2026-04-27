-- ══════════════════════════════════════════════
-- Migration 007: Tabela de Bloqueios
-- ══════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS bloqueios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profissional_id INT NULL COMMENT 'NULL = bloqueio para todos',
    data DATE NOT NULL,
    hora_inicio TIME NULL COMMENT 'NULL = dia inteiro bloqueado',
    hora_fim TIME NULL,
    motivo VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (profissional_id) REFERENCES profissionais(id) ON DELETE CASCADE,

    INDEX idx_data (data),
    INDEX idx_profissional_data (profissional_id, data)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
