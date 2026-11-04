#!/usr/bin/env bash
# Provision Ubuntu 24.04 EC2 for JASTIP (Laravel 13). One time only.
# Ubuntu 24.04 ships an older PHP than Laravel 13 + this app wants, so we use
# the ondrej/php PPA to get a current, maintained patch release. Verify the
# release you land on against the official PHP security advisories list
# (https://www.php.net/security/) before flying this into production.
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
PHP_VER="${PHP_VER:-8.5}"
RUN_USER="${RUN_USER:-ubuntu}"
WEB_USER="${WEB_USER:-www-data}"
SSL_CERT="${SSL_CERT:-/etc/nginx/ssl/origin.pem}"
SSL_KEY="${SSL_KEY:-/etc/nginx/ssl/origin.key}"

[ "$(id -u)" -eq 0 ] || { echo "run as root: sudo -E bash bootstrap.sh" >&2; exit 1; }
[ -n "$DB_PASS" ]   || { echo "DB_PASS is required" >&2; exit 1; }

export DEBIAN_FRONTEND=noninteractive
apt-get update -y
apt-get install -y --no-install-recommends software-properties-common ca-certificates
add-apt-repository -y ppa:ondrej/php
apt-get update -y
apt-get install -y --no-install-recommends nginx git unzip curl ca-certificates mysql-server \
  "php${PHP_VER}-fpm" "php${PHP_VER}-cli" "php${PHP_VER}-mysql" "php${PHP_VER}-mbstring" \
  "php${PHP_VER}-xml" "php${PHP_VER}-curl" "php${PHP_VER}-zip" "php${PHP_VER}-gd" \
  "php${PHP_VER}-bcmath"

# Minimum PHP patch level per minor branch. Keep in sync with the newest
# security release for each branch (https://www.php.net/security/).
INSTALLED="$(php${PHP_VER} -r 'echo PHP_VERSION;')"
MIN_PHP=""
case "$PHP_VER" in
  8.2) MIN_PHP="8.2.35" ;;
  8.3) MIN_PHP="8.3.36" ;;
  8.4) MIN_PHP="8.4.27" ;;
  8.5) MIN_PHP="8.5.12" ;;
esac
if [ -n "$MIN_PHP" ]; then
  # sort -V -C: sukses kalau input SUDAH terurut versi, yaitu $MIN_PHP <= $INSTALLED.
  if ! printf '%s\n%s\n' "$MIN_PHP" "$INSTALLED" | sort -V -C; then
    echo "ERROR: installed PHP $INSTALLED < minimum $MIN_PHP. Aborting." >&2; exit 1
  fi
fi

# Keep Composer and Artisan on the same CLI version as the configured FPM service.
update-alternatives --set php "/usr/bin/php${PHP_VER}"
CLI_VERSION="$(php -r 'echo PHP_VERSION;')"
if [ "$CLI_VERSION" != "$INSTALLED" ]; then
  echo "ERROR: default PHP CLI is $CLI_VERSION, expected $INSTALLED." >&2
  exit 1
fi

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

# --- code (the GitHub Actions deploy handles releases, .env, and caches) ---
if [ -d "$APP_PATH/.git" ]; then
  :
elif [ -f "$APP_PATH/artisan" ] && [ -d "$APP_PATH/public" ]; then
  echo "Using existing application directory: $APP_PATH"
elif [ -e "$APP_PATH" ]; then
  echo "ERROR: $APP_PATH exists but is not a Git checkout or Laravel application. Refusing to modify it." >&2
  exit 1
else
  mkdir -p "$(dirname "$APP_PATH")"
  git clone --branch "$REPO_BRANCH" "$REPO_URL" "$APP_PATH"
fi
chown -R "${RUN_USER}:${WEB_USER}" "$APP_PATH"

# --- Hardening (defense in depth) ---
# Ditulis ke conf.d sebagai DROP-IN, bukan mengedit php.ini milik paket:
# apt akan menimpa php.ini saat upgrade, sedangkan conf.d ikut terbaca FPM.
PHP_CONFD="/etc/php/${PHP_VER}/fpm/conf.d"
if [ -d "$PHP_CONFD" ]; then
  cat > "$PHP_CONFD/99-jastip-hardening.ini" <<'INI'
; Dikelola oleh deploy/bootstrap.sh
session.upload_progress.enabled = 0
INI
fi
FPM_POOL="/etc/php/${PHP_VER}/fpm/pool.d/www.conf"
if [ -f "$FPM_POOL" ]; then
  # Recycle workers periodically (bounds the blast radius of a leaked worker)
  # without paying a process respawn on literally every request.
  # Sed hanya menimpa baris yang ADA; kalau belum ada, ditambahkan sekali.
  sed -i -E 's|^[;[:space:]]*pm\.max_requests[[:space:]]*=.*|pm.max_requests = 500|' "$FPM_POOL"
  grep -qE '^pm\.max_requests[[:space:]]*=' "$FPM_POOL" || echo 'pm.max_requests = 500' >> "$FPM_POOL"
fi

# Gagal di sini (config rusak) jauh lebih baik daripada meninggalkan FPM
# yang tidak bisa start setelah nginx di-reload di bawah.
if command -v "php-fpm${PHP_VER}" >/dev/null 2>&1; then
  "php-fpm${PHP_VER}" -t
else
  echo "WARN: php-fpm${PHP_VER} tidak ditemukan, tes config FPM dilewati." >&2
fi

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
systemctl reload "php${PHP_VER}-fpm"

echo "bootstrap OK. Next: .\\deploy\\deploy.ps1 -Server <ip>"
