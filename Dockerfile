FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev

RUN docker-php-ext-install pdo_mysql zip

WORKDIR /app

COPY . .

RUN useradd -ms /bin/bash sail

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

RUN composer install

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]