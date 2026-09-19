<#
  Deploy JASTIP (Laravel 13) to AWS EC2 over SSH.

  Prep: copy deploy\.env.production.example -> deploy\.env.production, isi nilainya.
  DB name/user/password diambil otomatis dari file itu, tidak perlu diketik ulang.

  Pertama kali (server masih kosong):
    .\deploy\deploy.ps1 -Server 1.2.3.4 -Domain jastip.example.com -Bootstrap

  Cloudflare Origin Certificate (biar SSL Full (strict)); jalankan ulang dengan
  -Bootstrap, script bootstrap idempotent:
    .\deploy\deploy.ps1 -Server 1.2.3.4 -Domain jastip.example.com -Bootstrap `
        -CertPem .\deploy\origin.pem -CertKey .\deploy\origin.key

  Deploy rutin (kode sudah di-push ke GitHub):
    .\deploy\deploy.ps1 -Server 1.2.3.4
#>
[CmdletBinding()]
param(
  [Parameter(Mandatory = $true)][string]$Server,
  [string]$SshUser = 'ubuntu',
  [string]$Key = "$env:USERPROFILE\.ssh\jastip.pem",
  [string]$AppPath = '/var/www/jastip',
  [string]$Branch = 'main',
  [string]$RepoUrl = 'https://github.com/masmbull/jastip.git',
  [string]$Domain = '_',
  [string]$PhpVer = '8.3',
  [string]$DbName,
  [string]$DbUser,
  [string]$DbPass,
  [string]$CertPem,
  [string]$CertKey,
  [switch]$Bootstrap,
  [switch]$SkipBuild
)

$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path -Parent $PSScriptRoot
$envFile = Join-Path $PSScriptRoot '.env.production'
$target = "$SshUser@$Server"
$sshArgs = @('-i', $Key, '-o', 'StrictHostKeyChecking=accept-new', '-o', 'ConnectTimeout=20')

function Invoke-Remote {
  param([Parameter(Mandatory = $true)][string]$Command)
  & ssh @sshArgs $target $Command
  if ($LASTEXITCODE -ne 0) { throw "remote command failed (exit $LASTEXITCODE): $Command" }
}

function Send-File {
  param(
    [Parameter(Mandatory = $true)][string]$LocalPath,
    [Parameter(Mandatory = $true)][string]$RemotePath
  )
  if (-not (Test-Path $LocalPath)) { throw "local path not found: $LocalPath" }
  & scp @sshArgs -r ($LocalPath -replace '\\', '/') "${target}:${RemotePath}"
  if ($LASTEXITCODE -ne 0) { throw "scp failed: $LocalPath -> $RemotePath" }
}

if (-not (Test-Path $Key))     { throw "ssh key not found: $Key" }
if (-not (Test-Path $envFile)) { throw "missing $envFile (copy .env.production.example, fill it in)" }

# DB creds diambil dari deploy/.env.production supaya MySQL di server & .env tidak mismatch
$prodVars = @{}
foreach ($line in Get-Content $envFile) {
  if ($line -match '^\s*([A-Z0-9_]+)\s*=\s*(.+?)\s*$') { $prodVars[$matches[1]] = $matches[2].Trim().Trim('"') }
}
if (-not $DbName) { $DbName = $prodVars['DB_DATABASE'] }
if (-not $DbUser) { $DbUser = $prodVars['DB_USERNAME'] }
if (-not $DbPass) { $DbPass = $prodVars['DB_PASSWORD'] }
if (-not $DbName) { $DbName = 'jastip' }
if (-not $DbUser) { $DbUser = 'jastip' }
if ($Bootstrap -and ($DbPass -in @($null, '', 'CHANGE_ME'))) { throw "set DB_PASSWORD di $envFile dulu" }
if ($DbPass -eq 'CHANGE_ME') { Write-Warning "DB_PASSWORD masih 'CHANGE_ME' di $envFile" }
if ($prodVars['APP_URL'] -match 'example\.com') { Write-Warning "APP_URL masih placeholder: $($prodVars['APP_URL'])" }

# warn kalau server bakal ambil kode yang belum di-push
$localHead = (git -C $projectRoot rev-parse HEAD).Trim()
$remoteHead = ((git -C $projectRoot ls-remote origin "refs/heads/$Branch") -split '\s+')[0]
if ($localHead -ne $remoteHead) { Write-Warning "local HEAD ($localHead) != origin/$Branch ($remoteHead) - push dulu kalau mau deploy kode terbaru" }

if ($CertPem) { Send-File -LocalPath $CertPem -RemotePath '/tmp/origin.pem' }
if ($CertKey) { Send-File -LocalPath $CertKey -RemotePath '/tmp/origin.key' }

if ($Bootstrap) {
  Send-File -LocalPath (Join-Path $PSScriptRoot 'bootstrap.sh') -RemotePath '/tmp/jastip-bootstrap.sh'
  if ($CertPem) { Invoke-Remote "sudo install -m 600 -o root -g root /tmp/origin.pem /etc/nginx/ssl/origin.pem" }
  if ($CertKey) { Invoke-Remote "sudo install -m 600 -o root -g root /tmp/origin.key /etc/nginx/ssl/origin.key" }
  Invoke-Remote ("sudo env APP_PATH='$AppPath' APP_DOMAIN='$Domain' REPO_URL='$RepoUrl' REPO_BRANCH='$Branch' " +
    "DB_NAME='$DbName' DB_USER='$DbUser' DB_PASS='$DbPass' PHP_VER='$PhpVer' bash /tmp/jastip-bootstrap.sh")
}

# 1. assets - public/build gitignored, jadi di-build lokal lalu di-upload
if (-not $SkipBuild) {
  Push-Location $projectRoot
  try {
    if (Test-Path (Join-Path $projectRoot 'package-lock.json')) { npm ci } else { npm install }
    if ($LASTEXITCODE -ne 0) { throw 'npm install failed' }
    npm run build
    if ($LASTEXITCODE -ne 0) { throw 'npm run build failed' }
  } finally { Pop-Location }
}
Invoke-Remote "rm -rf $AppPath/public/build"
Send-File -LocalPath (Join-Path $projectRoot 'public/build') -RemotePath "$AppPath/public"

# 2. production .env (single source of truth = deploy/.env.production)
Send-File -LocalPath $envFile -RemotePath "$AppPath/.env"

# 3. kode, dependency, migrasi, cache
$steps = @(
  "cd $AppPath",
  "git fetch --prune origin",
  "git checkout -f $Branch",
  "git reset --hard origin/$Branch",
  "composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader",
  "{ grep -q '^APP_KEY=base64:' .env || php artisan key:generate --force; }",
  "php artisan migrate --force",
  "{ php artisan storage:link >/dev/null 2>&1 || true; }",
  "php artisan optimize",
  "sudo chown -R ${SshUser}:www-data $AppPath",
  "sudo find $AppPath/storage $AppPath/bootstrap/cache -type d -exec chmod 2775 {} +",
  "sudo find $AppPath/storage $AppPath/bootstrap/cache -type f -exec chmod 664 {} +",
  "sudo systemctl reload php$PhpVer-fpm"
) -join ' && '
Invoke-Remote $steps

Write-Host "deploy OK -> $Domain ($Server) branch $Branch"
