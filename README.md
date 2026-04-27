# Barbearia Turetta

Sistema de agendamentos online para a Barbearia Turetta. Frontend minimalista (preto & branco) com backend PHP puro, otimizado para deploy em HostGator (shared hosting).

## Stack

| Camada | Tecnologia |
|---|---|
| Frontend | HTML5, CSS3 (vanilla), JavaScript (ES6+) |
| Backend | PHP 7.4+ (sem framework) |
| Banco de Dados | MySQL 5.7+ / MariaDB |
| Hosting | cPanel / HostGator |

## Estrutura

```
barbearia/
├── public_html/        # Frontend (document root Apache)
├── turetta_core/       # Backend (fora do document root)
├── scripts/            # Deploy e automação
└── docs/               # Documentação
```

## Setup Local

```bash
# 1. Clone o repositório
git clone https://github.com/seu-usuario/barbearia.git
cd barbearia

# 2. Copie e configure o .env
cp turetta_core/.env.example turetta_core/.env
# Edite turetta_core/.env com suas credenciais de banco

# 3. Instale dependências PHP
cd turetta_core && composer install && cd ..

# 4. Execute as migrations
php scripts/migrate.php --seed

# 5. Inicie o servidor PHP local
php -S localhost:8000 -t public_html
```

## Deploy (HostGator)

**Opção 1: Via Setup Wizard (sem SSH)**
1. Faça upload de todos os arquivos via FTP (FileZilla)
2. Acesse `https://seudominio.com.br/scripts/setup-wizard.php`
3. Preencha os dados e clique em "Instalar"
4. Delete o arquivo `setup-wizard.php` após o setup

**Opção 2: Via Script (com SSH)**
```bash
DEPLOY_USER=usuario DEPLOY_HOST=seudominio.com.br bash scripts/deploy.sh
```

## Credenciais Padrão

| Campo | Valor |
|---|---|
| E-mail Admin | admin@turetta.com.br |
| Senha Admin | turetta@admin |

> ⚠️ **Altere a senha no primeiro acesso!**

## Licença

Proprietário — Barbearia Turetta. Todos os direitos reservados.
