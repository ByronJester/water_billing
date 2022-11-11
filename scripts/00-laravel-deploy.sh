#!/usr/bin/env bash
echo "Running composer"
composer global require hirak/prestissimo
composer install --no-dev --no-scripts --optimize-autoloader --no-autoloader --no-ansi --no-interaction -no-progress --no-dev --profile --working-dir=/var/www/html
composer dump-autoload --optimize --classmap-authoritative --no-interaction --no-scripts --no-dev

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Running migrations..."
php artisan migrate --force