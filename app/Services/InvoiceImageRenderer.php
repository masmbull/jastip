<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Render invoice (HTML template yang sama dengan PDF/pratinjau) jadi PNG.
 *
 * Dipakai untuk mengirim invoice sebagai GAMBAR ke WhatsApp tanpa browser
 * admin: prosesnya harus jalan di server. Caranya memanggil Chrome/Edge
 * headless (`--screenshot`), bukan library PHP, karena template invoice
 * memakai layout tabel + CSS inline yang tidak bisa dirasterisasi GD/Imagick.
 *
 * Kalau binary browser tidak tersedia di server, render() mengembalikan null
 * dan pemanggil WAJIB menampilkan pesan jelas — jangan mengirim gambar kosong.
 */
class InvoiceImageRenderer
{
    /**
     * Lokasi binary yang dicoba, berurutan. Bisa ditimpa lewat
     * config('invoice.browser_path') / INVOICE_BROWSER_PATH.
     *
     * @var array<int, string>
     */
    private const CANDIDATES = [
        '/usr/bin/google-chrome',
        '/usr/bin/chromium',
        '/usr/bin/chromium-browser',
        '/snap/bin/chromium',
        '/usr/bin/microsoft-edge',
        'C:\Program Files\Google\Chrome\Application\chrome.exe',
        'C:\Program Files (x86)\Google\Chrome\Application\chrome.exe',
        'C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe',
        'C:\Program Files\Microsoft\Edge\Application\msedge.exe',
    ];

    public function __construct(
        private ?string $browserPath = null,
        private int $width = 820,
        private int $timeout = 45,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            config('invoice.browser_path') ?: null,
            (int) config('invoice.image_width', 820),
            (int) config('invoice.image_timeout', 45),
        );
    }

    /**
     * Binary browser yang akan dipakai, atau null kalau tidak ketemu.
     */
    public function browser(): ?string
    {
        $configured = $this->browserPath ?: (string) config('invoice.browser_path', '');
        if ($configured !== '' && is_file($configured)) {
            return $configured;
        }

        foreach (self::CANDIDATES as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    public function isAvailable(): bool
    {
        return $this->browser() !== null;
    }

    /**
     * Render HTML jadi isi file PNG.
     *
     * @return string|null isi PNG, atau null kalau gagal / browser tak ada.
     */
    public function render(string $html): ?string
    {
        $browser = $this->browser();
        if ($browser === null) {
            Log::warning('InvoiceImageRenderer: tidak ada Chrome/Edge headless di server.');

            return null;
        }

        // File sementara di disk lokal (bukan storage publik): HTML sumber,
        // PNG hasil, dan direktori profil supaya tidak bentrok antar request.
        self::pruneStale();

        $tmp = storage_path('app/tmp/invoice-'.Str::random(16));
        if (! is_dir($tmp) && ! @mkdir($tmp, 0775, true) && ! is_dir($tmp)) {
            Log::warning('InvoiceImageRenderer: gagal membuat direktori sementara.', ['path' => $tmp]);

            return null;
        }

        $htmlPath = $tmp.DIRECTORY_SEPARATOR.'invoice.html';
        $pngPath = $tmp.DIRECTORY_SEPARATOR.'invoice.png';

        try {
            file_put_contents($htmlPath, $html);

            $fileUrl = 'file:///'.ltrim(str_replace('\\', '/', $htmlPath), '/');

            // Chrome --screenshot selalu memakai tinggi jendela penuh, jadi
            // ukur dulu tinggi konten lalu screenshot persis setinggi itu.
            // Tanpa langkah ini gambar menyisakan area putih kosong di bawah.
            $height = $this->measureHeight($browser, $fileUrl, $tmp);

            $command = [
                $browser,
                '--headless=new',
                '--disable-gpu',
                '--no-sandbox',
                '--hide-scrollbars',
                '--force-device-scale-factor=2',
                '--window-size='.$this->width.','.$height,
                '--virtual-time-budget=3000',
                '--user-data-dir='.$tmp.DIRECTORY_SEPARATOR.'profile',
                '--screenshot='.$pngPath,
                $fileUrl,
            ];

            $this->runProcess($command, $this->timeout);

            if (! is_file($pngPath) || filesize($pngPath) < 100) {
                Log::warning('InvoiceImageRenderer: PNG tidak dihasilkan.');

                return null;
            }

            return (string) file_get_contents($pngPath);
        } catch (\Throwable $e) {
            Log::warning('InvoiceImageRenderer: exception.', ['error' => $e->getMessage()]);

            return null;
        } finally {
            $this->cleanup($tmp);
        }
    }

    /**
     * Ukur tinggi konten halaman lewat Chrome headless (--dump-dom).
     *
     * Mengembalikan tinggi dalam piksel (CSS px), atau tinggi default 1200
     * kalau pengukuran gagal — lebih baik kelebihan sedikit daripada terpotong.
     */
    private function measureHeight(string $browser, string $fileUrl, string $tmp): int
    {
        $default = 1200;

        // Halaman pembungkus: iframe memuat invoice, lalu tinggi kontennya
        // ditulis ke <title> setelah event load. Dibaca dari DOM hasil
        // --dump-dom, jadi tidak butuh driver browser.
        $probe = $tmp.DIRECTORY_SEPARATOR.'measure.html';
        $safeUrl = htmlspecialchars($fileUrl, ENT_QUOTES);
        $probeHtml = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>0</title></head>'
            .'<body style="margin:0"><iframe id="f" src="'.$safeUrl.'" style="width:'.$this->width.'px;border:0"></iframe>'
            .'<script>window.addEventListener("load",function(){'
            .'var d=document.getElementById("f").contentDocument;'
            .'var h=Math.ceil(d.documentElement.scrollHeight);'
            .'if(h>0){document.title=String(h);}'
            .'});</script></body></html>';

        file_put_contents($probe, $probeHtml);

        $command = [
            $browser,
            '--headless=new',
            '--disable-gpu',
            '--no-sandbox',
            // Wajib: iframe file:// tidak bisa dibaca tanpa flag ini.
            '--allow-file-access-from-files',
            '--virtual-time-budget=3000',
            '--user-data-dir='.$tmp.DIRECTORY_SEPARATOR.'profile-measure',
            '--dump-dom',
            'file:///'.ltrim(str_replace('\\', '/', $probe), '/'),
        ];

        $output = $this->runProcess($command, 20);
        if ($output === null) {
            return $default;
        }

        if (preg_match('/<title>(\d+)<\/title>/i', $output, $m)) {
            $height = (int) $m[1];
            // Batasi: invoice sangat panjang tetap wajar, tapi jangan sampai
            // satu request menghasilkan gambar raksasa.
            if ($height >= 200) {
                return min($height, 4000);
            }
        }

        return $default;
    }

    /**
     * Jalankan proses dan kembalikan stdout, atau null kalau gagal/timeout.
     */
    private function runProcess(array $command, int $timeout): ?string
    {
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptors, $pipes);
        if (! is_resource($process)) {
            return null;
        }

        fclose($pipes[0]);
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);

        $out = '';
        $err = '';
        $deadline = microtime(true) + $timeout;

        while (proc_get_status($process)['running'] && microtime(true) < $deadline) {
            // Kuras KEDUA pipe setiap putaran. Kalau stderr penuh dan tidak
            // dibaca, proses browser menggantung menunggu buffer kosong.
            $out .= (string) stream_get_contents($pipes[1]);
            $err .= (string) stream_get_contents($pipes[2]);
            usleep(50_000);
        }

        $out .= (string) stream_get_contents($pipes[1]);
        $err .= (string) stream_get_contents($pipes[2]);

        $status = proc_get_status($process);
        if ($status['running']) {
            proc_terminate($process, 9);
        }

        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        return $out === '' ? null : $out;
    }

    /**
     * Hapus direktori sementara beserta isinya.
     *
     * Windows kadang masih mengunci file profil browser beberapa saat setelah
     * proses selesai. Kegagalan cleanup TIDAK boleh menggagalkan pengiriman,
     * jadi semua error di sini ditelan dan sisa file dibiarkan (akan
     * dibersihkan pada pemanggilan berikutnya / oleh tmpwatch di server).
     */
    private function cleanup(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        try {
            // Profil browser bisa berisi banyak file; hapus dari yang terdalam.
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $file) {
                try {
                    $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
                } catch (\Throwable) {
                    // File masih dikunci: lewati.
                }
            }

            try {
                rmdir($dir);
            } catch (\Throwable) {
                // Direktori masih berisi: biarkan.
            }
        } catch (\Throwable $e) {
            Log::debug('InvoiceImageRenderer: cleanup dilewati.', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Bersihkan sisa direktori sementara yang lebih tua dari 1 jam.
     * Dipanggil sebelum render supaya server tidak menumpuk file.
     */
    public static function pruneStale(): void
    {
        $base = storage_path('app/tmp');
        if (! is_dir($base)) {
            return;
        }

        $limit = time() - 3600;
        foreach (glob($base.DIRECTORY_SEPARATOR.'invoice-*', GLOB_ONLYDIR) ?: [] as $dir) {
            if (@filemtime($dir) < $limit) {
                (new self)->cleanup($dir);
            }
        }
    }

    /**
     * Pastikan direktori tmp ada (dipanggil sebelum render).
     */
    public static function ensureTmpDirectory(): void
    {
        $dir = storage_path('app/tmp');
        if (! is_dir($dir)) {
            Storage::disk('local')->makeDirectory('tmp');
        }
    }
}
