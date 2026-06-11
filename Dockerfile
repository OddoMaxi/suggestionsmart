FROM php:8.3-fpm-alpine

# Dépendances système
RUN apk add --no-cache \
    nginx \
    mysql-client \
    libzip-dev \
    zip unzip git curl \
    icu-dev oniguruma-dev \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    supervisor

# Extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install \
        pdo_mysql mysqli \
        zip intl mbstring \
        opcache pcntl bcmath \
        gd

# Opcache production
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Dépendances Composer (ignore imagick — on utilise SVG)
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction \
    --ignore-platform-req=ext-imagick

# Application complète
COPY . .

# Autoload optimisé
RUN composer dump-autoload --optimize --no-dev

# Assets Filament
RUN php artisan filament:assets --no-interaction 2>/dev/null || true

# Dossiers + permissions
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions \
    storage/framework/views storage/app/public/qrcodes bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Config Nginx + Supervisor
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 10000

CMD ["/start.sh"]
