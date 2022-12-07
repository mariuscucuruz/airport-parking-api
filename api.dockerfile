FROM php:8.1-fpm-alpine

RUN echo "Building API container ......."
RUN apk update && apk add -yqq --no-cache --no-install-recommends \
    git zip unzip curl wget vim libzip-dev \
    build-essential software-properties-common \
    libmcrypt-dev libpq-dev libpng-dev libxml2-dev \
    default-mysql-client openssl libssl-dev libcurl4-openssl-dev \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev

RUN echo "Installing PHP extensions .................."
RUN pecl install zip && docker-php-ext-enable zip \
    && pecl install mcrypt-1.0.2 \
    && pecl install igbinary && docker-php-ext-enable igbinary \
    && yes | pecl install redis && docker-php-ext-enable redis

RUN docker-php-ext-install -j$(nproc) gd zip bcmath pdo pdo_mysql mysqli mbstring pcntl \
    && docker-php-ext-enable mcrypt pdo_mysql pdo gd \
    && docker-php-ext-configure gd --with-freetype --with-jpeg
RUN apt-get clean && apt-get --purge autoremove -y

RUN echo "Deploying the API .........."
WORKDIR /var/www
COPY . /var/www

RUN echo "Installing composer ................."
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# cannot go further without .env file
RUN #!/bin/bash \
    && if [ ! -x "/var/www/.env"; ] then \
    &&   echo "Please ensure .env exists!" >&2 \
    &&   exit() \
    && fi

RUN echo "Wrap it up!"
RUN composer dump-autoload \
    && composer install --ignore-platform-reqs --no-scripts --no-interaction -o

#RUN php artisan migrate:fresh
#RUN php artisan db:seed

RUN echo "API Ready!"
