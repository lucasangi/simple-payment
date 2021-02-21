#!/usr/bin/env ash
set -eu

# Installing dependences
echo "- Installing composer dependences"
composer install \
    --optimize-autoloader \
    --no-ansi \
    --no-interaction \
    --no-progress
echo "- Done"

# If running via docker-compose, then it is necessary to wait for the mysql container to be available
echo "- Waiting for mysql in host '$DB_SERVER'"
until mysql -u $DB_USER -p$DB_PASSWORD -h $DB_SERVER --port=$DB_PORT -e "exit" >&2
do
    echo "-- MySQL is unavailable - sleeping"
    sleep 1
done
echo "- Done! MySQL is up - executing command"

echo "- Running Migrations"
# /app/bin/console doctrine:migrations:migrate --no-interaction
echo "- Done"

exec "$@"