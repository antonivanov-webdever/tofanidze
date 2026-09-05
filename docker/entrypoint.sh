#!/bin/sh
set -e

# Wait for the database before touching it — compose starts services in parallel.
if [ -n "$DB_HOST" ]; then
    echo "Waiting for database at $DB_HOST:${DB_PORT:-3306}…"
    for i in $(seq 1 30); do
        if php -r "exit(@fsockopen(getenv('DB_HOST'), (int) (getenv('DB_PORT') ?: 3306)) ? 0 : 1);"; then
            echo "Database is up."
            break
        fi
        sleep 2
    done
fi

# Configuration comes from the environment; .env only exists so artisan has
# somewhere to write a generated key when APP_KEY was not supplied.
if [ ! -f .env ]; then
    cp .env.example .env
fi

if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

php artisan migrate --force
php artisan storage:link || true

# Cache configuration, routes and views for production.
if [ "$APP_ENV" = "production" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
else
    php artisan optimize:clear
fi

exec "$@"
