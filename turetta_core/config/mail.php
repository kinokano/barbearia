<?php
/**
 * Configuração de E-mail (SMTP)
 */
return [
    'host'         => env('MAIL_HOST', 'smtp.hostgator.com.br'),
    'port'         => (int) env('MAIL_PORT', 465),
    'username'     => env('MAIL_USERNAME', ''),
    'password'     => env('MAIL_PASSWORD', ''),
    'encryption'   => env('MAIL_ENCRYPTION', 'ssl'),
    'from_name'    => env('MAIL_FROM_NAME', 'Barbearia Turetta'),
    'from_address' => env('MAIL_FROM_ADDRESS', 'contato@turetta.com.br'),
];
