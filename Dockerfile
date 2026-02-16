FROM php:8.2-cli

# Install dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libonig-dev libssl-dev mariadb-client \
    && docker-php-ext-configure zip \
    && docker-php-ext-install pdo_mysql mbstring zip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

ENV COMPOSER_ALLOW_SUPERUSER=1

# Default command runs Laravel via PHP built-in server
CMD ["php", "-S", "0.0.0.0:9000", "-t", "public"]
