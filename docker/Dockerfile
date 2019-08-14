FROM php:7.2-fpm-alpine

WORKDIR /app

COPY . .

RUN apk add --update postgresql-dev freetype-dev libpng-dev libjpeg-turbo-dev freetype libpng libjpeg-turbo git\
 && docker-php-ext-configure gd \
    --with-gd \
    --with-freetype-dir=/usr/include/ \
    --with-png-dir=/usr/include/ \
    --with-jpeg-dir=/usr/include/ \
 && docker-php-ext-install gd pdo pdo_pgsql pgsql opcache zip \
 && apk del --no-cache freetype-dev libpng-dev libjpeg-turbo-dev