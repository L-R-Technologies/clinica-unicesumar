#!/bin/sh
set -e

cd /var/www/html

mkdir -p \
  storage/framework/cache \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  storage/app/public \
  storage/app/private \
  bootstrap/cache

echo "Aguardando o banco de dados em ${DB_HOST}:${DB_PORT:-3306}..."
attempt=0
until php -r 'exit(@fsockopen(getenv("DB_HOST"), (int)(getenv("DB_PORT") ?: 3306)) ? 0 : 1);'; do
  attempt=$((attempt + 1))
  if [ "$attempt" -ge 30 ]; then
    echo "Banco de dados indisponível após $attempt tentativas." >&2
    exit 1
  fi
  sleep 2
done

php artisan migrate --force

php artisan package:discover --ansi
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan storage:link 2>/dev/null || true

chown -R www-data:www-data storage bootstrap/cache

exec "$@"
