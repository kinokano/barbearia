<?php
/**
 * Barbearia Turetta — Setup Wizard
 * 
 * Wizard de configuração inicial acessível via browser.
 * URL: https://seudominio.com.br/scripts/setup-wizard.php
 * 
 * Permite:
 * 1. Testar conexão com banco de dados
 * 2. Executar migrations
 * 3. Criar usuário admin
 * 4. Gerar .env
 */

// Segurança: apenas executa se .env NÃO existe (primeiro setup)
$envPath = dirname(__DIR__) . '/turetta_core/.env';
if (file_exists($envPath)) {
    die('<h1>Setup já realizado.</h1><p>Delete o arquivo .env para executar novamente.</p>');
}

$step = $_POST['step'] ?? 'form';
$message = '';

if ($step === 'install') {
    $dbHost = $_POST['db_host'] ?? 'localhost';
    $dbName = $_POST['db_name'] ?? '';
    $dbUser = $_POST['db_user'] ?? '';
    $dbPass = $_POST['db_pass'] ?? '';
    $adminEmail = $_POST['admin_email'] ?? '';
    $adminPass = $_POST['admin_pass'] ?? '';
    $appUrl = $_POST['app_url'] ?? '';
    $jwtSecret = bin2hex(random_bytes(32));

    // 1. Testar conexão
    try {
        $pdo = new PDO(
            "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
            $dbUser,
            $dbPass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    } catch (PDOException $e) {
        $message = "❌ Erro de conexão: " . $e->getMessage();
        $step = 'form';
    }

    if ($step === 'install') {
        // 2. Gerar .env
        $envContent = "APP_NAME=\"Barbearia Turetta\"\n";
        $envContent .= "APP_ENV=production\n";
        $envContent .= "APP_URL={$appUrl}\n";
        $envContent .= "APP_DEBUG=false\n\n";
        $envContent .= "DB_HOST={$dbHost}\nDB_PORT=3306\nDB_DATABASE={$dbName}\nDB_USERNAME={$dbUser}\nDB_PASSWORD={$dbPass}\nDB_CHARSET=utf8mb4\n\n";
        $envContent .= "JWT_SECRET={$jwtSecret}\nJWT_TTL=3600\nJWT_REFRESH_TTL=604800\n";

        file_put_contents($envPath, $envContent);

        // 3. Executar migrations
        $migrationsDir = dirname(__DIR__) . '/turetta_core/database/migrations';
        $files = glob($migrationsDir . '/*.sql');
        sort($files);

        $pdo->exec('CREATE TABLE IF NOT EXISTS migrations (id INT AUTO_INCREMENT PRIMARY KEY, migration VARCHAR(255) UNIQUE, executed_at DATETIME DEFAULT CURRENT_TIMESTAMP)');

        foreach ($files as $file) {
            $sql = file_get_contents($file);
            $pdo->exec($sql);
            $pdo->prepare('INSERT IGNORE INTO migrations (migration) VALUES (?)')->execute([basename($file)]);
        }

        // 4. Criar admin
        $senhaHash = password_hash($adminPass, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha, role, ativo, created_at) VALUES ("Administrador", ?, ?, "admin", 1, NOW())');
        $stmt->execute([$adminEmail, $senhaHash]);

        // 5. Seeds
        $seedsDir = dirname(__DIR__) . '/turetta_core/database/seeds';
        foreach (glob($seedsDir . '/*.sql') as $file) {
            try { $pdo->exec(file_get_contents($file)); } catch (Exception $e) { /* ignore duplicates */ }
        }

        $message = "✅ Setup concluído! Faça login em {$appUrl}/admin com {$adminEmail}";
        $step = 'done';
    }
}
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup — Barbearia Turetta</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #0A0A0A; color: #FAFAFA; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #171717; border: 1px solid #252525; border-radius: 16px; padding: 48px; max-width: 480px; width: 100%; }
        h1 { font-size: 24px; font-weight: 700; margin-bottom: 8px; }
        p { color: #888; font-size: 14px; margin-bottom: 32px; }
        label { display: block; font-size: 12px; text-transform: uppercase; letter-spacing: 0.04em; color: #888; margin-bottom: 8px; font-weight: 600; }
        input { width: 100%; padding: 12px 16px; background: #252525; border: 1px solid #383838; border-radius: 10px; color: #FAFAFA; font-size: 16px; margin-bottom: 20px; }
        input:focus { border-color: #FAFAFA; outline: none; }
        button { width: 100%; padding: 14px; background: #FAFAFA; color: #0A0A0A; border: none; border-radius: 10px; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; cursor: pointer; }
        button:hover { background: #D1D1D1; }
        .msg { padding: 12px 16px; border-radius: 10px; margin-bottom: 24px; font-size: 14px; }
        .msg--error { background: #EF44441A; color: #EF4444; border: 1px solid #EF44443A; }
        .msg--success { background: #22C55E1A; color: #22C55E; border: 1px solid #22C55E3A; }
    </style>
</head>
<body>
    <div class="card">
        <h1>⚙ Setup Wizard</h1>
        <p>Configure a Barbearia Turetta em poucos segundos.</p>

        <?php if ($message): ?>
            <div class="msg <?= $step === 'done' ? 'msg--success' : 'msg--error' ?>"><?= $message ?></div>
        <?php endif; ?>

        <?php if ($step !== 'done'): ?>
        <form method="POST">
            <input type="hidden" name="step" value="install">
            <label>URL do Site</label>
            <input name="app_url" placeholder="https://www.seudominio.com.br" required>
            <label>Host do Banco</label>
            <input name="db_host" value="localhost" required>
            <label>Nome do Banco</label>
            <input name="db_name" placeholder="turetta_db" required>
            <label>Usuário do Banco</label>
            <input name="db_user" placeholder="turetta_user" required>
            <label>Senha do Banco</label>
            <input name="db_pass" type="password" required>
            <hr style="border: none; border-top: 1px solid #252525; margin: 24px 0;">
            <label>E-mail Admin</label>
            <input name="admin_email" type="email" placeholder="admin@turetta.com.br" required>
            <label>Senha Admin</label>
            <input name="admin_pass" type="password" placeholder="Mínimo 6 caracteres" required>
            <button type="submit">Instalar</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
