#!/bin/sh
set -e

echo "🏁 Waiting for database..."
until php bin/console doctrine:query:sql "SELECT 1" >/dev/null 2>&1; do
  sleep 1
done

echo "🚀 Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

echo "🔧 Starting with command: $@"
exec "$@"
