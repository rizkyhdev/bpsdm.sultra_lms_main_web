FROM serversideup/php:8.2-fpm-nginx

# Switch to root to install extensions
USER root

# Install PostgreSQL PHP extensions
RUN install-php-extensions pdo_pgsql pgsql

# Set the working directory
WORKDIR /var/www/html

# Switch back to the www-data user
USER www-data
