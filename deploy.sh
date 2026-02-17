#!/bin/bash
# Auto-deploy script for infrahub.click
# Triggered by GitHub webhook or manual execution

set -e

# Allow Composer to run as root (typical for VPS deployments)
export COMPOSER_ALLOW_SUPERUSER=1

APP_DIR="/var/www/infrahub.click"
LOG_FILE="/var/log/infrahub-deploy.log"

echo "========================================" >> "$LOG_FILE"
echo "🚀 Deployment started at $(date)" >> "$LOG_FILE"
echo "========================================" >> "$LOG_FILE"

cd "$APP_DIR"

# Pull latest changes
echo "📥 Pulling latest code..." >> "$LOG_FILE"
git pull origin main 2>&1 >> "$LOG_FILE"

# Install/update dependencies
echo "📦 Installing dependencies..." >> "$LOG_FILE"
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader 2>&1 >> "$LOG_FILE"

# Run database migrations
echo "🗄️ Running migrations..." >> "$LOG_FILE"
php artisan migrate --force 2>&1 >> "$LOG_FILE"

# Clear and rebuild caches
echo "⚡ Optimizing..." >> "$LOG_FILE"
php artisan optimize:clear 2>&1 >> "$LOG_FILE"
php artisan optimize 2>&1 >> "$LOG_FILE"
php artisan icons:cache 2>&1 >> "$LOG_FILE"

# Set proper permissions
echo "🔒 Setting permissions..." >> "$LOG_FILE"
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Restart queue workers if running
echo "🔄 Restarting queue workers..." >> "$LOG_FILE"
php artisan queue:restart 2>&1 >> "$LOG_FILE" || true

# Restart PHP-FPM and Apache
echo "🔁 Restarting services..." >> "$LOG_FILE"
systemctl restart php8.3-fpm 2>&1 >> "$LOG_FILE" || \
systemctl restart php8.2-fpm 2>&1 >> "$LOG_FILE" || true
systemctl reload apache2 2>&1 >> "$LOG_FILE" || true

echo "✅ Deployment complete at $(date)" >> "$LOG_FILE"
echo "" >> "$LOG_FILE"
