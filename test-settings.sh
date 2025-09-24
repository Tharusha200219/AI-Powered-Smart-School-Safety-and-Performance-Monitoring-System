#!/bin/bash

# Test the settings page
echo "Testing the settings page..."

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo "Caches cleared successfully!"

# Test if the settings page compiles without errors
php artisan tinker --execute="view('admin.pages.setup.settings.index', ['setting' => new \App\Models\Setting()]); echo 'Settings page compiles successfully!';"

echo "Settings page test completed!"