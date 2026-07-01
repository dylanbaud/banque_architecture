FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql intl zip opcache

WORKDIR /var/www/client-service

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

EXPOSE 9000

CMD ["php-fpm"]
