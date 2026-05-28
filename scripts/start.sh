#!/bin/bash
set -e

# Gera APP_KEY automaticamente se não estiver definida no Railway
if [ -z "$APP_KEY" ]; then
  export APP_KEY=$(php -r "echo 'base64:'.base64_encode(random_bytes(32));")
fi

mkdir -p storage/app/games storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

php artisan package:discover --ansi 2>/dev/null || true
php artisan config:clear 2>/dev/null || true

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
