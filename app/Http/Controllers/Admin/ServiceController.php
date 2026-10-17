<?php

namespace App\Http\Controllers\Admin;

use App\Models\Service;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ServiceController
{
    /**
     * Halaman Daftar Layanan sudah digabung ke Status Sistem.
     * Rutenya tetap dipertahankan supaya tautan atau bookmark lama tidak 404.
     */
    public function index()
    {
        return redirect()->route('admin.system.status');
    }

    public function check(Service $service)
    {
        $startTime = microtime(true);
        $status = 'offline';
        $errorMessage = null;

        try {
            match ($service->name) {
                'Database' => $this->checkDatabaseStatus(),
                'Cache' => $this->checkCacheStatus(),
                'API' => $this->checkApiStatus(),
                'Email' => $this->checkEmailStatus(),
                'Storage' => $this->checkStorageStatus(),
                default => throw new \Exception('Unknown service'),
            };
            $status = 'online';
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
        }

        $responseTime = (microtime(true) - $startTime) * 1000; // ms

        $service->update([
            'status' => $status,
            'last_checked' => now(),
            'response_time' => $responseTime,
            'error_message' => $errorMessage,
        ]);

        // Balasan JSON mentah bikin browser menampilkan kurung kurawal mentah
        // setelah tombol Check ditekan, karena tombolnya form biasa, bukan AJAX.
        return redirect()
            ->back()
            ->with('success', sprintf(
                'Cek %s selesai: %s (%s ms).',
                $service->name,
                $status,
                round($responseTime, 2)
            ));
    }

    public function checkAll()
    {
        $services = Service::all();

        foreach ($services as $service) {
            try {
                match ($service->name) {
                    'Database' => $this->checkDatabaseStatus(),
                    'Cache' => $this->checkCacheStatus(),
                    'API' => $this->checkApiStatus(),
                    'Email' => $this->checkEmailStatus(),
                    'Storage' => $this->checkStorageStatus(),
                };

                $service->update([
                    'status' => 'online',
                    'last_checked' => now(),
                    'error_message' => null,
                ]);
            } catch (\Exception $e) {
                $service->update([
                    'status' => 'offline',
                    'last_checked' => now(),
                    'error_message' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->route('admin.system.status')
            ->with('success', 'Status semua layanan sudah diperbarui.');
    }

    private function checkDatabaseStatus()
    {
        DB::connection()->getPdo();
    }

    private function checkCacheStatus()
    {
        Cache::put('health_check_'.time(), 'ok', 1);
    }

    private function checkApiStatus()
    {
        // Check jika API routes accessible
        $routes = collect(\Route::getRoutes())->filter(function ($route) {
            return in_array('GET', $route->methods) && strpos($route->uri, 'api/') === 0;
        });

        if ($routes->isEmpty()) {
            throw new \Exception('No API routes found');
        }
    }

    private function checkEmailStatus()
    {
        // Check email configuration
        $driver = config('mail.default');
        if (empty($driver)) {
            throw new \Exception('Email driver not configured');
        }
    }

    private function checkStorageStatus()
    {
        $path = storage_path('app');
        if (! is_writable($path)) {
            throw new \Exception('Storage path not writable');
        }
    }
}
