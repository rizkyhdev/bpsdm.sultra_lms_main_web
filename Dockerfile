FROM serversideup/php:8.2-fpm-nginx

# Switch to root to install extensions
USER root



# Set the working directory
WORKDIR /var/www/html

# Switch back to the www-data user
USER www-data
