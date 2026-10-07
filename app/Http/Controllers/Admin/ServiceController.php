<?php

namespace App\Http\Controllers\Admin;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ServiceController
{
    public function index()
    {
        $services = Service::orderBy('name')->get();
        
        // Tambah info tambahan
        $systemInfo = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'disk_usage' => $this->getDiskUsage(),
            'memory_usage' => $this->getMemoryUsage(),
        ];

        return view('admin.services.index', compact('services', 'systemInfo'));
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

        return response()->json([
            'status' => $status,
            'response_time' => round($responseTime, 2),
            'error_message' => $errorMessage,
        ]);
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

        return redirect()->route('admin.services.index')
            ->with('success', 'Service status checked');
    }

    private function checkDatabaseStatus()
    {
        DB::connection()->getPdo();
    }

    private function checkCacheStatus()
    {
        Cache::put('health_check_' . time(), 'ok', 1);
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
        if (!is_writable($path)) {
            throw new \Exception('Storage path not writable');
        }
    }

    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'online', 'message' => 'Connected'];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'message' => $e->getMessage()];
        }
    }

    private function checkCache()
    {
        try {
            Cache::put('health_check', 'ok', 1);
            Cache::forget('health_check');
            return ['status' => 'online', 'message' => 'Working'];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'message' => $e->getMessage()];
        }
    }

    private function checkStorage()
    {
        try {
            $path = storage_path('app');
            if (!is_writable($path)) {
                return ['status' => 'offline', 'message' => 'Not writable'];
            }
            return ['status' => 'online', 'message' => 'Writable'];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'message' => $e->getMessage()];
        }
    }

    private function getDiskUsage()
    {
        $total = disk_total_space('/');
        $free = disk_free_space('/');
        $used = $total - $free;

        return [
            'total' => $this->formatBytes($total),
            'used' => $this->formatBytes($used),
            'free' => $this->formatBytes($free),
            'percent' => round(($used / $total) * 100, 2),
        ];
    }

    private function getMemoryUsage()
    {
        $current = memory_get_usage(true);
        $peak = memory_get_peak_usage(true);

        return [
            'current' => $this->formatBytes($current),
            'peak' => $this->formatBytes($peak),
            'limit' => ini_get('memory_limit'),
        ];
    }

    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
