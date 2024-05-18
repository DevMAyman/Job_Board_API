#!/usr/bin/env bash

set -e

# Change to the directory where your application is located
cd /var/www/html

# Ensure Composer dependencies are installed
echo "Installing Composer dependencies..."
composer install --no-dev

# Clear and cache configuration
echo "Caching config..."
php artisan config:cache

# Clear and cache routes
echo "Caching routes..."
php artisan route:cache

# Run database migrations
echo "Running migrations..."
php artisan migrate --force

# Seed the database with the UserSeeder class
echo "Seeding the database..."
php artisan db:seed --class=UserSeeder

echo "Deployment seeding script completed successfully."
