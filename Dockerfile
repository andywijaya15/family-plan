FROM dunglas/frankenphp:latest-php8.3

# Install dependencies untuk Composer dan ekstensi PHP
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    libpq-dev \
    libexif-dev \
    libsodium-dev

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN install-php-extensions \
    pgsql \
    pdo_pgsql \
    gd \
    intl \
    zip \
    exif \
    sodium \
    pcntl

RUN curl -L https://github.com/php/frankenphp/releases/download/v1.9.1/frankenphp-linux-x86_64 -o /usr/local/bin/frankenphp \
    && chmod +x /usr/local/bin/frankenphp

WORKDIR /app

COPY . ./

# Install dependencies using Composer
RUN composer install --no-dev --optimize-autoloader

RUN php artisan filament:optimize

RUN rm -rf ./git

# Run FrankenPHP
CMD ["php", "artisan", "octane:frankenphp", "--host=0.0.0.0", "--port=80"]
