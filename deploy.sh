#!/usr/bin/env bash
set -euo pipefail

# Chefchaouen.info - cPanel Terminal Deployment & Update Script
# Usage: ./deploy.sh

echo "=========================================="
echo " Deploying Chefchaouen.info from GitHub..."
echo "=========================================="

# Detect PHP binary (cPanel MultiPHP EA-PHP83/84 or default php)
PHP_BIN="${PHP_BIN:-$(command -v ea-php84 || command -v ea-php83 || command -v php)}"
COMPOSER_BIN="${COMPOSER_BIN:-$(command -v composer)}"

echo "Using PHP: $($PHP_BIN -v | head -n 1)"

if [ ! -f ".env" ]; then
    echo "Creating .env from .env.example (remember to set DB credentials & APP_URL)..."
    cp .env.example .env
fi

echo "1/6 Pulling latest changes from origin main..."
git pull origin main

echo "2/6 Installing Composer dependencies (production)..."
$PHP_BIN "$COMPOSER_BIN" install --no-dev --optimize-autoloader --no-interaction

if ! grep -q '^APP_KEY=base64:' .env; then
    echo "Generating APP_KEY..."
    $PHP_BIN artisan key:generate --force
fi

echo "3/6 Ensuring storage link and directory permissions..."
if [ ! -L "public/storage" ] && [ ! -e "public/storage" ]; then
    $PHP_BIN artisan storage:link || true
fi
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "4/6 Running database migrations..."
$PHP_BIN artisan migrate --force

echo "5/6 Clearing & rebuilding Laravel production caches..."
$PHP_BIN artisan optimize:clear
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache

echo "6/6 Deployment complete!"
