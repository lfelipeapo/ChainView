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

# Forçar reinicialização completa
echo "Reinicializando Laravel..."
rm -rf bootstrap/cache/*.php
rm -rf storage/framework/cache/*
rm -rf storage/framework/views/*
rm -rf storage/framework/sessions/*
rm -rf storage/logs/*

# Recriar diretórios
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Limpar cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

php artisan key:generate --force

# Debug: verificar configurações do banco
echo "=== DEBUG: Configurações do Banco ==="
echo "DB_CONNECTION: $DB_CONNECTION"
echo "DB_HOST: $DB_HOST"
echo "DB_PORT: $DB_PORT"
echo "DB_DATABASE: $DB_DATABASE"
echo "APP_ENV: $APP_ENV"
echo "====================================="

# Testar conexão com o banco
echo "=== TESTE DE CONEXÃO ==="
php -r "
try {
    require_once 'vendor/autoload.php';
    \$app = require_once 'bootstrap/app.php';
    \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    DB::connection()->getPdo();
    echo 'Conexão OK: ' . DB::connection()->getDatabaseName() . PHP_EOL;
} catch (Exception \$e) {
    echo 'Erro de conexão: ' . \$e->getMessage() . PHP_EOL;
}
"
echo "========================="

# Migrations e seeders
php artisan migrate --force
php artisan db:seed --force

# Criar diretórios necessários
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R 777 storage bootstrap/cache
