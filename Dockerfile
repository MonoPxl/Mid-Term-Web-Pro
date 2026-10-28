# Build Laravel's Vite/Tailwind assets.
FROM node:24-bookworm-slim AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# Run Laravel with PHP-FPM + Nginx on Render.
FROM php:8.3-fpm-alpine AS app

RUN apk add --no-cache bash curl nginx supervisor icu-libs libcurl libzip libxml2 oniguruma sqlite-libs \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS curl-dev libzip-dev libxml2-dev icu-dev oniguruma-dev sqlite-dev \
    && docker-php-ext-install bcmath curl dom exif intl mbstring opcache pcntl pdo_sqlite xml zip \
    && apk del .build-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

WORKDIR /var/www/html
COPY . .
COPY --from=frontend /app/public/build ./public/build
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/start.sh /start.sh

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/var/www/html/storage/app/myitsbroadcast.sqlite \
    DB_FOREIGN_KEYS=true \
    SESSION_DRIVER=file \
    CACHE_STORE=file \
    QUEUE_CONNECTION=sync \
    FILESYSTEM_DISK=public \
    MAIL_MAILER=log \
    COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x /start.sh

EXPOSE 10000
CMD ["/start.sh"]
