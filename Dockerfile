FROM dunglas/frankenphp

RUN install-php-extensions \
    pcntl \
    pdo_pgsql \
    intl

COPY . /app

ENTRYPOINT ["php", "artisan", "octane:frankenphp","--watch"]