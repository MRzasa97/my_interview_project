#!/bin/bash
set -e

# Run migrations for dev environment
php bin/console doctrine:migrations:migrate --no-interaction --env=dev

# Run migrations for test environment
php bin/console doctrine:migrations:migrate --no-interaction --env=test

# Execute the original command
exec "$@"