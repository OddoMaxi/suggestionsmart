# Guide de Déploiement — SmartSuggest QR

## Prérequis serveur (Ubuntu 24.04)

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx php8.3-fpm php8.3-mysql php8.3-mbstring \
  php8.3-xml php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath \
  mysql-server git unzip curl certbot python3-certbot-nginx
```

## Installation Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

## Déploiement de l'application

```bash
cd /var/www
git clone <votre-repo> smartsuggest
cd smartsuggest
composer install --optimize-autoloader --no-dev
cp .env.example .env
php artisan key:generate
```

## Configuration .env production

```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

DB_HOST=127.0.0.1
DB_DATABASE=smartsuggest
DB_USERNAME=smartsuggest_user
DB_PASSWORD=VotreMotDePasseSecurise

MAIL_MAILER=smtp
MAIL_HOST=mail.votre-domaine.com
MAIL_PORT=587
MAIL_USERNAME=noreply@votre-domaine.com
MAIL_PASSWORD=VotreMotDePasseMail

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

## Base de données MySQL

```sql
CREATE DATABASE smartsuggest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'smartsuggest_user'@'localhost' IDENTIFIED BY 'VotreMotDePasseSecurise';
GRANT ALL PRIVILEGES ON smartsuggest.* TO 'smartsuggest_user'@'localhost';
FLUSH PRIVILEGES;
```

## Migrations et seeds

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan filament:upgrade
```

## Optimisation Laravel production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan icons:cache
```

## Configuration Nginx

```nginx
server {
    listen 80;
    server_name votre-domaine.com www.votre-domaine.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name votre-domaine.com;
    root /var/www/smartsuggest/public;
    index index.php;

    ssl_certificate     /etc/letsencrypt/live/votre-domaine.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/votre-domaine.com/privkey.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy "strict-origin-when-cross-origin";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* { deny all; }

    client_max_body_size 10M;
}
```

## SSL Let's Encrypt

```bash
sudo certbot --nginx -d votre-domaine.com -d www.votre-domaine.com
```

## Permissions fichiers

```bash
sudo chown -R www-data:www-data /var/www/smartsuggest
sudo chmod -R 755 /var/www/smartsuggest
sudo chmod -R 775 /var/www/smartsuggest/storage
sudo chmod -R 775 /var/www/smartsuggest/bootstrap/cache
```

## Queue Worker (Supervisor)

```ini
; /etc/supervisor/conf.d/smartsuggest-worker.conf
[program:smartsuggest-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/smartsuggest/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/smartsuggest-worker.log
```

```bash
sudo supervisorctl reread && sudo supervisorctl update && sudo supervisorctl start smartsuggest-worker:*
```

## Sauvegarde automatique (cron)

```bash
# crontab -e (utilisateur www-data ou root)
0 2 * * * mysqldump -u smartsuggest_user -p'VotreMotDePasseSecurise' smartsuggest | gzip > /var/backups/smartsuggest/db-$(date +\%Y\%m\%d).sql.gz
0 3 * * 0 tar -czf /var/backups/smartsuggest/storage-$(date +\%Y\%m\%d).tar.gz /var/www/smartsuggest/storage/app/public
# Garder 30 jours de backup
0 4 * * * find /var/backups/smartsuggest -mtime +30 -delete
* * * * * cd /var/www/smartsuggest && php artisan schedule:run >> /dev/null 2>&1
```

## Vérification finale

```bash
php artisan about
php artisan route:list
curl -I https://votre-domaine.com/suggestion
```
