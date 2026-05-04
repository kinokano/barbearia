# Barbearia Turetta

Sistema de agendamento online — Barbearia Turetta.

## Stack

| Camada | Tecnologia |
|---|---|
| Frontend | HTML5, CSS3, JavaScript (ES6+) |
| Backend | Laravel 10 (PHP 8.1+) |
| Banco de Dados | MySQL 8+ / MariaDB 10.4+ |
| Hosting | cPanel / HostGator |

## Estrutura

```
barbearia/
├── public_html/              ← Document root (Apache/cPanel)
│   ├── index.html            ← Página de agendamento
│   ├── admin.html            ← Painel administrativo
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   ├── agendamento.js
│   │   └── admin.js
│   └── api/
│       ├── index.php         ← Entry point Laravel
│       └── .htaccess
│
└── turetta_core/             ← Core Laravel (fora do document root)
    ├── app/
    │   ├── Console/
    │   ├── Exceptions/
    │   ├── Http/
    │   │   ├── Controllers/
    │   │   │   ├── AdminController.php
    │   │   │   ├── AppointmentController.php
    │   │   │   ├── ProfessionalController.php
    │   │   │   └── ServiceController.php
    │   │   └── Kernel.php
    │   ├── Models/
    │   │   ├── Appointment.php
    │   │   ├── Professional.php
    │   │   ├── Service.php
    │   │   └── User.php
    │   └── Providers/
    ├── bootstrap/
    ├── config/
    ├── database/
    │   ├── migrations/
    │   └── seeders/
    ├── routes/
    │   └── api.php
    ├── .env.example
    ├── artisan
    └── composer.json
```

## Setup Local

```bash
# 1. Clone
git clone https://github.com/seu-usuario/barbearia.git
cd barbearia

# 2. Instale dependências
cd turetta_core && composer install && cd ..

# 3. Configure o .env
cp turetta_core/.env.example turetta_core/.env
# Edite turetta_core/.env com credenciais do banco

# 4. Gere a key
cd turetta_core && php artisan key:generate && cd ..

# 5. Rode migrations + seed
cd turetta_core && php artisan migrate --seed && cd ..

# 6. Inicie o servidor
php -S localhost:8000 -t public_html
```

## API Endpoints

| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/professionals` | Lista profissionais |
| GET | `/api/services` | Lista serviços |
| GET | `/api/slots?data=YYYY-MM-DD&professional_id=N` | Horários disponíveis |
| POST | `/api/appointments` | Criar agendamento |
| GET | `/api/admin/appointments?data=YYYY-MM-DD` | Agenda do dia |
| GET | `/api/admin/clients` | Lista de clientes |
| PATCH | `/api/admin/appointments/{id}/status` | Atualizar status |

## Deploy HostGator

1. Faça upload de `turetta_core/` para fora do `public_html` (ex: `/home/user/turetta_core/`)
2. Faça upload do conteúdo de `public_html/` para `/home/user/public_html/`
3. Edite `public_html/api/index.php` — ajuste o caminho do autoload:
   ```php
   require __DIR__.'/../../turetta_core/vendor/autoload.php';
   $app = require_once __DIR__.'/../../turetta_core/bootstrap/app.php';
   ```
4. Configure `.env` em `turetta_core/` com credenciais MySQL do cPanel
5. Execute via SSH: `cd ~/turetta_core && php artisan migrate --seed`
6. Gere a key: `cd ~/turetta_core && php artisan key:generate`

## Licença

Proprietário — Barbearia Turetta. Todos os direitos reservados.
