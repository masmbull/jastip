<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;

/**
 * Render invoice HTML menjadi PDF.
 *
 * Dua renderer, dengan prioritas yang jelas:
 *
 *   chromium  — Chrome headless via Browsershot. Satu-satunya renderer yang
 *               mendukung layout invoice (flexbox). Hasilnya sama persis
 *               dengan pratinjau di browser.
 *   dompdf    — jaring pengaman. DomPDF tidak mendukung flexbox sama sekali
 *               (display:flex di-drop menjadi block), jadi template invoice
 *               pecah: kolom kanan header keluar dari kertas, kartu payment
 *               bersusun vertikal, dan QR bisa terpotong. PDF DomPDF TIDAK
 *               boleh dianggap layak kirim ke pelanggan.
 *
 * Karena itu renderer yang dipakai selalu dikembalikan dan selalu dicatat,
 * supaya kegagalan downgrade kelihatan (log + header respons) dan bukan
 * diam-diam menghasilkan invoice rusak.
 */
class InvoicePdfRenderer
{
    public const RENDERER_CHROMIUM = 'chromium';

    public const RENDERER_DOMPDF = 'dompdf';

    /**
     * Render PDF untuk invoice.
     *
     * @return array{pdf: string, renderer: string, degraded: bool}
     */
    public function render(string $html): array
    {
        $pdf = null;
        $renderer = self::RENDERER_CHROMIUM;

        if (class_exists(Browsershot::class)) {
            try {
                $pdf = $this->renderWithChrome($html);
            } catch (\Throwable $e) {
                report($e);
                Log::warning('Invoice PDF: Chrome rendering failed, falling back to DomPDF. The fallback layout is degraded because DomPDF has no flexbox support.', [
                    'exception' => $e->getMessage(),
                ]);
                $pdf = null;
            }
        }

        if ($pdf === null) {
            $renderer = self::RENDERER_DOMPDF;
            $pdf = $this->renderWithDompdf($html);
        }

        // BOM di depan PDF bikin header rusak, jadi dibuang.
        return [
            'pdf' => ltrim($pdf, "\xEF\xBB\xBF"),
            'renderer' => $renderer,
            'degraded' => $renderer === self::RENDERER_DOMPDF,
        ];
    }

    /**
     * Render dengan Chrome headless (layout invoice upheld).
     */
    private function renderWithChrome(string $html): string
    {
        $browsershot = Browsershot::html($html)
            ->format('A4')
            ->margins(8, 10, 8, 10)   // top, right, bottom, left (mm) — must match @page in pdf.blade.php
            ->scale(1)
            ->printBackground()
            ->waitUntilNetworkIdle()     // wait for Google Fonts & images
            ->timeout(30);

        // Windows: PHP exec() may not inherit the interactive PATH,
        // so binary locations are configured explicitly via .env.
        if ($nodePath = config('browsershot.node_binary')) {
            $browsershot->setNodeBinary($nodePath);
        }
        if ($npmPath = config('browsershot.npm_binary')) {
            $browsershot->setNpmBinary($npmPath);
        }
        if ($chromePath = config('browsershot.chrome_path')) {
            $browsershot->setChromePath($chromePath);
        }

        // Path global node_modules: dipakai server karena node_modules
        // project tidak ikut ter-deploy (lihat config/browsershot.php).
        // Path-nya dideteksi otomatis — lokasi global install berbeda antara
        // distro (/usr/lib/node_modules) dan NodeSource (/usr/local/lib),
        // jadi menebaknya di .env rawan salah.
        $nodeModulePath = $this->resolveNodeModulePath();
        if ($nodeModulePath !== null) {
            $browsershot->setNodeModulePath($nodeModulePath);
        }

        $extraArgs = config('browsershot.chromium_args', []);
        if (! empty($extraArgs)) {
            $browsershot->addChromiumArguments($extraArgs);
        }

        // Required in production containers / EC2 running as root.
        if (config('app.env') !== 'local') {
            $browsershot->addChromiumArguments([
                'no-sandbox',
                'disable-setuid-sandbox',
                'disable-dev-shm-usage',   // /dev/shm only 64MB on EC2, Chrome crashes without this
                // Crashpad butuh direktori database yang bisa ditulis. Tanpa
                // ini Chrome gagal start dengan "chrome_crashpad_handler:
                // --database is required".
                'disable-crash-reporter',
            ]);
        }

        // Chrome butuh HOME yang bisa ditulis untuk crashpad dan cache.
        // Saat PHP-FPM (user www-data) menjalankan render, HOME bisa menunjuk
        // ke direktori milik user lain, sehingga Chrome gagal start dengan
        // "chrome_crashpad_handler: --database is required".
        //
        // setNodeEnv() dipakai, bukan setEnvironmentOptions(): env di
        // browser.cjs di-spread SEBELUM process.env, jadi process.env yang
        // menang. setNodeEnv() men-set env di level proses node, di luar
        // jangkauan spread itu, jadi benar-benar overriding.
        $home = $this->writableHomeForChrome();
        if ($home !== null) {
            $browsershot->setNodeEnv(['HOME' => $home]);
            $browsershot->setUserDataDir($home.'/chrome-profile');
        }

        return $browsershot->pdf();
    }

    /**
     * Folder HOME yang bisa ditulis oleh user PHP-FPM.
     *
     * Chrome menyimpan crashpad database dan cache di HOME. Kalau HOME
     * menunjuk ke folder milik user lain (mis. /home/ubuntu saat render
     * dijalankan lewat sudo -u www-data), Chrome gagal start.
     *
     * @return string|null Path HOME yang aman, null kalau tak ada yang cocok.
     */
    public function writableHomeForChrome(): ?string
    {
        $candidates = [];

        $home = getenv('HOME');
        if (is_string($home) && $home !== '') {
            $candidates[] = $home;
        }

        // Folder khusus yang pasti bisa ditulis user web.
        $candidates[] = storage_path('app/chrome');
        $candidates[] = sys_get_temp_dir().'/jastip-chrome';

        foreach ($candidates as $path) {
            if (! is_dir($path)) {
                // Several requests can render at once, so a "folder already
                // exists" race must not be treated as a failure.
                @mkdir($path, 0775, true);
            }
            if (is_dir($path) && is_writable($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Cari folder global node_modules yang benar-benar berisi Puppeteer.
     *
     * Node mencari modul 'puppeteer' relatif ke lokasi file browser.cjs
     * (vendor/spatie/browsershot/bin). Di development, node_modules project
     * ada di sana, jadi modul ketemu tanpa NODE_PATH. Di production
     * node_modules project TIDAK ikut ter-deploy, jadi Puppeteer harus
     * datang dari install global.
     *
     * Lokasi global install berbeda antar sumber paket:
     *   - paket distro Ubuntu : /usr/lib/node_modules
     *   - NodeSource / manual : /usr/local/lib/node_modules
     * Jadi path-nya dicari, bukan ditebak.
     *
     * @return string|null Folder node_modules, null kalau tidak ketemu.
     */
    public function resolveNodeModulePath(): ?string
    {
        $candidates = [];

        // 1. Dikonfigurasi eksplisit (kalau diisi, dipakai sesuai urutan).
        if ($configured = config('browsershot.node_module_path')) {
            $candidates[] = $configured;
        }

        // 2. Laporan 'npm root -g' — sumber kebenaran saat install.
        $npmRoot = $this->npmRootGlobal();
        if ($npmRoot !== null) {
            $candidates[] = $npmRoot;
        }

        // 3. Lokasi umum, untuk jaga-jaga kalau 'npm root -g' tidak bisa
        //    dijalankan (mis. PATH PHP-FPM berbeda).
        $candidates[] = '/usr/local/lib/node_modules';
        $candidates[] = '/usr/lib/node_modules';

        // 4. node_modules project — jalur yang dipakai di development.
        $candidates[] = base_path('node_modules');

        foreach (array_unique($candidates) as $path) {
            if (is_dir($path.'/puppeteer')) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Output 'npm root -g', atau null kalau npm tidak bisa dijalankan.
     */
    private function npmRootGlobal(): ?string
    {
        $npm = config('browsershot.npm_binary') ?: 'npm';

        // Di Windows 'npm' adalah .cmd, jadi harus dipanggil lewat cmd.exe.
        // escapedhellcmd() menambahkan tanda kutip yang benar untuk .cmd.
        $command = PHP_OS_FAMILY === 'Windows'
            ? 'cmd /c '.escapeshellarg($npm).' root -g'
            : escapeshellarg($npm).' root -g';

        try {
            $output = shell_exec($command.' 2>&1');
        } catch (\Throwable) {
            return null;
        }

        $path = is_string($output) ? trim($output) : '';

        // Kalau perintah gagal, output-nya bisa berupa pesan error, bukan path.
        if ($path === '' || ! str_contains($path, 'node_modules')) {
            return null;
        }

        return $path;
    }

    /**
     * Terjemahkan error teknis jadi penyebab yang bisa langsung dit acted.
     *
     * Kegagalan paling sering di server: modul 'puppeteer' tidak ditemukan
     * karena node_modules project tidak ikut ter-deploy, atau Chrome binary
     * belum terpasang.
     */
    private function explainFailure(\Throwable $e): string
    {
        $message = $e->getMessage();

        if (str_contains($message, 'Cannot find module')) {
            $global = $this->npmRootGlobal();

            return "Modul 'puppeteer' tidak ditemukan. Node mencari modul relatif ke "
                .'vendor/spatie/browsershot/bin, dan node_modules project tidak ikut ter-deploy. '
                .'Pasang Puppeteer global: npm install -g puppeteer'
                .($global !== null ? " (lokasi global: {$global})" : '')
                .'. Detail: '.$message;
        }

        if (str_contains($message, 'executablePath') || str_contains($message, 'Browser was not found')) {
            return 'Binary Chrome tidak ditemukan. Jalankan: npx puppeteer browsers install chrome, '
                .'lalu set BROWSERSHOT_CHROME_PATH ke path Chrome (tanpa wildcard). '
                .'Detail: '.$message;
        }

        if (str_contains($message, 'crashpad_handler') || str_contains($message, 'Failed to launch the browser process')) {
            return 'Chrome gagal start. Penyebab yang paling sering: HOME tidak bisa ditulis '
                .'oleh user PHP-FPM, sehingga crashpad tidak bisa membuat database-nya '
                .'("chrome_crashpad_handler: --database is required"). '
                .'Pastikan folder HOME untuk Chrome bisa ditulis, atau jalankan render sebagai user '
                .'yang punya akses. Detail: '.$message;
        }

        if (str_contains($message, 'spawn') || str_contains($message, 'not found')) {
            return 'Node/npm tidak bisa dijalankan. Cek BROWSERSHOT_NODE_PATH dan BROWSERSHOT_NPM_PATH. '
                .'Detail: '.$message;
        }

        return $message;
    }

    /**
     * Render dengan DomPDF (fallback, layout rusak).
     */
    private function renderWithDompdf(string $html): string
    {
        $options = new Options;
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(preg_replace('/^\xEF\xBB\xBF/', '', $html));
        $dompdf->setPaper('a4');
        $dompdf->render();

        return $dompdf->output();
    }

    /**
     * Cek apakah Chrome rendering benar-benar bisa jalan di mesin ini.
     * Dipakai artisan command + health check deploy, supaya masalah
     * ketahuan sebelum invoice dikirim ke pelanggan.
     *
     * @return array{ok: bool, renderer: string, message: string}
     */
    public function selfTest(): array
    {
        if (! class_exists(Browsershot::class)) {
            return [
                'ok' => false,
                'renderer' => self::RENDERER_DOMPDF,
                'message' => 'spatie/browsershot tidak terpasang — composer install --no-dev belum dijalankan.',
            ];
        }

        try {
            $pdf = $this->renderWithChrome('<html><body><h1>self test</h1></body></html>');
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'renderer' => self::RENDERER_DOMPDF,
                'message' => 'Chrome gagal render: '.$this->explainFailure($e),
            ];
        }

        if (! str_starts_with($pdf, '%PDF')) {
            return [
                'ok' => false,
                'renderer' => self::RENDERER_DOMPDF,
                'message' => 'Chrome mengembalikan output yang bukan PDF.',
            ];
        }

        return [
            'ok' => true,
            'renderer' => self::RENDERER_CHROMIUM,
            'message' => 'Chrome headless siap — invoice PDF dirender dengan layout penuh.',
        ];
    }
}
