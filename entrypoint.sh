#!/bin/sh

set -a
. /var/www/mytheresa/.env
set +a

# Wait for the database service to be ready
echo "Waiting for the database..."
while ! nc -z database 3306; do
  sleep 1
done

APP_ENV=prod php bin/console cache:clear
APP_ENV=prod php bin/console cache:warmup

# Run migrations
echo "Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

if [ "$APP_ENV" != "prod" ]; then
    # Run seeders/fixtures
    echo "Running fixtures..."
    php bin/console doctrine:fixtures:load --no-interaction
fi

# Finally, start the main PHP-FPM process
exec "$@"
