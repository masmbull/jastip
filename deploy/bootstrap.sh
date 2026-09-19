#!/usr/bin/env bash
# Provision Ubuntu 24.04 EC2 for JASTIP (Laravel 13). One time only.
# Normally invoked by deploy.ps1 -Bootstrap; manual use:
#   sudo env DB_PASS='secret' APP_DOMAIN='jastip.example.com' bash bootstrap.sh
set -euo pipefail

APP_PATH="${APP_PATH:-/var/www/jastip}"
APP_DOMAIN="${APP_DOMAIN:-_}"          # '_' = answer any host; set real domain for SSL
REPO_URL="${REPO_URL:-https://github.com/masmbull/jastip.git}"
REPO_BRANCH="${REPO_BRANCH:-main}"
DB_NAME="${DB_NAME:-jastip}"
DB_USER="${DB_USER:-jastip}"
DB_PASS="${DB_PASS:-}"
PHP_VER="${PHP_VER:-8.3}"
RUN_USER="${RUN_USER:-ubuntu}"
WEB_USER="${WEB_USER:-www-data}"
SSL_CERT="${SSL_CERT:-/etc/nginx/ssl/origin.pem}"
SSL_KEY="${SSL_KEY:-/etc/nginx/ssl/origin.key}"

[ "$(id -u)" -eq 0 ] || { echo "run as root: sudo -E bash bootstrap.sh" >&2; exit 1; }
[ -n "$DB_PASS" ]   || { echo "DB_PASS is required" >&2; exit 1; }

export DEBIAN_FRONTEND=noninteractive
apt-get update -y
apt-get install -y --no-install-recommends nginx git unzip curl ca-certificates mysql-server \
  "php${PHP_VER}-fpm" "php${PHP_VER}-cli" "php${PHP_VER}-mysql" "php${PHP_VER}-mbstring" \
  "php${PHP_VER}-xml" "php${PHP_VER}-curl" "php${PHP_VER}-zip" "php${PHP_VER}-gd" \
  "php${PHP_VER}-bcmath"

if ! command -v composer >/dev/null 2>&1; then
  curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
  php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
  rm -f /tmp/composer-setup.php
fi

systemctl enable --now "php${PHP_VER}-fpm" nginx mysql

# --- database ---
mysql -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost'; FLUSH PRIVILEGES;"

# --- code (deploy.ps1 handles deps, .env, migrations, caches) ---
mkdir -p "$(dirname "$APP_PATH")"
if [ ! -d "$APP_PATH/.git" ]; then
  git clone --branch "$REPO_BRANCH" "$REPO_URL" "$APP_PATH"
fi
chown -R "${RUN_USER}:${WEB_USER}" "$APP_PATH"

# --- nginx vhost (Cloudflare Origin Certificate -> Full (strict)) ---
mkdir -p /etc/nginx/ssl
if [ -f "$SSL_CERT" ] && [ -f "$SSL_KEY" ]; then
  LISTEN="listen 443 ssl;
  ssl_certificate     ${SSL_CERT};
  ssl_certificate_key ${SSL_KEY};"
  REDIRECT="server {
  listen 80;
  server_name ${APP_DOMAIN};
  return 301 https://\$host\$request_uri;
}"
else
  LISTEN="listen 80;"
  REDIRECT="# no Cloudflare Origin Certificate in /etc/nginx/ssl -> plain HTTP. Cloudflare SSL mode must be Flexible until you add it."
fi

cat > /etc/nginx/sites-available/jastip <<NGINX
${REDIRECT}
server {
  ${LISTEN}
  server_name ${APP_DOMAIN};
  root ${APP_PATH}/public;
  index index.php;
  client_max_body_size 12m;

  location / { try_files \$uri \$uri/ /index.php?\$query_string; }

  location ~ \.php$ {
    include snippets/fastcgi-php.conf;
    fastcgi_pass unix:/run/php/php${PHP_VER}-fpm.sock;
  }

  location ~ /\.(?!well-known).* { deny all; }
}
NGINX

ln -sf /etc/nginx/sites-available/jastip /etc/nginx/sites-enabled/jastip
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl reload nginx

echo "bootstrap OK. Next: .\\deploy\\deploy.ps1 -Server <ip>"
