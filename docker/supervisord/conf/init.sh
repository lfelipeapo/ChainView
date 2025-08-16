#!/usr/bin/env bash
set -e

APP_DIR=/var/www/doc-viewer
cd "$APP_DIR"

# Criar diretório de logs do supervisor
mkdir -p /var/log/supervisor

if [ ! -f "$APP_DIR/vendor/autoload.php" ]; then
    composer install -n --prefer-dist
fi

if [ ! -f "$APP_DIR/.env" ]; then
    cp .env.example .env 2>/dev/null || echo "Arquivo .env.example não encontrado, criando .env básico"
    # Criar .env básico se não existir
    cat > .env << EOF
APP_NAME=Laravel
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=${DB_HOST:-postgres}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE:-db}
DB_USERNAME=${DB_USERNAME:-postgres}
DB_PASSWORD=${DB_PASSWORD:-post123}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="\${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="\${APP_NAME}"
VITE_PUSHER_APP_KEY="\${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="\${PUSHER_HOST}"
VITE_PUSHER_PORT="\${PUSHER_PORT}"
VITE_PUSHER_SCHEME="\${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="\${PUSHER_APP_CLUSTER}"
EOF
fi

# Forçar reinicialização completa
echo "Reinicializando Laravel..."
rm -rf bootstrap/cache/*.php 2>/dev/null || true
rm -rf storage/framework/cache/* 2>/dev/null || true
rm -rf storage/framework/views/* 2>/dev/null || true
rm -rf storage/framework/sessions/* 2>/dev/null || true
rm -rf storage/logs/* 2>/dev/null || true

# Recriar diretórios
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Forçar permissões
chmod -R 777 storage bootstrap/cache

# Limpar cache
php artisan config:clear || echo "Erro ao limpar config cache"
php artisan cache:clear || echo "Erro ao limpar cache"
php artisan route:clear || echo "Erro ao limpar route cache"
php artisan view:clear || echo "Erro ao limpar view cache"
php artisan optimize:clear || echo "Erro ao limpar optimize cache"

# Gerar chave da aplicação se não existir
if ! grep -q "APP_KEY=base64:" .env; then
    php artisan key:generate --force || echo "Erro ao gerar chave da aplicação"
fi

# Debug: verificar configurações do banco
echo "=== DEBUG: Configurações do Banco ==="
echo "DB_CONNECTION: $DB_CONNECTION"
echo "DB_HOST: $DB_HOST"
echo "DB_PORT: $DB_PORT"
echo "DB_DATABASE: $DB_DATABASE"
echo "APP_ENV: $APP_ENV"
echo "DATABASE_URL: $DATABASE_URL"
echo "====================================="

# Se DATABASE_URL estiver definido, usar ele
if [ ! -z "$DATABASE_URL" ]; then
    echo "Usando DATABASE_URL para conexão..."
    # O Laravel já suporta DATABASE_URL nativamente, então vamos apenas
    # garantir que as variáveis estejam definidas para debug
    echo "DATABASE_URL configurado, Laravel irá usar automaticamente"
fi

# Testar conexão com o banco com timeout
echo "=== TESTE DE CONEXÃO ==="
timeout 30 bash -c '
until php -r "
try {
    require_once \"vendor/autoload.php\";
    \$app = require_once \"bootstrap/app.php\";
    \$app->make(\"Illuminate\Contracts\Console\Kernel\")->bootstrap();
    DB::connection()->getPdo();
    echo \"Conexão OK: \" . DB::connection()->getDatabaseName() . PHP_EOL;
    exit(0);
} catch (Exception \$e) {
    echo \"Erro de conexão: \" . \$e->getMessage() . PHP_EOL;
    exit(1);
}
"; do
    echo "Aguardando banco de dados..."
    sleep 2
done

# Executar migrations se banco estiver disponível
if php -r "
try {
    require_once 'vendor/autoload.php';
    \$app = require_once 'bootstrap/app.php';
    \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    DB::connection()->getPdo();
    exit(0);
} catch (Exception \$e) {
    exit(1);
}
"; then
    echo "Executando migrations..."
    php artisan migrate --force || echo "Erro ao executar migrations"
    echo "Executando seeders..."
    php artisan db:seed --force || echo "Erro ao executar seeders"
else
    echo "Banco de dados não disponível, pulando migrations e seeders"
fi

# Criar diretórios necessários novamente
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 777 storage bootstrap/cache

echo "Inicialização concluída!"
