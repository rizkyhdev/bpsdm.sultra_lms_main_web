FROM serversideup/php:8.2-fpm-nginx

# Set the working directory
WORKDIR /var/www/html

# Define environment variables for Laravel production
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr

# Switch to root to copy folders and set ownership properly
USER root

# Copy application files and set ownership to www-data
COPY --chown=www-data:www-data . /var/www/html

# Switch back to the www-data user to run setup commands safely
USER www-data

# Install Laravel dependencies (ignoring dev packages)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Cache Laravel configuration for faster boot times
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Note: The serversideup image automatically starts nginx and php-fpm on container boot.