# syntax=docker/dockerfile:1

# ─────────────────────────── Stage 1: front-end assets ───────────────────────────
FROM node:24-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY vite.config.js ./
COPY resources ./resources
RUN npm run build

# ─────────────────────────── Stage 2: PHP dependencies ───────────────────────────
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction

COPY . .
RUN composer dump-autoload --optimize --no-dev

# ─────────────────────────── Stage 3: runtime ───────────────────────────
FROM php:8.3-fpm-alpine AS runtime

RUN apk add --no-cache \
        freetype \
        libjpeg-turbo \
        libpng \
        libzip \
        icu-libs \
        oniguruma \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libzip-dev \
        icu-dev \
        oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        gd \
        zip \
        intl \
        bcmath \
        opcache \
    && apk del .build-deps

COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-app.ini

WORKDIR /var/www/html

COPY --from=vendor /app /var/www/html
COPY --from=assets /app/public/build /var/www/html/public/build

# nginx runs in its own container with none of this app's code — it only
# gets `public/` via a shared volume (see docker-compose*.yml). A named
# volume auto-populates from the image the FIRST time it's empty, but that's
# a race between which container's (very different) image content wins, and
# it never repopulates on a later rebuild anyway. This snapshot is what
# entrypoint.sh copies into that volume on every single start instead,
# so nginx always has exactly what this image just built, deploy after deploy.
RUN cp -r /var/www/html/public /var/www/html/public-dist

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

EXPOSE 9000

ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]
