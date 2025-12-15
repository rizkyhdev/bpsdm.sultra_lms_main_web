#!/usr/bin/env bash
echo "Running composer and npm install"
composer global require hirak/prestissimo
composer install --no-dev --working-dir=/var/www/html

# composer install --no-dev --no-interaction --working-dir=/var/www/html

# npm install
# npm run build

# Skip generating app key for dev
# echo "generating application key..."
# php artisan key:generate --show

# echo "Caching config..."
# php artisan config:cache

# echo "Caching routes..."
# php artisan route:cache



echo "Running migrations..."
php artisan migrate --force

# Clear cache
# php artisan optimize:clear


# Cache the various components of the Laravel application
echo "Caching components of the Laravel application..."
php artisan config:cache
echo "Caching events..."
php artisan event:cache
echo "Caching routes..."
php artisan route:cache
echo "Caching views..."
php artisan view:cache

