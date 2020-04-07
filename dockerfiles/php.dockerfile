FROM php:7.4.4-fpm-alpine3.11

WORKDIR /var/www/html

RUN apk add --no-cache gmp-dev libzip-dev \
    && docker-php-ext-install gmp zip bcmath mysqli pdo_mysql

COPY web_dev .

RUN find storage -type d -exec chmod 777 {} \;
