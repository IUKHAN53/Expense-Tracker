#!/usr/bin/env bash
#
# Pull the latest code from GitHub and refresh the deployment.
# Run on the VPS as root:  bash /var/www/expense-tracker/deploy.sh
#
set -euo pipefail

cd /var/www/expense-tracker

echo "==> Pulling latest code"
git pull origin main

echo "==> Installing PHP dependencies"
php8.3 "$(command -v composer)" install --no-dev --optimize-autoloader --no-interaction

echo "==> Running database migrations"
php8.3 artisan migrate --force

echo "==> Rebuilding caches"
php8.3 artisan config:cache
php8.3 artisan route:cache

echo "==> Fixing permissions"
chown -R www-data:www-data /var/www/expense-tracker

echo "==> Deploy complete."
