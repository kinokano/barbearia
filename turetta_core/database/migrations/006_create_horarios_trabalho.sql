-- ══════════════════════════════════════════════
-- Migration 006: Tabela de Horários de Trabalho
-- ══════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS horarios_trabalho (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profissional_id INT NOT NULL,
    dia_semana TINYINT NOT NULL COMMENT '0=Dom, 1=Seg, ..., 6=Sab',
    hora_inicio TIME NOT NULL,
    hora_fim TIME NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (profissional_id) REFERENCES profissionais(id) ON DELETE CASCADE,

    INDEX idx_profissional_dia (profissional_id, dia_semana),
    UNIQUE KEY uk_profissional_dia (profissional_id, dia_semana)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
