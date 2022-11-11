composer install --no-dev --working-dir=/var/www/html

php artisan config:cache
php artisan route:cache
# php artisan cache:clear
php artisan migrate --force

