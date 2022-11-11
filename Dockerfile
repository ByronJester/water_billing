FROM composer as builder

WORKDIR /app

COPY composer.json composer.lock /app/

FROM php:7.4-fpm-alpine

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

ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1


EXPOSE 8080

CMD ["sh", "./start.sh"]
