#!/usr/bin/env sh
set -eu

cd /var/www/html

DB_PATH="${DB_DATABASE:-/var/www/html/storage/app/myitsbroadcast.sqlite}"
mkdir -p "$(dirname "$DB_PATH")" \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

touch "$DB_PATH"
chown -R www-data:www-data storage bootstrap/cache
chmod 664 "$DB_PATH"

# Recreate Laravel's public storage symlink when the container starts.
php artisan storage:link || true

# The local SQLite database is intentionally temporary on Render Free.
php artisan migrate --force
php artisan db:seed --class=ProductionDemoSeeder --force

php artisan optimize:clear
php artisan config:cache

exec /usr/bin/supervisord -n -c /etc/supervisord.conf
