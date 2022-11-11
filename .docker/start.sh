if [ -e .env ]
then
    echo "Exporting env variables..."
    set -a
    . ./.env
    set +a
else
    echo ".env not found. Skipping..."
fi

cd "$KEY_PATH" && ls -la
chmod -R 755 "$KEY_PATH" && ls -la
cd /var/www/html

php artisan config:cache
php artisan route:cache
# php artisan cache:clear
php artisan migrate --force

