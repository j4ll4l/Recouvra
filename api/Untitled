FROM dunglas/frankenphp:1-php8.4
RUN install-php-extensions pdo_pgsql intl opcache zip redis
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app