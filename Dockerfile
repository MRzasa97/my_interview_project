FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libpq-dev \
    libonig-dev \
    && docker-php-ext-install \
    intl \
    pdo_mysql \
    mbstring \
    opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/symfony

COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install

RUN mkdir -p var/cache/dev/doctrine/orm/Proxies && \
    chown -R www-data:www-data var/cache && \
    chmod -R 775 var/cache

# Expose port 9000 and start php-fpm server
CMD ["php-fpm"]
