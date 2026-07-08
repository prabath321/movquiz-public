#!/usr/bin/env sh
set -e

if [ ! -f .env ]; then
    echo "Missing .env file. Mount your project .env file to /var/www/html/.env."
    exit 1
fi

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

php artisan config:clear

exec "$@"
