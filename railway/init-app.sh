#!/bin/bash
# Make sure this file has executable permissions, run `chmod +x railway/init-app.sh`

# Exit the script if any command fails
set -e

# composer install
npm install
composer install --no-dev --no-interaction --prefer-dist
npm run build

# Run migrations
php artisan migrate --force

# Create admin user if it doesn't exist
# Use environment variables with defaults (can be overridden via Railway environment variables)
php railway/create-admin.php

# Clear cache
php artisan optimize:clear

# Cache the various components of the Laravel application
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache