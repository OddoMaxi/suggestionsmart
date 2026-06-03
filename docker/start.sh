#!/bin/sh
set -e

echo "==> SmartSuggest QR — Démarrage..."

# Générer la clé si absente
if [ -z "$APP_KEY" ]; then
    echo "==> Génération de la clé..."
    php artisan key:generate --force
fi

# Migrations
echo "==> Migrations..."
php artisan migrate --force --no-interaction

# Seed uniquement si la table roles est vide (première installation)
ROLES_COUNT=$(php artisan tinker --execute="echo \DB::table('roles')->count();" 2>/dev/null | tail -1)
if [ "$ROLES_COUNT" = "0" ] || [ -z "$ROLES_COUNT" ]; then
    echo "==> Premier démarrage — Seed initial..."
    php artisan db:seed --force --no-interaction
fi

# Cache production
echo "==> Cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Lien storage
php artisan storage:link 2>/dev/null || true

echo "==> Démarrage Nginx + PHP-FPM + Queue..."
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
