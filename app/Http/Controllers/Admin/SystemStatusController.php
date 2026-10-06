<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ShippingService;
use Illuminate\Support\Facades\Http;

class SystemStatusController extends Controller
{
    public function __construct(private readonly ShippingService $shippingService)
    {
    }

    /**
     * Show system status and API services monitoring
     */
    public function index()
    {
        $services = [
            [
                'name' => 'Database',
                'status' => $this->checkDatabase(),
                'icon' => '🗄️',
                'type' => 'internal',
            ],
            [
                'name' => 'File Storage',
                'status' => $this->checkFileStorage(),
                'icon' => '💾',
                'type' => 'internal',
            ],
            [
                'name' => 'Cache',
                'status' => $this->checkCache(),
                'icon' => '⚡',
                'type' => 'internal',
            ],
            [
                'name' => 'Session',
                'status' => $this->checkSession(),
                'icon' => '🔐',
                'type' => 'internal',
            ],
        ];

        // Add shipping API status
        $shippingStatus = $this->shippingService->getApiStatus();
        if ($shippingStatus['status'] !== 'not_configured') {
            $services[] = [
                'name' => 'Shipping API (' . ucfirst($shippingStatus['provider']) . ')',
                'status' => $shippingStatus['status'],
                'message' => $shippingStatus['message'],
                'icon' => '🚚',
                'type' => 'external',
                'last_update' => $shippingStatus['last_update'],
            ];
        }

        // Add common external APIs
        $services = array_merge($services, [
            [
                'name' => 'WhatsApp API',
                'status' => $this->checkWhatsappApi(),
                'icon' => '📱',
                'type' => 'external',
            ],
        ]);

        // System info
        $systemInfo = [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'os' => php_uname('s'),
            'memory_usage' => $this->formatBytes(memory_get_usage(true)),
            'disk_free' => $this->formatBytes(disk_free_space('/')),
            'disk_total' => $this->formatBytes(disk_total_space('/')),
        ];

        return view('admin.system.status', compact('services', 'systemInfo'));
    }

    /**
     * Check database connection
     */
    private function checkDatabase(): string
    {
        try {
            \DB::connection()->getPdo();
            return 'online';
        } catch (\Exception $e) {
            return 'offline';
        }
    }

    /**
     * Check file storage
     */
    private function checkFileStorage(): string
    {
        try {
            $testFile = storage_path('app/health-check-' . time() . '.tmp');
            file_put_contents($testFile, 'test');
            @unlink($testFile);
            return 'online';
        } catch (\Exception $e) {
            return 'offline';
        }
    }

    /**
     * Check cache
     */
    private function checkCache(): string
    {
        try {
            $testKey = 'health-check-' . time();
            \Cache::put($testKey, 'test', 1);
            $value = \Cache::get($testKey);
            \Cache::forget($testKey);
            return $value === 'test' ? 'online' : 'offline';
        } catch (\Exception $e) {
            return 'offline';
        }
    }

    /**
     * Check session
     */
    private function checkSession(): string
    {
        try {
            $testKey = 'health-check-' . time();
            \Session::put($testKey, 'test');
            $value = \Session::get($testKey);
            \Session::forget($testKey);
            return $value === 'test' ? 'online' : 'offline';
        } catch (\Exception $e) {
            return 'offline';
        }
    }

    /**
     * Check WhatsApp API connectivity
     */
    private function checkWhatsappApi(): string
    {
        try {
            $response = Http::timeout(5)->head('https://api.whatsapp.com');
            return $response->successful() ? 'online' : 'offline';
        } catch (\Exception $e) {
            return 'offline';
        }
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
