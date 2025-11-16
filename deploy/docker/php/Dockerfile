# =========================
# PHP Backend Dockerfile
# =========================

# Base image
FROM php:8.2-cli

# Set working directory
WORKDIR /var/www/html

# -------------------------
# Install system dependencies & PHP extensions
# -------------------------
RUN apt-get update && apt-get install -y \
        zip unzip git curl libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli mbstring zip xml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# -------------------------
# Install Composer
# -------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# -------------------------
# Copy PHP error config
# -------------------------
COPY deploy/docker/php/docker-php-errors.ini /usr/local/etc/php/conf.d/docker-php-errors.ini

# -------------------------
# Copy source code
# -------------------------
COPY src/php-backend/ /var/www/html/

# -------------------------
# Set permissions (if using mounted volumes)
# -------------------------
RUN chown -R www-data:www-data /var/www/html

# -------------------------
# Install PHP dependencies via Composer
# -------------------------
RUN composer install --no-dev --optimize-autoloader --no-interaction

# -------------------------
# Expose port for PHP server
# -------------------------
EXPOSE 8000

# -------------------------
# Start PHP built-in server & logs to stdout
# -------------------------
CMD ["php", "-S", "0.0.0.0:8000", "-t", "/var/www/html"]
