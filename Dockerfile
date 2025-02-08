FROM php:8.3-fpm

# Установка необходимых зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    zip \
    unzip

# Установка расширений PHP
RUN docker-php-ext-install pdo_mysql zip

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Создание пользователя с тем же UID и GID, что и на хосте
ARG UID=1000
ARG GID=1000

RUN groupadd -g $GID laravel && useradd -m -u $UID -g $GID -s /bin/bash laravel

# Установка прав доступа
RUN mkdir -p /var/www/html/storage/framework/{views,cache,sessions} /var/www/html/bootstrap/cache
RUN chown -R laravel:laravel /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# # Создание необходимых директорий, если они отсутствуют
# RUN mkdir -p /var/www/html/storage/framework/{views,cache,sessions} /var/www/html/bootstrap/cache

# # Установка прав доступа
# RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
# RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Установка рабочей директории
WORKDIR /var/www/html
