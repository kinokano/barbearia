# Deploy — Barbearia Turetta (HostGator)

## Pré-requisitos

- Plano de hospedagem com PHP 7.4+ e MySQL
- Acesso ao cPanel
- Domínio apontando para a HostGator

## Método 1: Setup Wizard (Sem SSH)

### 1. Upload dos Arquivos

1. Acesse o **Gerenciador de Arquivos** do cPanel
2. Faça upload dos arquivos:
   - `public_html/*` → Diretório `public_html` existente
   - `turetta_core/*` → Crie pasta `turetta_core` **ao lado** de `public_html` (mesmo nível)
   - `scripts/*` → Crie pasta `scripts` **ao lado** de `public_html`

### 2. Criar Banco de Dados

1. No cPanel, acesse **MySQL Databases**
2. Crie um banco: `turetta_db`
3. Crie um usuário: `turetta_user`
4. Associe o usuário ao banco com **ALL PRIVILEGES**

### 3. Executar Setup Wizard

1. Acesse: `https://seudominio.com.br/scripts/setup-wizard.php`
2. Preencha os dados do banco e admin
3. Clique em **Instalar**

### 4. Limpeza Pós-Setup

1. Delete o arquivo `scripts/setup-wizard.php` (segurança!)
2. Teste o site acessando `https://seudominio.com.br`

---

## Método 2: Via SSH (com terminal)

```bash
# SSH na HostGator
ssh usuario@seudominio.com.br

# Navegar para o diretório
cd ~

# Clonar repo (ou upload via FTP)
git clone https://github.com/seu-usuario/barbearia.git temp
cp -r temp/public_html/* ~/public_html/
cp -r temp/turetta_core ~/turetta_core
cp -r temp/scripts ~/scripts
rm -rf temp

# Configurar ambiente
cp turetta_core/.env.example turetta_core/.env
nano turetta_core/.env  # Editar credenciais

# Instalar dependências
cd turetta_core
php composer.phar install --no-dev
cd ..

# Executar migrations e seeds
php scripts/migrate.php --seed

# Permissões
chmod -R 755 public_html
chmod -R 775 turetta_core/storage
```

## Estrutura no Servidor

```
/home/usuario/
├── public_html/        ← Document root (Apache)
│   ├── api.php         ← Entry point da API
│   ├── .htaccess       ← Rewrite rules
│   └── assets/         ← CSS, JS, imagens
├── turetta_core/       ← Backend (NÃO acessível publicamente)
│   ├── .env            ← Variáveis de ambiente
│   ├── app/            ← Controllers, Models, Services
│   └── database/       ← Migrations, Seeds
└── scripts/            ← Automação
```

## Troubleshooting

| Problema | Solução |
|---|---|
| 500 Internal Server Error | Verificar `.htaccess` e versão do PHP no cPanel |
| API retorna 404 | Habilitar `mod_rewrite` no cPanel > Select PHP Version |
| Erro de conexão DB | Verificar credenciais no `.env` e no cPanel MySQL |
| Permissão negada em logs | `chmod 775 turetta_core/storage/logs` |
