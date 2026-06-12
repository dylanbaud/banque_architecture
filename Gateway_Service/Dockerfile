FROM php:8.4-fpm

WORKDIR /var/www/api-gateway

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

RUN mkdir -p logs

EXPOSE 9000

CMD ["php-fpm"]