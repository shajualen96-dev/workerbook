#!/bin/bash
set -e

# Start MariaDB service
service mariadb start

# Wait for MariaDB to be fully active
until mysqladmin ping --silent; do
    echo "Waiting for MariaDB to start..."
    sleep 1
done

# Remove auth_socket plugin constraint and grant root access to localhost & 127.0.0.1
mysql -u root <<EOF
USE mysql;
UPDATE user SET plugin='' WHERE User='root';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY '';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' IDENTIFIED BY '';
FLUSH PRIVILEGES;
CREATE DATABASE IF NOT EXISTS worker;
EOF

# Import SQL schema and initial data
mysql -u root worker < /var/www/html/worker_dump.sql

echo "Database initialized successfully for Apache!"

# Start Apache web server in foreground
exec apache2-foreground
