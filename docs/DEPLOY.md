# Deployment Guide

## Prerequisites
- **PHP 8.5+** (wajib untuk security - mitigasi CVE PHP2Shell)
- Composer 2.x
- MySQL/MariaDB
- Node.js & npm (untuk build assets)

## Pre-Deployment Checklist

### 1. Environment Setup
```bash
# Copy .env.example ke .env di server
cp .env.example .env

# Edit .env untuk production:
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database credentials
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=jastip_db
DB_USERNAME=jastip_user
DB_PASSWORD=your_secure_password

# WhatsApp Gateway (optional)
WHATSAPP_GATEWAY=fonnte
WHATSAPP_FONNTE_TOKEN=your_token_here
```

### 2. Dependencies Installation
```bash
# Install PHP dependencies (production only)
composer install --no-dev --optimize-autoloader

# Install Node dependencies & build assets
npm ci
npm run build

# Generate app key (first time only)
php artisan key:generate
```

### 3. Database Migration
```bash
# Run migrations (safe - hanya ADD COLUMN)
php artisan migrate --force

# Optional: Seed QRIS & logo settings
php artisan db:seed --class=SetImageSettingsSeeder
```

### 4. Storage & Permissions
```bash
# Create storage symlink (first time only)
php artisan storage:link

# Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

Logo, favicon, and QRIS uploads in Admin Settings accept files up to 10 MB.
The deployment includes `public/.user.ini` to set PHP upload limits, while Nginx
allows up to 12 MB for the full multipart form request.

### 5. Cache & Optimization
```bash
# Clear old cache
php artisan optimize:clear

# Rebuild cache
php artisan optimize

# Config cache
php artisan config:cache

# Route cache
php artisan route:cache

# View cache
php artisan view:cache
```

### 6. Assets & Images
```bash
# Copy QRIS & logo images ke storage
cp /path/to/qris.jpg storage/app/public/settings/
cp /path/to/logo.jpg storage/app/public/settings/

# Atau jalankan seeder (sudah termasuk di step 3)
```

## Deployment Steps

### Standard Deployment
```bash
# 1. Pull latest code
git pull origin main

# 2. Install dependencies
composer install --no-dev --optimize-autoloader

# 3. Run migrations
php artisan migrate --force

# 4. Rebuild cache
php artisan optimize:clear
php artisan optimize

# 5. Reload PHP-FPM (adjust versi PHP)
sudo systemctl reload php8.5-fpm

# 6. Verify
curl -I https://yourdomain.com
```

### Zero-Downtime Deployment (Advanced)
```bash
# Use deployment tools like:
# - Deployer (deployer.org)
# - Envoy (laravel.com/docs/envoy)
# - GitHub Actions + SSH

# Example with symlink strategy:
/var/www/
  current -> releases/release-20261104-120000/
  releases/
    release-20261104-120000/
    release-20261103-150000/
  shared/
    storage/
    .env
```

## Post-Deployment Verification

### 1. Health Checks
```bash
# Check app runs
curl https://yourdomain.com

# Check admin login
curl https://yourdomain.com/admin/login

# Check API endpoints
curl https://yourdomain.com/api/health
```

### 2. Database Check
```bash
# Verify migrations
php artisan migrate:status

# Check rekening config
php artisan tinker
>>> config('invoice.accounts')
```

### 3. Test Invoice
- Login ke admin
- Buat invoice manual baru
- Verify: deposit per item, 3 rekening tampil
- Test kirim via WhatsApp (kalau gateway configured)

## Rollback Procedure

### Quick Rollback
```bash
# 1. Checkout previous commit
git log --oneline -5  # lihat commit history
git checkout <previous-commit-hash>

# 2. Reinstall dependencies
composer install --no-dev

# 3. Rollback migration (if needed)
php artisan migrate:rollback --step=1

# 4. Clear cache
php artisan optimize:clear
php artisan optimize

# 5. Reload PHP-FPM
sudo systemctl reload php8.5-fpm
```

## Troubleshooting

### Issue: Composer.lock out of sync
```bash
# Solution: Update composer.lock
composer update --lock
git add composer.lock
git commit -m "chore: update composer.lock"
```

### Issue: PHP version or FPM service not found
The application requires PHP 8.5 for both CLI and FPM. On an existing server,
run the bootstrap once to install PHP 8.5, set it as the default CLI, and update
Nginx to its FPM socket. The deploy script continues with a deployment after
bootstrap:
```powershell
.\deploy\deploy.ps1 -Server <EC2_HOST> -Bootstrap
```

Confirm both CLI and FPM before rerunning a failed GitHub Actions deployment:
```bash
php -v
sudo systemctl status php8.5-fpm
```

### Issue: Permission denied errors
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Issue: 500 error after deployment
```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear all cache
php artisan optimize:clear

# Verify .env
cat .env | grep APP_ENV  # should be production
```

### Issue: Assets not loading (404)
```bash
# Rebuild assets
npm run build

# Check public/build exists
ls -la public/build/
```

### Issue: Invoice images not showing
```bash
# Verify storage symlink
ls -la public/storage  # should point to ../storage/app/public

# Recreate if missing; deployments now create this link automatically
php artisan storage:link
```

## Security Checklist

- ✅ PHP 8.5+ (CVE mitigation)
- ✅ APP_DEBUG=false in production
- ✅ Strong DB passwords
- ✅ HTTPS enabled (SSL certificate)
- ✅ Firewall configured (only 80, 443, 22)
- ✅ Regular backups (database + storage/)
- ✅ .env file permissions (chmod 600)
- ✅ Composer audit (composer audit)

## Monitoring

### Log Files
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# PHP-FPM logs
tail -f /var/log/php8.5-fpm.log

# Nginx logs
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log
```

### Performance
```bash
# Check PHP opcache
php -i | grep opcache

# Check queue workers (if using)
php artisan queue:work --daemon
```

## Backup Strategy

### Database Backup
```bash
# Manual backup
php artisan backup:database

# Auto backup (daily cron)
0 2 * * * cd /var/www/jastip && php artisan backup:database
```

### Full Backup
```bash
# Database + storage + .env
tar -czf backup-$(date +%Y%m%d).tar.gz \
    .env \
    storage/ \
    database/backups/
```

## CI/CD Integration

GitHub Actions workflow sudah ada di `.github/workflows/tests.yml`:
- Auto test on push/PR
- PHP 8.5 only
- Composer validate

Tinggal tambahkan deploy step kalau perlu auto-deploy.

## Support

- Docs: `docs/` folder
- WhatsApp Gateway: `docs/WHATSAPP-GATEWAY.md`
- TODO: `docs/TODO.md`
