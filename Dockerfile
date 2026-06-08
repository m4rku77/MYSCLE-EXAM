FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev

RUN docker-php-ext-install pdo_mysql zip

WORKDIR /app

COPY . .

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

RUN composer install

EXPOSE 8080

CMD php artisan config:clear && php artisan migrate --force && php -S 0.0.0.0:${PORT:-8080} -t public