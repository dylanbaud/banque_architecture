FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql intl zip opcache

WORKDIR /var/www/compte-service

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dépendances installées dans l'image (couche mise en cache tant que composer.* ne change pas)
COPY composer.json composer.lock ./
RUN composer install \
    --no-interaction \
    --prefer-dist \
    --no-scripts \
    --no-autoloader

COPY . .

RUN composer dump-autoload --no-scripts

EXPOSE 9000

# APP_SECRET généré au démarrage s'il n'est pas fourni (aucun secret versionné)
ENTRYPOINT ["sh", "-c", "export APP_SECRET=\"${APP_SECRET:-$(php -r 'echo bin2hex(random_bytes(16));')}\"; exec \"$@\"", "--"]
CMD ["php-fpm"]
