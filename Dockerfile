# Frontend Build Stage
FROM node:20-slim AS frontend
WORKDIR /app

# Copy and install Node.js dependencies
COPY package*.json ./
RUN npm ci

# Copy all files and build the frontend
COPY . .
RUN npm run build

# PHP/Laravel Build Stage
FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install zip pdo_sqlite opcache

# Install Swoole
RUN pecl install swoole && docker-php-ext-enable swoole

# Configure PHP for production
COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini

# Set working directory
WORKDIR /var/www

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Laravel Composer files and install dependencies
COPY composer.* ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy application files
COPY . .

# Copy frontend build artifacts
COPY --from=frontend /app/public/build public/build

# Explicitly create SQLite database directory
RUN mkdir -p database \
    && chown -R www-data:www-data database

# Set permissions for storage and bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Health Check
HEALTHCHECK --interval=30s --timeout=5s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:8000 || exit 1

# Switch to non-root user for security
USER www-data

# Expose Octane port
EXPOSE 8000

# Start Laravel with robust CMD
CMD sh -c "\
    php artisan config:cache && \
    php artisan route:cache || echo 'Skipping route cache' && \
    php artisan view:cache && \
    php artisan octane:start --server=swoole --host=0.0.0.0 --port=8000 >> /var/www/storage/logs/octane.log 2>&1"
