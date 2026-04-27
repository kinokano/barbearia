#!/bin/bash
# ══════════════════════════════════════════════
# Barbearia Turetta — Deploy Script
# Para HostGator (cPanel / Shared Hosting)
# ══════════════════════════════════════════════

set -e

echo "═══════════════════════════════════════"
echo "  Barbearia Turetta — Deploy"
echo "═══════════════════════════════════════"
echo ""

# ── Variáveis ────────────────────────────────
REMOTE_USER="${DEPLOY_USER:-usuario}"
REMOTE_HOST="${DEPLOY_HOST:-seudominio.com.br}"
REMOTE_PATH="${DEPLOY_PATH:-/home/usuario}"
PUBLIC_PATH="${REMOTE_PATH}/public_html"
CORE_PATH="${REMOTE_PATH}/turetta_core"

# ── 1. Upload do Frontend ────────────────────
echo "→ [1/4] Enviando Frontend..."
rsync -avz --delete \
    --exclude='.DS_Store' \
    --exclude='Thumbs.db' \
    ./public_html/ \
    ${REMOTE_USER}@${REMOTE_HOST}:${PUBLIC_PATH}/

# ── 2. Upload do Backend ─────────────────────
echo "→ [2/4] Enviando Backend..."
rsync -avz --delete \
    --exclude='vendor/' \
    --exclude='.env' \
    --exclude='storage/logs/*' \
    --exclude='storage/cache/*' \
    ./turetta_core/ \
    ${REMOTE_USER}@${REMOTE_HOST}:${CORE_PATH}/

# ── 3. Instalar dependências ─────────────────
echo "→ [3/4] Instalando dependências PHP..."
ssh ${REMOTE_USER}@${REMOTE_HOST} "cd ${CORE_PATH} && php composer.phar install --no-dev --optimize-autoloader 2>/dev/null || echo 'Composer não encontrado. Instale manualmente.'"

# ── 4. Executar migrations ───────────────────
echo "→ [4/4] Executando migrations..."
ssh ${REMOTE_USER}@${REMOTE_HOST} "cd ${REMOTE_PATH} && php scripts/migrate.php"

# ── Permissões ───────────────────────────────
echo "→ Ajustando permissões..."
ssh ${REMOTE_USER}@${REMOTE_HOST} "chmod -R 755 ${PUBLIC_PATH} && chmod -R 775 ${CORE_PATH}/storage"

echo ""
echo "═══════════════════════════════════════"
echo "  Deploy concluído com sucesso! ✓"
echo "═══════════════════════════════════════"
