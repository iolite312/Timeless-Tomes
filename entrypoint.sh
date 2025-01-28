#!/bin/bash

# Change directory to /app (where your project files are mounted)
cd /app

# Check if the vendor directory exists and is not empty
if [ ! -d "vendor" ] || [ -z "$(ls -A vendor)" ]; then
    echo "Vendor directory is missing or empty. Running composer install..."
    composer install --optimize-autoloader
else
    echo "Vendor directory exists and is not empty. Skipping composer install."
fi

# Execute the main command (start PHP-FPM)
exec "$@"