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

# https://github.com/docker-library/php/issues/240#issuecomment-305038173
RUN apk add --no-cache --repository http://dl-3.alpinelinux.org/alpine/edge/testing gnu-libiconv
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so php

WORKDIR /var/www/html/app

# Faster setup for permissions
# https://blog.programster.org/dockerfile-speed-up-the-setting-of-permissions
COPY --from=builder --chown=www-data:www-data /app .
COPY --from=builder /usr/bin/composer /usr/local/bin/composer

RUN mkdir -p /run/nginx
COPY .docker/nginx/conf.d/app.conf /etc/nginx/conf.d

COPY .docker/php/fpm.d/www.conf /usr/local/etc/php-fpm.d/
COPY .docker/php/uploads.ini /usr/local/etc/php/conf.d/

COPY .docker/supervisor/conf.d/app.conf /etc/supervisord.conf
COPY .docker/start.sh ./start.sh

EXPOSE 8080

CMD ["sh", "./start.sh"]
