# API — Barbearia Turetta

Base URL: `https://seudominio.com.br/api`

## Autenticação

Todas as rotas protegidas exigem header:
```
Authorization: Bearer {token}
```

---

## Rotas Públicas

### `GET /servicos`
Lista serviços ativos.

**Response:**
```json
{
    "success": true,
    "data": [
        { "id": 1, "nome": "Corte Masculino", "duracao_minutos": 40, "preco": 45.00 }
    ]
}
```

### `GET /profissionais`
Lista profissionais ativos.

### `GET /disponibilidade/:profissional_id/:data`
Retorna slots disponíveis.

**Exemplo:** `GET /disponibilidade/1/2026-04-20`

**Response:**
```json
{
    "success": true,
    "data": [
        { "time": "09:00", "available": true },
        { "time": "09:30", "available": false },
        { "time": "10:00", "available": true }
    ]
}
```

### `GET /config`
Retorna configuração pública do tenant (nome, logo, horários).

---

## Autenticação

### `POST /auth/login`
```json
{ "email": "user@email.com", "senha": "123456" }
```

### `POST /auth/register`
```json
{ "nome": "João", "email": "joao@email.com", "telefone": "11999999999", "senha": "123456" }
```

### `POST /auth/refresh`
```json
{ "refresh_token": "..." }
```

### `POST /auth/logout` 🔒
### `GET /auth/me` 🔒

---

## Agendamentos

### `POST /agendamentos` 🔒
```json
{ "profissional_id": 1, "servico_id": 1, "data": "2026-04-20", "hora": "10:00" }
```

---

## Admin 🔒🛡️

### `GET /admin/dashboard`
### `GET /admin/agendamentos?data=2026-04-20`
### `PUT /admin/agendamentos/:id` — `{ "status": "concluido" }`
### `DELETE /admin/agendamentos/:id`
### `GET /admin/clientes?busca=joao`
### `GET /admin/clientes/:id`
### `POST /admin/clientes`
### `PUT /admin/clientes/:id`
### `POST /admin/profissionais`
### `PUT /admin/profissionais/:id`
### `DELETE /admin/profissionais/:id`
### `POST /admin/servicos`
### `PUT /admin/servicos/:id`
### `DELETE /admin/servicos/:id`
### `GET /admin/relatorios?de=2026-04-01&ate=2026-04-30`
### `GET /admin/config`
### `PUT /admin/config`

---

**Legenda:** 🔒 Requer autenticação | 🛡️ Requer role admin
