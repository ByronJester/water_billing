FROM richarvey/nginx-php-fpm:1.10.4

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

RUN mkdir -p /run/nginx
COPY .docker/nginx/conf.d/app.conf /etc/nginx/conf.d

COPY .docker/php/fpm.d/www.conf /usr/local/etc/php-fpm.d/
COPY .docker/php/uploads.ini /usr/local/etc/php/conf.d/

COPY .docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

COPY .docker/supervisor/conf.d/app.conf /etc/supervisord.conf

CMD ["/start.sh"]