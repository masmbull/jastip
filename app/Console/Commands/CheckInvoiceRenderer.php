<?php

namespace App\Console\Commands;

use App\Services\InvoicePdfRenderer;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('invoice:check-renderer')]
#[Description('Cek apakah invoice PDF bisa dirender dengan Chrome headless (layout benar) atau jatuh ke DomPDF (layout rusak).')]
class CheckInvoiceRenderer extends Command
{
    public function handle(InvoicePdfRenderer $renderer): int
    {
        $node = config('browsershot.node_binary');
        $chrome = config('browsershot.chrome_path');
        $configured = config('browsershot.node_module_path');
        $detected = $renderer->resolveNodeModulePath();

        $this->line('User                 : '.get_current_user().' (APP_ENV: '.config('app.env').')');
        $this->line('Node                 : '.($node ?: 'node (dari PATH)'));
        $this->line('Chrome path          : '.($chrome ?: '(kosong — Puppeteer menebak sendiri)'));
        $this->line('node_modules di .env : '.($configured ?: '(kosong — dideteksi otomatis)'));
        $this->line('Puppeteer ditemukan : '.($detected ?: 'TIDAK ADA'));
        $this->newLine();

        if ($node && ! is_executable($node)) {
            $this->error("Node binary '$node' tidak bisa dieksekusi oleh user ini.");
        }
        if ($chrome && ! is_file($chrome)) {
            $this->error("Chrome binary '$chrome' tidak ada. Cek symlink /opt/chrome-stable (deploy/bootstrap.sh).");
        }
        if ($configured && ! is_dir($configured)) {
            $this->warn("BROWSERSHOT_NODE_MODULE_PATH '$configured' tidak ada — nilai ini diabaikan, pakai hasil deteksi otomatis.");
        }
        $this->newLine();

        $result = $renderer->selfTest();

        $this->line('Renderer invoice PDF : '.$result['renderer']);
        $this->line($result['message']);

        if (! $result['ok']) {
            $this->newLine();
            $this->error('Invoice PDF akan dirender dengan DomPDF, layout-nya rusak (flexbox tidak didukung).');
            $this->line('Perbaikan di server:');
            $this->line('  1. Instal Node.js 22+, Puppeteer global, dan Chrome (lihat docs/ec2-setup.md).');
            $this->line('  2. Isi BROWSERSHOT_* di .env, lalu php artisan config:clear && php artisan config:cache.');
            $this->line('  3. Jalankan ulang: php artisan invoice:check-renderer');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
