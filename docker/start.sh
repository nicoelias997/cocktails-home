#!/bin/sh
set -eu

mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan migrate --force
php artisan storage:link || true

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
