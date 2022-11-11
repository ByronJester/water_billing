FROM composer as builder

WORKDIR /app

COPY composer.json composer.lock /app/

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

RUN apk add --no-cache libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

WORKDIR /var/www/html/app

COPY --from=builder --chown=www-data:www-data /app .
COPY --from=builder /usr/bin/composer /usr/local/bin/composer

RUN mkdir -p /run/nginx
COPY .docker/nginx/conf.d/app.conf /etc/nginx/conf.d

COPY .docker/php/fpm.d/www.conf /usr/local/etc/php-fpm.d/
COPY .docker/php/uploads.ini /usr/local/etc/php/conf.d/

COPY .docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

COPY .docker/supervisor/conf.d/app.conf /etc/supervisord.conf
COPY .docker/start.sh ./start.sh

RUN mkdir -p /run/apache2

COPY . .

ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/app/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

ENV COMPOSER_ALLOW_SUPERUSER 1

EXPOSE 8080

RUN chmod +x start.sh


CMD ["./start.sh"]