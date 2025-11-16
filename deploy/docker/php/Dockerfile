# Base image
FROM php:8.2-fpm

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    zip unzip git curl libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli mbstring zip xml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy source code
COPY src/php-backend/ /var/www/html/

# Set permissions (important if mounting volume from host)
RUN chown -R www-data:www-data /var/www/html

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

EXPOSE 8000

# =====================
# Configure PHP-FPM logs to stdout/stderr
# =====================
RUN sed -i "s|^error_log = .*|error_log = /proc/self/fd/2|" /usr/local/etc/php-fpm.d/www.conf \
    && sed -i "s|^;catch_workers_output = .*|catch_workers_output = yes|" /usr/local/etc/php-fpm.d/www.conf

# Start PHP-FPM in foreground (logs visible in docker)
CMD ["php-fpm", "-F"]
