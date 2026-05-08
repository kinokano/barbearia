# Barbearia Turetta

Sistema de agendamento online — Barbearia Turetta.

## Stack

| Camada | Tecnologia |
|---|---|
| Backend | Laravel 13 (PHP 8.3+) |
| Frontend | Blade Templates + Tailwind CSS (CDN) |
| Banco de Dados | SQLite (dev) / MySQL 8+ (prod) |
| Hosting | cPanel / HostGator |

## Estrutura do Projeto

```
barbearia/
├── public_html/              ← Document root (Apache/cPanel)
│   ├── index.php             ← Entry point → bootstrap do Laravel
│   └── .htaccess
│
└── turetta/                  ← Core Laravel (fora do document root)
    ├── app/
    │   ├── Http/
    │   │   ├── Controllers/
    │   │   │   ├── Auth/LoginController.php
    │   │   │   ├── Admin/
    │   │   │   │   ├── DashboardController.php
    │   │   │   │   ├── ServiceController.php
    │   │   │   │   ├── ProfessionalController.php
    │   │   │   │   ├── ScheduleController.php
    │   │   │   │   └── ClientController.php
    │   │   │   ├── Professional/AgendaController.php
    │   │   │   └── Client/BookingController.php
    │   │   └── Middleware/RoleMiddleware.php
    │   └── Models/
    │       ├── Role.php
    │       ├── User.php
    │       ├── Service.php
    │       ├── Professional.php
    │       ├── Schedule.php
    │       └── Appointment.php
    ├── database/
    │   ├── migrations/
    │   └── seeders/
    ├── resources/views/
    │   ├── layouts/app.blade.php
    │   ├── auth/login.blade.php
    │   ├── admin/ (dashboard, services, professionals, schedules, clients)
    │   ├── professional/ (agenda, appointment-detail)
    │   └── client/ (booking step-by-step, success)
    └── routes/web.php
```

## Setup Local

```bash
# 1. Clone
git clone https://github.com/seu-usuario/barbearia.git
cd barbearia

# 2. Instale dependências
cd turetta && composer install && cd ..

# 3. Configure o .env
cp turetta/.env.example turetta/.env

# 4. Gere a key
cd turetta && php artisan key:generate && cd ..

# 5. Rode migrations + seed
cd turetta && php artisan migrate:fresh --seed && cd ..

# 6. Inicie o servidor
cd turetta && php artisan serve
```

## Credenciais de Acesso (Dev)

| Papel | E-mail | Senha |
|---|---|---|
| Admin | admin@turetta.com | turetta2026 |
| Profissional | joao@turetta.com | turetta2026 |

## Funcionalidades

### Booking Público (`/agendar`)
- Step 1: Escolher serviço
- Step 2: Escolher profissional (filtrado pelo serviço)
- Step 3: Escolher data e horário (AJAX, slots disponíveis)
- Step 4: Confirmar com dados pessoais

### Admin (`/admin`)
- Agenda global com filtro por data
- CRUD de Serviços, Profissionais e Horários
- CRM de Clientes com link WhatsApp
- Alteração de status de agendamentos (pendente/agendado/cancelado)

### Profissional (`/profissional`)
- Agenda pessoal filtrada por data
- Detalhes do agendamento com dados do cliente

## Deploy HostGator

1. Upload `turetta/` para `/home/user/turetta/`
2. Upload conteúdo de `public_html/` para `/home/user/public_html/`
3. Configure `.env` em `turetta/` com credenciais MySQL
4. Via SSH: `cd ~/turetta && php artisan migrate --seed`
5. Via SSH: `cd ~/turetta && php artisan key:generate`

## Licença

Proprietário — Barbearia Turetta. Todos os direitos reservados.
