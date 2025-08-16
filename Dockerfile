FROM lfelipeapo/php-nginx-web:1.1.0

## Diretório da aplicação
ARG APP_DIR=/var/www
ARG NODE_ENV=production

# Instalar Node.js e npm apenas em produção
RUN if [ "$NODE_ENV" = "production" ]; then \
        curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
        && apt-get install -y nodejs; \
    fi

# Copia o backend para o container (Render não usa volumes)
COPY ./applications/doc-viewer $APP_DIR/doc-viewer

# Em produção, builda o frontend e copia para public
RUN if [ "$NODE_ENV" = "production" ]; then \
        # Copia o frontend
        COPY ./frontend $APP_DIR/frontend \
        && cd $APP_DIR/frontend \
        && npm ci --only=production \
        && npm run build \
        && cp -r dist/* $APP_DIR/doc-viewer/public/ \
        && rm -rf $APP_DIR/frontend; \
    fi

# Garante permissões
RUN mkdir -p $APP_DIR/doc-viewer/storage \
    && mkdir -p $APP_DIR/doc-viewer/bootstrap/cache \
    && chown -R www-data:www-data $APP_DIR/doc-viewer

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    apt-utils && \
    apt-get autoremove -y && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mysqli pdo pdo_mysql pdo_pgsql pgsql session xml zip iconv simplexml pcntl gd fileinfo

# Xdebug desabilitado (a imagem base não possui xdebug). Se precisar, podemos instalar via PECL.
RUN pecl install xdebug && docker-php-ext-enable xdebug || true

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Cria system user para rodar Composer e Artisan Commands
WORKDIR $APP_DIR
RUN chmod 755 -R *

# Dependências para Composer e pacotes PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl unzip zip && \
    apt-get autoremove -y && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

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

COPY ./docker/supervisord/supervisord.conf /etc/supervisor
COPY ./docker/supervisord/conf /etc/supervisord.d/
# remover configs herdadas que apontam para /var/www/html
RUN rm -f /etc/supervisord.d/queue.conf || true

RUN [ ! -e /var/www/html ] && ln -s /var/www/doc-viewer /var/www/html || true

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]