#!/usr/bin/env bash
set -e

composer install --no-dev --optimize-autoloader --working-dir=/var/www/html

php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan migrate --force
