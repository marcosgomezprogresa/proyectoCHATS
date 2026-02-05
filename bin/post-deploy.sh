#!/bin/bash

# Post-deploy script for Railway
# Executed after the container is deployed

set -e

echo "=== Starting Post-Deploy Script ==="
echo "Current directory: $(pwd)"
echo "PHP version: $(php -v)"

echo ""
echo "=== Running Symfony cache clear... ==="
php bin/console cache:clear --env=prod || echo "⚠️  Cache clear failed, continuing..."

echo ""
echo "=== Creating database if not exists... ==="
php bin/console doctrine:database:create --if-not-exists --env=prod || echo "⚠️  Database creation failed"

echo ""
echo "=== Running database migrations... ==="
php bin/console doctrine:migrations:migrate --no-interaction --env=prod
MIGRATION_EXIT=$?
if [ $MIGRATION_EXIT -ne 0 ]; then
    echo "⚠️  Migrations exited with code $MIGRATION_EXIT"
else
    echo "✅ Migrations completed successfully"
fi

echo ""
echo "=== Installing assets... ==="
php bin/console assets:install --env=prod || echo "⚠️  Assets install failed"

echo ""
echo "✅ Post-deploy script completed!"
