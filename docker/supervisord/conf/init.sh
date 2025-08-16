#!/usr/bin/env bash
set -e

APP_DIR=/var/www/doc-viewer
cd "$APP_DIR"

if [ ! -f "$APP_DIR/vendor/autoload.php" ]; then
    composer install -n --prefer-dist
fi

if [ ! -f "$APP_DIR/.env" ]; then
    cp .env.example .env
fi

# Configurações para produção
if [ "$APP_ENV" = "production" ]; then
    echo "Configurando para produção..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
else
    php artisan config:clear
    php artisan optimize:clear
fi

php artisan key:generate --force

# Debug: verificar configurações do banco
echo "=== DEBUG: Configurações do Banco ==="
echo "DB_CONNECTION: $DB_CONNECTION"
echo "DB_HOST: $DB_HOST"
echo "DB_PORT: $DB_PORT"
echo "DB_DATABASE: $DB_DATABASE"
echo "APP_ENV: $APP_ENV"
echo "====================================="

php artisan migrate --force
php artisan db:seed --force

# Criar diretórios necessários
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache
