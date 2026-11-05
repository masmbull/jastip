# EC2 Deployment Setup — NITIPDIEND

This document covers all server-side requirements for the full invoice stack,
including Browsershot (Chrome/Puppeteer) for pixel-perfect PDF generation.

---

## 1. Base Requirements

```bash
sudo apt update && sudo apt upgrade -y

# PHP 8.5 + extensions
sudo apt install -y php8.5 php8.5-fpm php8.5-cli php8.5-mbstring \
    php8.5-xml php8.5-curl php8.5-zip php8.5-gd php8.5-bcmath \
    php8.5-intl php8.5-pdo php8.5-mysql php8.5-redis

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Nginx
sudo apt install -y nginx

# MySQL client (if using RDS)
sudo apt install -y mysql-client
```

---

## 2. Node.js 22+ (required for Puppeteer / Browsershot)

Browsershot requires Node 22 LTS or higher.

```bash
# Install Node.js 22 via NodeSource
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs

# Verify
node --version   # must be v22.x or higher
npm --version
```

---

## 3. Puppeteer + Chrome

```bash
# Install Puppeteer globally (downloads bundled Chromium)
sudo npm install -g puppeteer

# Let Puppeteer download and install its Chrome binary
sudo npx puppeteer browsers install chrome

# Chrome runtime dependencies (Ubuntu 24.04)
sudo apt install -y \
    libasound2t64 libatk1.0-0 libatk-bridge2.0-0 libcairo2 libcups2 \
    libdbus-1-3 libexpat1 libfontconfig1 libgbm1 libgcc1 libglib2.0-0 \
    libgtk-3-0 libnspr4 libpango-1.0-0 libpangocairo-1.0-0 libstdc++6 \
    libx11-6 libx11-xcb1 libxcb1 libxcomposite1 libxcursor1 libxdamage1 \
    libxext6 libxfixes3 libxi6 libxrandr2 libxrender1 libxss1 libxtst6 \
    ca-certificates fonts-liberation xdg-utils wget

# Ubuntu 22.04: replace libasound2t64 with libasound2

# Find the Chrome executable path (you'll need this for .env):
node -e "const {executablePath} = require('puppeteer'); console.log(executablePath())"
```

---

## 4. AppArmor fix (Ubuntu 24.04 — allows Chrome sandbox)

```bash
sudo sysctl -w kernel.apparmor_restrict_unprivileged_userns=0
sudo sysctl -w kernel.apparmor_restrict_unprivileged_unconfined=0

# Make permanent
echo "kernel.apparmor_restrict_unprivileged_userns=0" | sudo tee -a /etc/sysctl.d/99-browsershot.conf
echo "kernel.apparmor_restrict_unprivileged_unconfined=0" | sudo tee -a /etc/sysctl.d/99-browsershot.conf
sudo sysctl --system
```

---

## 5. Application Deployment

```bash
cd /var/www/nitipdiend

# Pull latest code
git pull origin main

# PHP dependencies (includes spatie/browsershot)
composer install --no-dev --optimize-autoloader

# Node dependencies (includes puppeteer)
npm ci

# Laravel setup
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

---

## 6. Environment Variables

Add these to `/var/www/nitipdiend/.env` (production):

```env
APP_ENV=production
APP_DEBUG=false

# Browsershot — set these after running the node command in step 3
BROWSERSHOT_NODE_PATH=/usr/local/bin/node
BROWSERSHOT_NPM_PATH=/usr/local/bin/npm
BROWSERSHOT_CHROME_PATH=/root/.cache/puppeteer/chrome/linux-*/chrome-linux64/chrome
# Chrome args for EC2 (running as root or without sandbox):
BROWSERSHOT_CHROME_ARGS=no-sandbox,disable-setuid-sandbox,disable-dev-shm-usage
```

> **Note:** `disable-dev-shm-usage` is important on EC2 — the default `/dev/shm`
> is only 64MB which causes Chrome to crash on heavy pages.

---

## 7. Nginx + PHP-FPM Config (example)

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/nitipdiend/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.5-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Increase timeout for PDF generation (Chrome can take a few seconds)
    fastcgi_read_timeout 60;
    proxy_read_timeout 60;
}
```

---

## 8. File Permissions

```bash
sudo chown -R www-data:www-data /var/www/nitipdiend
sudo chmod -R 755 /var/www/nitipdiend
sudo chmod -R 775 /var/www/nitipdiend/storage
sudo chmod -R 775 /var/www/nitipdiend/bootstrap/cache
```

---

## 9. Verify Browsershot Works

```bash
# Quick test — should output a PDF binary
cd /var/www/nitipdiend
php artisan tinker
>>> \Spatie\Browsershot\Browsershot::html('<h1>Test</h1>')->addChromiumArguments(['no-sandbox','disable-setuid-sandbox','disable-dev-shm-usage'])->pdf();
# Should return a binary string starting with %PDF
```

---

## Summary Checklist

- [ ] PHP 8.5 + all extensions
- [ ] Composer installed
- [ ] Node.js 22+
- [ ] `npm install -g puppeteer` + `npx puppeteer browsers install chrome`
- [ ] Chrome runtime apt packages installed
- [ ] AppArmor sysctl applied (Ubuntu 24.04)
- [ ] `composer install --no-dev` run after deploy
- [ ] `npm ci` run after deploy
- [ ] `.env` has `BROWSERSHOT_*` variables set
- [ ] Nginx timeout increased to 60s
- [ ] Storage symlink created (`php artisan storage:link`)
