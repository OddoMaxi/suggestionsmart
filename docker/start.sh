#!/bin/sh
set -e

echo "==> SmartSuggest QR — Démarrage..."

# En production Docker, APP_KEY vient de la variable d'environnement Render
# Pas besoin de .env — Laravel lit directement les variables d'env
if [ -z "$APP_KEY" ]; then
    echo "ERREUR : APP_KEY non défini. Ajoutez-le dans les variables d'environnement Render."
    exit 1
fi

# Migrations
echo "==> Migrations..."
php artisan migrate --force --no-interaction

# Seed uniquement au premier démarrage (table roles vide)
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
