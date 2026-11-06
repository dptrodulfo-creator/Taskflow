FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --no-scripts

FROM node:22-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

FROM php:8.4-apache

RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor
COPY --from=frontend --chown=www-data:www-data /app/public/build ./public/build
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf

RUN mkdir -p storage/framework/cache storage/framework/sessions \
    storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && php artisan package:discover --ansi

EXPOSE 80
