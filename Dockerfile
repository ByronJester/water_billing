FROM composer as builder

WORKDIR /app

COPY composer.json composer.lock /app/

# Faster parallel build
RUN composer global require hirak/prestissimo

# https://blog.amezmo.com/php-deployment-best-practices-when-using-composer/
RUN composer install  \
    --optimize-autoloader \
    --no-autoloader \
    --no-ansi \
    --no-interaction \
    --no-progress \
    --no-dev \
    --profile

COPY . /app

RUN composer dump-autoload \
    --optimize \
    --classmap-authoritative \
    --no-interaction \
    --no-scripts \
    --no-dev

FROM php:7.4-fpm-alpine

RUN apk update && apk add --no-cache \
    libpng-dev \
    libzip-dev \
    libxml2-dev \
    curl-dev \
    freetype-dev \
    jpeg-dev \
    oniguruma-dev \
    zip \
    vim \
    nginx \
    supervisor \
    pcre-dev $PHPIZE_DEPS

RUN pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    bcmath \
    curl \
    gd \
    json \
    mbstring \
    pdo \
    pdo_mysql \
    xml \
    zip \
    sockets

# https://github.com/docker-library/php/issues/240#issuecomment-305038173
RUN apk add --no-cache --repository http://dl-3.alpinelinux.org/alpine/edge/testing gnu-libiconv
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so php

WORKDIR /var/www/html/app

# Faster setup for permissions
# https://blog.programster.org/dockerfile-speed-up-the-setting-of-permissions
COPY --from=builder --chown=www-data:www-data /app .
COPY --from=builder /usr/bin/composer /usr/local/bin/composer

RUN mkdir -p /run/nginx
RUN rm /etc/nginx/conf.d/default.conf
COPY .docker/nginx/conf.d/app.conf /etc/nginx/conf.d

COPY .docker/php/fpm.d/www.conf /usr/local/etc/php-fpm.d/
COPY .docker/php/uploads.ini /usr/local/etc/php/conf.d/

COPY .docker/supervisor/conf.d/app.conf /etc/supervisord.conf
COPY .docker/start.sh ./start.sh

EXPOSE 8080

# CMD ["sh", "./start.sh"]
RUN chmod +x start.sh

# unset default image entrypoint
ENTRYPOINT []
