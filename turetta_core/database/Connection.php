<?php
/**
 * Conexão com o Banco de Dados (Singleton PDO)
 */
class Connection
{
    private static ?PDO $instance = null;

    /**
     * Retorna a instância do PDO
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = $GLOBALS['config']['database'];

            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );

            try {
                self::$instance = new PDO($dsn, $config['username'], $config['password'], $config['options']);
            } catch (PDOException $e) {
                error_log('DB Connection Error: ' . $e->getMessage());
                Response::error('Erro de conexão com o banco de dados.', 500);
            }
        }

        return self::$instance;
    }

    /**
     * Previne clonagem
     */
    private function __clone() {}
}
