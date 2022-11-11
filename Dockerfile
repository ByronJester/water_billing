# FROM composer as builder

# WORKDIR /app

# COPY composer.json composer.lock /app/

# # https://blog.amezmo.com/php-deployment-best-practices-when-using-composer/
# RUN composer install  \
#     --optimize-autoloader \
#     --no-autoloader \
#     --no-ansi \
#     --no-interaction \
#     --no-progress \
#     --no-dev \
#     --profile

# COPY . /app

# RUN composer dump-autoload \
#     --optimize \
#     --classmap-authoritative \
#     --no-interaction \
#     --no-scripts \
#     --no-dev

# FROM php:7.4-fpm-alpine

# RUN apk add --no-cache libpq-dev \
#     && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
#     && docker-php-ext-install pdo pdo_pgsql pgsql



# WORKDIR /var/www/html/app
# COPY --from=builder --chown=www-data:www-data /app .
# COPY --from=builder /usr/bin/composer /usr/local/bin/composer

# RUN mkdir -p /run/nginx
# COPY .docker/nginx/conf.d/app.conf /etc/nginx/conf.d

# COPY .docker/php/fpm.d/www.conf /usr/local/etc/php-fpm.d/
# COPY .docker/php/uploads.ini /usr/local/etc/php/conf.d/

# COPY .docker/supervisor/conf.d/app.conf /etc/supervisord.conf
# COPY .docker/start.sh ./start.sh

# COPY . .

# ENV SKIP_COMPOSER 1
# ENV WEBROOT /var/www/html/app/public
# ENV PHP_ERRORS_STDERR 1
# ENV RUN_SCRIPTS 1
# ENV REAL_IP_HEADER 1

# ENV APP_ENV production
# ENV APP_DEBUG false
# ENV LOG_CHANNEL stderr

# ENV COMPOSER_ALLOW_SUPERUSER 1

# EXPOSE 8080

# # CMD ["sh", "./start.sh"]
# RUN chmod +x start.sh
# ENTRYPOINT []

FROM composer:2.0.11 as backend

WORKDIR /backend

COPY composer.json composer.lock /backend/

RUN composer install  \
    --ignore-platform-reqs \
    --optimize-autoloader \
    --no-autoloader \
    --no-ansi \
    --no-interaction \
    --no-progress \
    --profile

COPY . /backend

RUN composer dump-autoload \
    --optimize \
    --classmap-authoritative \
    --no-interaction

FROM php:7.4-fpm-alpine

RUN apk add --no-cache libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

FROM node:12.21.0-alpine as frontend

WORKDIR /frontend

COPY package.json package-lock.json /frontend/

RUN npm install

COPY artisan tailwind.config.js webpack.config.js webpack.mix.js /frontend/
COPY app ./app
COPY bootstrap ./bootstrap
COPY public ./public
COPY resources ./resources

RUN npm run dev

FROM php:7.3-apache-stretch

RUN mkdir -p /usr/share/man/man1 /usr/share/man/man2

RUN apt-get update && apt-get install -y --no-install-recommends \
    build-essential \
    libpng-dev \
    libzip-dev \
    libxml2-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libcurl4-openssl-dev \
    libonig-dev \
    openjdk-8-jdk \
    software-properties-common

RUN apt-add-repository contrib

RUN apt-get update

# https://stackoverflow.com/a/63575759
RUN echo "ttf-mscorefonts-installer msttcorefonts/accepted-mscorefonts-eula select true" | debconf-set-selections
RUN apt-get install -y --no-install-recommends fontconfig ttf-mscorefonts-installer
ADD .docker/fonts/localfonts.conf /etc/fonts/local.conf
RUN fc-cache -f -v

RUN pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-configure gd \
    --with-png-dir=/usr/include/ \
    --with-jpeg-dir=/usr/include/ \
    --with-freetype-dir=/usr/include/ \
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

WORKDIR /var/www/html

COPY --from=backend --chown=www-data:www-data /backend .

RUN rm -rf ./public/*
COPY --from=frontend --chown=www-data:www-data /frontend/public ./public

COPY .docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY .docker/php/uploads.ini /usr/local/etc/php/conf.d

RUN mkdir -p /run/apache2
RUN mkdir -p /passport/keys
RUN chmod 755 -R /passport/keys
RUN chmod 777 -R /var/www/html/storage/

RUN echo "Listen 8080" >> /etc/apache2/ports.conf
RUN a2enmod rewrite
RUN a2enmod headers

COPY .docker/start.sh ./start.sh
RUN chmod +x ./start.sh

EXPOSE 8080

COPY --from=us-docker.pkg.dev/berglas/berglas/berglas:latest /bin/berglas /bin/berglas
ENTRYPOINT exec /bin/berglas exec -- sh -c ./start.sh