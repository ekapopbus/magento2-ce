FROM php:8.4-fpm

RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    libonig-dev \
    pkg-config \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j"$(nproc)" \
            pdo_mysql \
            mbstring \
            intl \
            zip \
            gd \
            xml \
            bcmath \
            opcache \
        && mkdir -p /tmp/pear/cache \
        && pecl clear-cache \
        && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Use the default php-fpm entrypoint and command
