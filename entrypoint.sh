#!/bin/bash
set -e

# Start MariaDB server
service mariadb start

# Wait for MariaDB service to be fully active
until mysqladmin ping --silent; do
    echo "Waiting for MariaDB to start..."
    sleep 1
done

# Create database and import schema + data
mysql -u root -e "CREATE DATABASE IF NOT EXISTS worker;"
mysql -u root worker < /var/www/html/worker_dump.sql

echo "Database initialized successfully!"

# Start Apache in foreground
exec apache2-foreground
