# Stage 1: Node build
FROM node:20-alpine AS nodebuild
WORKDIR /app
COPY package.json package-lock.json* pnpm-lock.yaml* yarn.lock* ./
RUN npm install
RUN npm install @rollup/rollup-linux-x64-musl lightningcss-linux-x64-musl @tailwindcss/oxide-linux-x64-musl -D
COPY . .
RUN npm run build

FROM php:8.2-apache

# Install dependency system
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    libsqlite3-dev \
    libpq-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql pdo_sqlite zip mbstring xml
RUN a2enmod rewrite
RUN echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Harden PHP ini defaults (override any compromised/host-provided settings)
RUN { \
    echo 'display_errors=Off'; \
    echo 'log_errors=On'; \
    echo 'error_reporting=E_ALL'; \
    echo 'auto_prepend_file='; \
    echo 'auto_append_file='; \
    echo 'user_ini.filename='; \
    echo 'allow_url_include=Off'; \
    echo 'allow_url_fopen=On'; \
  } > /usr/local/etc/php/conf.d/zz-cat-app.ini

# Set working directory
WORKDIR /var/www/html

# Copy composer manifests first, then install deps in clean context
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts

# Copy project after vendor installation
COPY . .

# Host bind mounts often ship stale bootstrap/cache; manifest must match installed packages
RUN rm -f bootstrap/cache/packages.php bootstrap/cache/services.php bootstrap/cache/config.php

# Copy built frontend assets from node build stage
COPY --from=nodebuild /app/public/build /var/www/html/public/build

# Run composer scripts after full app source is present
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permission Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

RUN mkdir -p storage/logs bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache \
 && find storage -type d -exec chmod 775 {} \; \
 && find storage -type f -exec chmod 664 {} \; \
 && find bootstrap/cache -type d -exec chmod 775 {} \; \
 && find bootstrap/cache -type f -exec chmod 664 {} \;

# Serve Laravel public/ directly via Apache.
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf

EXPOSE 80
CMD ["apache2-foreground"]
