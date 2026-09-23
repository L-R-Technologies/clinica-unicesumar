# syntax=docker/dockerfile:1

# ---------- vendor (Composer) ----------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN --mount=type=cache,target=/tmp/cache composer install \
      --no-dev --no-scripts --no-autoloader \
      --prefer-dist --no-interaction --no-progress \
      --ignore-platform-req=php
COPY . .
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative

# ---------- assets (Vite: React + Inertia) ----------
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN --mount=type=cache,target=/root/.npm npm ci --no-audit --no-fund
COPY . .
ARG VITE_APP_NAME="Clínica Unicesumar"
ENV VITE_APP_NAME=${VITE_APP_NAME}
RUN npm run build

# ---------- runtime (PHP-FPM + Nginx + Supervisor) ----------
FROM php:8.4-fpm-alpine AS runtime

COPY --from=ghcr.io/mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
      pdo_mysql mbstring bcmath exif pcntl zip intl gd opcache

RUN apk add --no-cache nginx supervisor tzdata

ENV TZ=America/Sao_Paulo

WORKDIR /var/www/html

COPY --from=vendor --chown=www-data:www-data /app /var/www/html
COPY --from=assets --chown=www-data:www-data /app/public/build /var/www/html/public/build

COPY docker/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/fpm-logging.conf /usr/local/etc/php-fpm.d/zz-logging.conf
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh \
    && mkdir -p \
       storage/framework/cache \
       storage/framework/sessions \
       storage/framework/views \
       storage/logs \
       storage/app/public \
       storage/app/private \
       bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
  CMD wget -qO- http://127.0.0.1/up >/dev/null 2>&1 || exit 1

ENTRYPOINT ["entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
