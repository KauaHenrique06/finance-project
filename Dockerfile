FROM php:8.3-fpm

ARG UID=1000
ARG GID=1000

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_pgsql zip pcntl exif \
    && pecl install redis \
    && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Usuário com o mesmo UID/GID do host, para que os arquivos criados
# dentro do container (artisan make, composer, etc) já saiam com o dono certo
RUN groupadd -g ${GID} app || true \
    && useradd -u ${UID} -g ${GID} -m -s /bin/bash app

ENV COMPOSER_HOME=/home/app/.composer

WORKDIR /var/www/html

USER app