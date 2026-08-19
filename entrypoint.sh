#!/bin/bash
set -e

# Start MariaDB service
service mariadb start

# Wait for MariaDB to start
until mysqladmin ping --silent; do
    echo "Waiting for MariaDB to start..."
    sleep 1
done

# Grant full privileges to root for localhost and 127.0.0.1 without password
mysql -e "CREATE USER IF NOT EXISTS 'root'@'127.0.0.1' IDENTIFIED BY '';"
mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' WITH GRANT OPTION;"
mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION;"
mysql -e "FLUSH PRIVILEGES;"

# Create database and import schema
mysql -u root -e "CREATE DATABASE IF NOT EXISTS worker;"
mysql -u root worker < /var/www/html/worker_dump.sql

echo "Database initialized successfully!"

# Start Apache in foreground
exec apache2-foreground
