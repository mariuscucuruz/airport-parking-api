FROM php:8.1-npm

RUN echo "Building API container ......."
RUN apt-get update && apt-get install -yqq  --no-install-recommends \
    git zip unzip curl wget vim libzip-dev \
    build-essential software-properties-common \
    libmcrypt-dev libpq-dev libpng-dev libxml2-dev \
    default-mysql-client openssl libssl-dev libcurl4-openssl-dev \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev

RUN echo "Installing PHP extensions .................."
RUN pecl install mcrypt-1.0.2
RUN docker-php-ext-install -j$(nproc) gd zip bcmath pdo pdo_mysql pdo_pgsql mbstring \
    && docker-php-ext-enable mcrypt pdo_mysql pdo gd \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/
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
