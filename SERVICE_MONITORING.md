# Service Monitoring Dashboard

## Overview

Admin page untuk monitoring status semua services, APIs, dan sistem NITIP DI END. Fitur ini membantu admin memantau kesehatan aplikasi secara real-time.

## Fitur

### 1. Service Status Monitoring
- **Database**: Check koneksi MySQL
- **Cache**: Check Redis/File cache system
- **API**: Check REST API endpoints availability
- **Email**: Check SMTP email service configuration
- **Storage**: Check file storage system writable status

### 2. System Information
- Disk Usage: Total, Used, Free disk space dengan progress bar
- Memory Usage: Current, Peak, dan Limit memory PHP
- Database Connection: Status koneksi database
- Cache System: Status cache (Redis/File)
- Storage: Status writable storage

### 3. Service Health Check
- Real-time monitoring dengan response time dalam milliseconds
- Last checked timestamp dengan human-readable format
- Error messages untuk debugging
- Bulk check all services button
- Individual service check button

## Akses

**URL Admin**: `/admin/services`

**Middleware**: Require admin role (middleware: `admin`)

**Routes**:
```php
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::post('/services/{service}/check', [ServiceController::class, 'check'])->name('services.check');
Route::get('/services/check-all', [ServiceController::class, 'checkAll'])->name('services.checkAll');
```

## Database Schema

### Services Table
```sql
CREATE TABLE services (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255),
    description VARCHAR(255),
    status ENUM('online', 'offline', 'degraded'),
    last_checked TIMESTAMP,
    response_time FLOAT,
    error_message TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Response Time Indicators

- **Green (Online)**: Service responsif dan accessible
- **Yellow (Degraded)**: Service berjalan tapi performance terganggu
- **Red (Offline)**: Service tidak accessible

## Usage Examples

### Check Individual Service

```bash
curl -X POST /admin/services/{service_id}/check \
  -H "Authorization: Bearer {token}" \
  -H "X-CSRF-Token: {csrf_token}"
```

### Check All Services

```bash
curl -X GET /admin/services/check-all \
  -H "Authorization: Bearer {token}"
```

## Performance Metrics

- **Response Time Calculation**: Start microtime → Perform check → End microtime (in milliseconds)
- **Cache Health**: Write-read-delete test
- **Database Health**: Simple PDO connection test
- **Storage Health**: Writable check on storage_path

## Security

- Rate limited via admin middleware
- CSRF token required
- Admin authentication required
- No sensitive data exposed in response

## Seeder

Services automatically seeded via `ServiceSeeder`:

```php
php artisan db:seed --class=ServiceSeeder
```

Services yang di-seed:
1. Database - Main MySQL database connection
2. Cache - Redis/File cache system
3. API - REST API endpoints
4. Email - Email service (SMTP)
5. Storage - File storage system

## Migration

File: `database/migrations/2025_10_07_000001_create_services_table.php`

Run:
```bash
php artisan migrate
```

## UI Components

### Status Indicator
```blade
<span class="inline-flex h-3 w-3 rounded-full" 
      :class="{'bg-green-500': status === 'online', 'bg-yellow-500': status === 'degraded', 'bg-red-500': status === 'offline'}">
</span>
```

### Health Card Grid
- Database status card
- Cache status card
- Storage status card
- Disk usage card with progress bar
- Memory usage card

### Services Table
- Service name & description
- Status badge with color indicator
- Response time (ms)
- Last checked (human readable)
- Check button (individual)
- Error message (if any)

## Future Enhancements

1. **Webhooks**: Send alert to Slack/Email jika service down
2. **Monitoring History**: Store status history untuk analytics
3. **Uptime Statistics**: Calculate uptime percentage
4. **Performance Graphs**: Chart response time trends
5. **Scheduled Checks**: Automated health checks via cron job
6. **Service Dependencies**: Show which service affects others

## Troubleshooting

### Service shows Offline

1. Check error message displayed
2. Verify service configuration in `.env`
3. Check system logs for errors
4. Ensure proper file permissions

### Response Time High

1. Check system resource usage
2. Verify database connection pool
3. Check cache backend status
4. Review application performance logs

## Technical Details

**Controller**: `App\Http\Controllers\Admin\ServiceController`
**Model**: `App\Models\Service`
**View**: `resources/views/admin/services/index.blade.php`

### Service Check Methods

```php
private function checkDatabaseStatus()      // Check PDO connection
private function checkCacheStatus()         // Write-read-delete test
private function checkApiStatus()           // Check API routes available
private function checkEmailStatus()         // Check email driver configured
private function checkStorageStatus()       // Check storage writable
```

### Helper Methods

```php
private function formatBytes($bytes)        // Format bytes to human readable (B, KB, MB, GB)
private function getDiskUsage()             // Get disk usage info
private function getMemoryUsage()           // Get PHP memory usage
```
