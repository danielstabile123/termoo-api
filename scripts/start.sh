#!/bin/bash
# Script executado no Railway quando o container sobe.
# Prepara pastas, chave da app e inicia o servidor PHP.

set -e

# APP_KEY criptografa dados do Laravel — gera automaticamente se faltar no Railway
if [ -z "$APP_KEY" ]; then
  export APP_KEY=$(php -r "echo 'base64:'.base64_encode(random_bytes(32));")
fi

# Pastas que o Laravel precisa para logs, cache e arquivos de partida
mkdir -p storage/app/games storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

php artisan package:discover --ansi 2>/dev/null || true
php artisan config:clear 2>/dev/null || true

# Sobe servidor na porta que o Railway informa ($PORT)
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
