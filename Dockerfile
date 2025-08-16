FROM lfelipeapo/php-nginx-web:1.1.0

## Diretório da aplicação
ARG APP_DIR=/var/www
ARG NODE_ENV=production
ENV NODE_ENV=${NODE_ENV}

# Pré-reqs para instalar Node
RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates curl gnupg && \
    rm -rf /var/lib/apt/lists/*

# Node.js 20 (sempre; precisamos de npm tanto para build quanto para dev no runtime)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get update && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

# Copia o backend para o container (Render não usa volumes)
COPY ./applications/doc-viewer $APP_DIR/doc-viewer

# Copia o frontend
COPY ./frontend $APP_DIR/frontend

# Em produção, builda o frontend e copia para public
# Em desenvolvimento, NÃO roda dev no build (isso é runtime)
RUN if [ "$NODE_ENV" = "production" ]; then \
        cd $APP_DIR/frontend \
        && npm ci --include=dev \
        && npm run build \
        && cp -r dist/* $APP_DIR/doc-viewer/public/ \
        && rm -rf $APP_DIR/frontend; \
    else \
        echo "NODE_ENV=$NODE_ENV -> skip build do frontend no build; dev roda no runtime"; \
    fi

# Garante permissões
RUN mkdir -p $APP_DIR/doc-viewer/storage \
    && mkdir -p $APP_DIR/doc-viewer/bootstrap/cache \
    && chown -R www-data:www-data $APP_DIR/doc-viewer

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    apt-utils git curl unzip zip && \
    apt-get autoremove -y && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mysqli pdo pdo_mysql pdo_pgsql pgsql session xml zip iconv simplexml pcntl gd fileinfo

# Xdebug opcional
RUN pecl install xdebug && docker-php-ext-enable xdebug || true

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Instalar Composer dentro da imagem
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# carregar configuração padrão do NGINX
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/nginx/sites /etc/nginx/sites-available

# configuração do supervisor
RUN apt-get update && apt-get install -y supervisor htop && \
    apt-get autoremove -y && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Criar diretórios de log do supervisor
RUN mkdir -p /var/log/supervisor

# Criar diretórios de log do NGINX
RUN mkdir -p /var/log/nginx

COPY ./docker/supervisord/supervisord.conf /etc/supervisor
COPY ./docker/supervisord/conf /etc/supervisord.d/
# remover configs herdadas que apontam para /var/www/html
RUN rm -f /etc/supervisord.d/queue.conf || true

RUN [ ! -e /var/www/html ] && ln -s /var/www/doc-viewer /var/www/html || true

WORKDIR $APP_DIR
RUN chmod 777 -R *

# instalar ufw, copiar script
RUN apt-get update && apt-get install -y --no-install-recommends ufw && \
    apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY ./docker/ufw-start.sh /usr/local/bin/ufw-start.sh
RUN chmod +x /usr/local/bin/ufw-start.sh

# Sempre inicia com o Supervisor (php-fpm, nginx, queue, etc.)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]