#!/bin/sh

# composer run-script post-autoload-dump
# https://laravel.com/docs/7.x/deployment#optimizing-configuration-loading

php artisan config:cache
php artisan route:cache
# php artisan cache:clear
php artisan migrate --force

# apache2ctl -D FOREGROUND
# supervisord -c /etc/supervisord.conf
