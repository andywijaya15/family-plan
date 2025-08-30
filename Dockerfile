FROM dunglas/frankenphp:latest

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN install-php-extensions pcntl pdo_pgsql intl zip

COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts

COPY . .

ENTRYPOINT ["php", "artisan", "octane:frankenphp", "--watch"]
