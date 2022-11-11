FROM richarvey/nginx-php-fpm:1.7.2

FROM composer as builder

COPY . .

# Image config
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

RUN composer install  \
    --optimize-autoloader \
    --no-autoloader \
    --no-ansi \
    --no-interaction \
    --no-progress \
    --no-dev \
    --profile

RUN composer dump-autoload \
    --optimize \
    --classmap-authoritative \
    --no-interaction \
    --no-scripts \
    --no-dev

CMD ["/start.sh"]
