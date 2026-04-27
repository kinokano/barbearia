-- ══════════════════════════════════════════════
-- Seed: Serviços Padrão
-- ══════════════════════════════════════════════

INSERT INTO servicos (nome, descricao, duracao_minutos, preco, ativo, created_at) VALUES
('Corte Masculino', 'Corte moderno com acabamento na máquina e navalha. Inclui lavagem.', 40, 45.00, 1, NOW()),
('Barba', 'Modelagem com navalha, toalha quente e produtos premium.', 30, 35.00, 1, NOW()),
('Combo Completo', 'Corte + Barba com desconto. A experiência completa Turetta.', 60, 70.00, 1, NOW()),
('Corte Infantil', 'Corte para crianças até 12 anos com toda paciência e cuidado.', 30, 35.00, 1, NOW()),
('Sobrancelha', 'Design e limpeza de sobrancelha com navalha.', 15, 15.00, 1, NOW()),
('Pigmentação', 'Pigmentação capilar para cobertura de falhas.', 60, 80.00, 1, NOW());
