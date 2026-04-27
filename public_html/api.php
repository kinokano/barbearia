<?php
/**
 * Barbearia Turetta — API Entry Point
 * 
 * Ponte entre o document root (public_html) e o backend (turetta_core).
 * Todas as requests /api/* são roteadas para cá pelo .htaccess.
 */

// Caminho para o core (fora do document root)
$corePath = dirname(__DIR__) . '/turetta_core';

// Bootstrap da aplicação
require_once $corePath . '/bootstrap.php';

// Roteamento
require_once $corePath . '/routes/api.php';
