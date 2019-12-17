#!/bin/sh

echo "Waiting for MySQL at $DB_HOST:${DB_PORT:-3306}"
wait-for $DB_HOST:${DB_PORT:-3306} -t 60 -q -- echo OK

php artisan migrate --force
php artisan optimize
php artisan storage:link

exec php-fpm
