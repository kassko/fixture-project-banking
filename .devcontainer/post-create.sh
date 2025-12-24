#!/bin/bash
set -e

echo " Installing Composer dependencies..."
composer install --prefer-dist --no-interaction

echo "🗄️ Setting up database (SQLite for development)..."
if [ ! -f var/data.db ]; then
    php bin/console doctrine:database:create --if-not-exists 2>/dev/null || true
    php bin/console doctrine:schema:create 2>/dev/null || true
fi

echo "🧹 Clearing cache..."
php bin/console cache:clear

echo "✅ Development environment ready!"
echo ""
echo "📋 Useful commands:"
echo "  symfony server:start    - Start the Symfony development server"
echo "  php bin/console         - Run Symfony console commands"
echo "  composer test           - Run tests"
echo ""
