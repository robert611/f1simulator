FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    && docker-php-ext-install \
        intl \
        zip \
        xml \
        pdo_mysql \
        ctype \
        iconv \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install opcache
RUN pecl install xdebug && docker-php-ext-enable xdebug

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /app
