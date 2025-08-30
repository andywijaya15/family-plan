FROM php:8.4-cli

WORKDIR /app

# Install system dependencies + PHP extensions
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    unzip \
    git \
    libpq-dev \
    && docker-php-ext-install intl pdo_pgsql pcntl zip

# Copy composer binary
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Expose port
EXPOSE 8000

# Jalankan Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
