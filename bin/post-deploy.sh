#!/bin/bash

# Post-deploy script for Railway
# Executed after the container is deployed

echo "Running Symfony cache clear..."
php bin/console cache:clear --env=prod

echo "Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --env=prod || true

echo "Installing assets..."
php bin/console assets:install --env=prod

echo "Post-deploy completed!"
