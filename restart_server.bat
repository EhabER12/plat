@echo off
echo === Cleaning Laravel caches ===
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo === Optimizing Laravel ===
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo === Starting Laravel server ===
php artisan serve 