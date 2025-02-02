FROM php:8.4-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    iputils-ping \
    wget \
    iproute2 \
    netcat-openbsd \
    cron \
    nano

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets

RUN pecl install redis && docker-php-ext-enable redis

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www

RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap \
    && chown -R www-data:www-data /var/www/storage/logs \
    && chmod 777 -R /var/www

RUN mkdir -p /var/log && touch /var/log/xdebug.log && chmod 777 /var/log/xdebug.log

RUN echo "xdebug.mode=debug\n\
    xdebug.start_with_request=yes\n\
    xdebug.discover_client_host=1\n\
    xdebug.client_port=9000\n\
    xdebug.log=/var/log/xdebug.log\n\
    xdebug.max_nesting_level=256" > /usr/local/etc/php/conf.d/20-xdebug.ini

ENTRYPOINT ["php-fpm"]

EXPOSE 9000
