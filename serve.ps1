# Local serve helper for NITIP DI END (Windows).
# After enabling pdo_sqlite in php.ini, plain `php artisan serve` also works.
$ErrorActionPreference = 'Stop'
$port = if ($args.Count -gt 0) { $args[0] } else { '8000' }

Set-Location $PSScriptRoot
Remove-Item Env:PHP_INI_SCAN_DIR -ErrorAction SilentlyContinue

Write-Host "NITIP DI END -> http://127.0.0.1:${port}" -ForegroundColor Yellow
Write-Host "Optional HMR: npm run dev" -ForegroundColor DarkGray

& php artisan serve --host=127.0.0.1 --port=$port
