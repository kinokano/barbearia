<?php
/**
 * Configuração do Tenant (Barbearia)
 * 
 * ★ PONTO DE EXTENSÃO SAAS
 * Hoje: arquivo estático com dados de uma barbearia.
 * Futuro: resolver por subdomínio/header → tabela `tenants` no DB.
 * 
 * Para cada nova barbearia no SaaS, basta criar/clonar este arquivo
 * com os dados do novo tenant.
 */
return [
    'id'   => 1,
    'slug' => 'turetta',

    // Branding
    'nome'      => 'Barbearia Turetta',
    'slogan'    => 'Estilo que define você.',
    'logo'      => '/assets/img/logo.svg',
    'logo_dark' => '/assets/img/logo-white.svg',

    // Contato
    'telefone'  => '(11) 99999-9999',
    'whatsapp'  => '5511999999999',
    'email'     => 'contato@turetta.com.br',
    'instagram' => '@barbeariatutetta',

    // Endereço
    'endereco' => [
        'rua'    => 'Rua Exemplo, 123',
        'bairro' => 'Centro',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
        'cep'    => '01001-000',
    ],

    // Horário de funcionamento padrão
    'horario_funcionamento' => [
        'segunda'  => ['09:00', '20:00'],
        'terca'    => ['09:00', '20:00'],
        'quarta'   => ['09:00', '20:00'],
        'quinta'   => ['09:00', '20:00'],
        'sexta'    => ['09:00', '20:00'],
        'sabado'   => ['09:00', '18:00'],
        'domingo'  => null, // Fechado
    ],

    // Intervalo de agendamento (em minutos)
    'intervalo_slot' => 30,

    // Antecedência mínima para agendar (em horas)
    'antecedencia_minima' => 2,

    // Antecedência máxima para agendar (em dias)
    'antecedencia_maxima' => 30,

    // Tema (preparação para customização SaaS)
    'tema' => [
        'cor_primaria'   => '#0A0A0A',
        'cor_secundaria' => '#FAFAFA',
        'fonte_titulo'   => 'Outfit',
        'fonte_corpo'    => 'Inter',
    ],
];
