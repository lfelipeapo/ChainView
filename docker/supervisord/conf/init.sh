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

php artisan key:generate --force
php artisan config:clear
php artisan optimize:clear
php artisan migrate --force || true

chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache
