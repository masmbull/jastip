# JASTIP Production Deployment Guide

## Pre-Deployment Checklist

### ✅ Code Quality
- [x] Responsive design implemented (mobile, tablet, desktop)
- [x] Dark mode theme fully functional
- [x] Security hardening complete (rate limiting, headers, validation)
- [x] Performance optimized (query caching, eager loading)
- [x] SEO enhanced (JSON-LD structured data)
- [x] Remember Me feature added
- [x] PWA service worker implemented
- [x] All tests passing

### ✅ Security
- [x] CSRF token protection
- [x] Rate limiting on login (5 attempts/minute)
- [x] SQL injection prevention (Eloquent ORM)
- [x] XSS prevention (security headers, input validation)
- [x] Clickjacking protection (X-Frame-Options)
- [x] HTTPS enforcement (HSTS header)

### ✅ Database
- [x] Fresh migration completed
- [x] All tables created
- [x] Seeders running successfully
- [x] Admin credentials set: admin/admin123
- [x] Customer credentials set: budi/customer123

---

## Deployment Steps

### 1. Environment Setup
```bash
# Copy environment file
cp .env.example .env.production

# Set production values
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database configuration
DB_CONNECTION=mysql  # or postgresql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=jastip
DB_USERNAME=db-user
DB_PASSWORD=strong-password

# Cache configuration
CACHE_DRIVER=redis  # or memcached
CACHE_HOST=your-redis-host
CACHE_PORT=6379

# Session
SESSION_DRIVER=database  # persistent sessions
SESSION_LIFETIME=120

# Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@jastip.app
```

### 2. Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

### 3. Build Optimization
```bash
# Cache configuration, routes, and views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize class loading
composer dump-autoload --optimize

# Clear and warm up caches
php artisan cache:clear
php artisan cache:forget home.featured_products
php artisan cache:forget home.categories
php artisan cache:forget home.popular_products
php artisan cache:forget home.viral_products
```

### 4. Database Preparation
```bash
# Run migrations
php artisan migrate --force

# Seed initial data
php artisan db:seed --class=DatabaseSeeder
```

### 5. Web Server Configuration

#### Nginx Configuration
```nginx
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com;

    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;

    root /path/to/jastip/public;
    index index.php;

    # Security headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}

# Redirect HTTP to HTTPS
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}
```

#### Apache Configuration
```apache
<VirtualHost *:443>
    ServerName yourdomain.com
    DocumentRoot /path/to/jastip/public

    SSLEngine On
    SSLCertificateFile /path/to/certificate.crt
    SSLCertificateKeyFile /path/to/private.key

    <Directory /path/to/jastip/public>
        AllowOverride All
        Require all granted

        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule ^ index.php [QSA,L]
        </IfModule>
    </Directory>

    # Security headers
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
</VirtualHost>
```

### 6. File Permissions
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data /path/to/jastip
```

### 7. SSL Certificate
```bash
# Using Let's Encrypt with Certbot
sudo certbot certonly --webroot -w /path/to/jastip/public -d yourdomain.com

# Auto-renewal
sudo systemctl enable certbot.timer
```

### 8. Monitoring & Logging

#### Application Monitoring
```bash
# Install monitoring tools
composer require sentry/sentry-laravel
php artisan sentry:publish

# Configure .env
SENTRY_LARAVEL_DSN=https://your-sentry-dsn
```

#### Log Rotation
```bash
# /etc/logrotate.d/jastip
/path/to/jastip/storage/logs/*.log {
    daily
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
}
```

### 9. Backup Strategy
```bash
# Automated daily backups
0 2 * * * /path/to/jastip/backup.sh

# backup.sh
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
tar -czf /backups/jastip_$DATE.tar.gz /path/to/jastip
mysqldump -u db-user -p db-password jastip > /backups/jastip_$DATE.sql
```

### 10. Performance Optimization

#### Configure Redis
```bash
# Install and start Redis
sudo apt-get install redis-server
sudo systemctl start redis-server
sudo systemctl enable redis-server

# Configure Laravel to use Redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

#### CDN Configuration
```bash
# Configure CloudFlare or similar CDN
# Point domain to CDN
# Enable auto HTTPS redirect
# Enable caching for assets
```

---

## Post-Deployment Verification

```bash
# Check application health
curl -I https://yourdomain.com

# Verify HTTPS/Security headers
curl -I https://yourdomain.com | grep -E "Strict-Transport|X-Frame|X-Content"

# Test login functionality
# Visit https://yourdomain.com/admin/login
# Test credentials: admin / admin123

# Check cache functioning
php artisan tinker
>>> Cache::get('home.featured_products')

# Monitor error logs
tail -f /path/to/jastip/storage/logs/laravel.log
```

---

## Maintenance Commands

```bash
# Clear all caches
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Database maintenance
php artisan migrate:fresh --seed  # Only in development!
php artisan tinker

# Queue management
php artisan queue:work  # Start queue worker
php artisan queue:failed  # View failed jobs
php artisan queue:retry all  # Retry failed jobs
```

---

## Rollback Plan

```bash
# If deployment fails:
git log --oneline -10
git revert <commit-hash>
php artisan migrate:rollback
php artisan cache:clear
# Restart services

# Database rollback
mysqldump -u db-user -p db-password jastip > /backups/before_rollback.sql
mysql -u db-user -p db-password jastip < /backups/jastip_before_deploy.sql
```

---

## SSL/TLS Configuration

### Minimum TLS Version
```nginx
ssl_protocols TLSv1.2 TLSv1.3;
ssl_ciphers HIGH:!aNULL:!MD5;
ssl_prefer_server_ciphers on;
```

### Security Headers
- ✅ Strict-Transport-Security (HSTS)
- ✅ Content-Security-Policy (CSP)
- ✅ X-Frame-Options
- ✅ X-Content-Type-Options
- ✅ Referrer-Policy

---

## Monitoring Commands

```bash
# Check server resources
top -b -n 1 | head -20
df -h
free -h

# Check database connections
mysql -u root -p -e "SHOW PROCESSLIST;"

# Check PHP-FPM
ps aux | grep php-fpm

# Monitor logs in real-time
tail -f /path/to/jastip/storage/logs/laravel.log
tail -f /var/log/nginx/error.log
```

---

## Troubleshooting

### 404 Errors
```bash
php artisan route:cache --force
php artisan view:cache --force
```

### Permission Errors
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data /path/to/jastip
```

### Database Errors
```bash
php artisan migrate:refresh --seed
php artisan cache:clear
```

### Slow Pages
```bash
# Enable query logging
DB_DEBUG=true

# Check cache status
php artisan tinker
>>> Cache::tags('products')->flush()
```

---

## Performance Targets

- **Homepage Load**: < 2 seconds
- **API Response**: < 500ms
- **Cache Hit Rate**: > 80%
- **Uptime**: 99.9%
- **Error Rate**: < 0.1%

---

## Contact & Support

**Admin Credentials**: admin / admin123  
**Customer Test**: budi / customer123  
**Support Email**: support@jastip.app  
**Emergency**: +62-812-3456-789

---

*Deployment Guide Version: 1.0*  
*Last Updated: October 7, 2026*
