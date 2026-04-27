<?php
/**
 * Barbearia Turetta — Migration Runner
 * 
 * Executa migrations SQL pendentes em ordem numérica.
 * Uso: php migrate.php
 */

// Bootstrap
$corePath = dirname(__DIR__) . '/turetta_core';
require_once $corePath . '/bootstrap.php';

echo "═══════════════════════════════════════\n";
echo "  Barbearia Turetta — Migrations\n";
echo "═══════════════════════════════════════\n\n";

$db = Connection::getInstance();

// Criar tabela de controle de migrations
$db->exec('
    CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL UNIQUE,
        executed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
');

// Listar migrations já executadas
$stmt = $db->query('SELECT migration FROM migrations ORDER BY migration');
$executed = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Listar arquivos de migration
$migrationsDir = $corePath . '/database/migrations';
$files = glob($migrationsDir . '/*.sql');
sort($files);

$pending = 0;

foreach ($files as $file) {
    $filename = basename($file);

    if (in_array($filename, $executed)) {
        echo "  ✓ {$filename} (já executada)\n";
        continue;
    }

    echo "  → Executando {$filename}... ";

    try {
        $sql = file_get_contents($file);
        $db->exec($sql);

        $stmt = $db->prepare('INSERT INTO migrations (migration) VALUES (?)');
        $stmt->execute([$filename]);

        echo "OK ✓\n";
        $pending++;
    } catch (PDOException $e) {
        echo "ERRO ✕\n";
        echo "    " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "\n";
if ($pending === 0) {
    echo "  Nenhuma migration pendente.\n";
} else {
    echo "  {$pending} migration(s) executada(s) com sucesso.\n";
}

// Perguntar se deseja executar seeds
if (isset($argv[1]) && $argv[1] === '--seed') {
    echo "\n── Seeds ──────────────────────────────\n\n";

    $seedsDir = $corePath . '/database/seeds';
    $seedFiles = glob($seedsDir . '/*.sql');
    sort($seedFiles);

    foreach ($seedFiles as $file) {
        $filename = basename($file);
        echo "  → Executando {$filename}... ";

        try {
            $sql = file_get_contents($file);
            $db->exec($sql);
            echo "OK ✓\n";
        } catch (PDOException $e) {
            echo "AVISO: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n═══════════════════════════════════════\n";
echo "  Concluído!\n";
echo "═══════════════════════════════════════\n";
