FROM node:20-slim AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

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

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy only necessary files
COPY composer.* ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy application files
COPY . .
COPY --from=frontend /app/public/build public/build

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Create SQLite database directory
RUN mkdir -p database \
    && chown -R www-data:www-data database

# Switch to non-root user
USER www-data

# Expose Octane port
EXPOSE 8000

# Start Octane
CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan octane:start --server=swoole --host=0.0.0.0 --port=8000