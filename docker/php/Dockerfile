FROM php:8.4-fpm-alpine

# Install system build dependencies and libraries
RUN apk add --no-cache \
    icu-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    linux-headers \
    $PHPIZE_DEPS

# Install PHP extensions required by Symfony, MySQL, and BCMath for money precision
RUN docker-php-ext-install \
    pdo_mysql \
    intl \
    opcache \
    zip \
    bcmath

# Configure PHP timezone to Asia/Manila and OPcache settings
RUN echo "date.timezone = Asia/Manila" > /usr/local/etc/php/conf.d/timezone.ini \
    && printf '%s\n' \
        'opcache.enable=1' \
        'opcache.memory_consumption=256' \
        'opcache.max_accelerated_files=20000' \
        'opcache.validate_timestamps=0' \
        'opcache.revalidate_freq=0' \
        > /usr/local/etc/php/conf.d/opcache.ini

# Allow more concurrent PHP-FPM workers (default of 5 queues requests)
RUN sed -i \
    -e 's/^pm.max_children = .*/pm.max_children = 20/' \
    -e 's/^pm.start_servers = .*/pm.start_servers = 4/' \
    -e 's/^pm.min_spare_servers = .*/pm.min_spare_servers = 2/' \
    -e 's/^pm.max_spare_servers = .*/pm.max_spare_servers = 6/' \
    /usr/local/etc/php-fpm.d/www.conf

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install dependencies inside the image so no Windows bind-mount scans happen at runtime
COPY composer.json composer.lock ./
RUN composer install \
        --no-interaction \
        --no-progress \
        --prefer-dist \
        --optimize-autoloader \
        --classmap-authoritative \
        --no-scripts

COPY . .

RUN composer dump-autoload --optimize --classmap-authoritative --no-interaction

ENV APP_ENV=prod APP_DEBUG=0
RUN php bin/console cache:warmup --no-interaction \
    && php bin/console asset-map:compile --env=prod --no-interaction

CMD ["php-fpm"]
