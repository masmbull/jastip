<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingUpdateRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SettingController extends Controller
{
    public function index()
    {
        $mediaStatus = [];
        foreach (['brand_logo', 'brand_favicon', 'qris_image'] as $key) {
            $path = ltrim((string) Setting::get($key, ''), '/');
            if ($path === '') {
                continue;
            }

            $mediaStatus[$key] = [
                'stored' => Storage::disk('public')->exists($path),
                'public' => is_file(public_path('storage/'.$path)),
            ];
        }

        return view('admin.settings.index', compact('mediaStatus'));
    }

    public function update(SettingUpdateRequest $request)
    {
        $this->rejectOversizedRequest($request);

        foreach (config('settings.groups', []) as $group => $meta) {
            foreach ($meta['settings'] ?? [] as $key => $definition) {
                $type = $definition['type'] ?? 'text';

                if ($type === 'image') {
                    $this->storeImage($request, $key, $group);

                    continue;
                }

                // API key sengaja tidak pernah ditampilkan ulang di form.
                // Kosong = "jangan diubah", jadi kredensial lama tidak terhapus
                // hanya karena admin menyimpan halaman Pengaturan.
                if ($type === 'password' && ! $request->filled($key)) {
                    continue;
                }

                if ($request->has($key)) {
                    // Tipe disimpan sama seperti SettingSeeder supaya cara baca
                    // nilainya tidak berubah setelah admin mengedit satu field.
                    $storageType = match ($type) {
                        'boolean' => 'boolean',
                        'number' => 'integer',
                        default => 'string',
                    };

                    Setting::set($key, $request->input($key), $storageType, $group, $type !== 'password');
                }
            }
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan berhasil diperbarui!');
    }

    private function storeImage(Request $request, string $key, string $group): void
    {
        if (! $request->hasFile($key)) {
            return;
        }

        try {
            $path = $request->file($key)->store('settings', 'public');
            if (! is_string($path) || $path === '') {
                throw new \RuntimeException('Storage did not return a file path.');
            }

            Setting::set($key, $path, 'string', $group, true);
        } catch (\Exception $e) {
            \Log::error('Failed to upload setting image: '.$key, [
                'error' => $e->getMessage(),
                'key' => $key,
            ]);

            throw ValidationException::withMessages([
                $key => ['Gagal mengunggah file gambar. Silakan coba lagi.'],
            ]);
        }
    }

    private function rejectOversizedRequest(Request $request): void
    {
        $contentLength = (int) $request->server('CONTENT_LENGTH', 0);
        $postLimit = $this->iniSizeInBytes((string) ini_get('post_max_size'));

        if ($contentLength > 0 && $postLimit > 0 && $contentLength > $postLimit) {
            throw ValidationException::withMessages([
                'settings' => ['Ukuran total formulir melebihi batas server. Gunakan gambar maksimal 10 MB.'],
            ]);
        }
    }

    private function iniSizeInBytes(string $value): int
    {
        if (! preg_match('/^\s*(\d+(?:\.\d+)?)\s*([KMG]?)\s*$/i', $value, $matches)) {
            return 0;
        }

        $power = match (strtoupper($matches[2])) {
            'K' => 1,
            'M' => 2,
            'G' => 3,
            default => 0,
        };

        return (int) ((float) $matches[1] * (1024 ** $power));
    }
}
