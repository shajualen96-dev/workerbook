FROM php:8.2-apache

# Install MariaDB server and client, plus PHP extensions
RUN apt-get update && apt-get install -y \
    mariadb-server \
    mariadb-client \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy project files to Apache web root
COPY . /var/www/html/

# Make entrypoint script executable
RUN chmod +x /var/www/html/entrypoint.sh

# Expose HTTP port
EXPOSE 80

# Use custom entrypoint script
ENTRYPOINT ["/var/www/html/entrypoint.sh"]
