FROM php:8.3-fpm-alpine3.19 as builder

# Install PHP extensions and dependencies
RUN apk update \
    && apk add --no-cache curl \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && curl -fsSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o /usr/local/bin/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions \
    && install-php-extensions pdo_mysql opcache zip intl

# Set working directory
WORKDIR /var/www/mytheresa

# Copy application files and install Composer dependencies
#COPY . /var/www/mytheresa
COPY . .

# Run Composer install
COPY composer.json composer.lock ./

RUN composer validate
RUN composer config platform.php 8.3 \
    && composer install --no-interaction --optimize-autoloader

# Run Unit Tests
RUN ./vendor/bin/phpunit --testdox

FROM php:8.3-fpm-alpine3.19 AS app

# Install PHP extensions and dependencies
RUN apk update \
    && apk add --no-cache curl \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && curl -fsSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o /usr/local/bin/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions \
    && install-php-extensions pdo_mysql opcache zip intl

# Set working directory
WORKDIR /var/www/mytheresa

COPY --from=builder /var/www/mytheresa /var/www/mytheresa

RUN composer config platform.php 8.3 \
    && composer install --no-dev --no-interaction --optimize-autoloader --no-scripts

CMD ["php-fpm"]
