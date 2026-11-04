<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingUpdateRequest;
use App\Models\Setting;
use App\Services\WhatsappGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SettingController extends Controller
{
    public function index()
    {
        $mediaStatus = [];
        foreach (['brand_logo', 'qris_image'] as $key) {
            $path = ltrim((string) Setting::get($key, ''), '/');
            if ($path === '') {
                continue;
            }

            $mediaStatus[$key] = [
                'stored' => Storage::disk('public')->exists($path),
                'public' => is_file(public_path('storage/'.$path)),
            ];
        }

        // Status gateway dipakai untuk menampilkan badge "Terhubung / Tidak siap".
        // Dibungkus try/catch: gateway yang mati tidak boleh membuat halaman
        // Pengaturan ikut error.
        try {
            $waGatewayStatus = WhatsappGatewayService::fromSettings()->status();
        } catch (\Throwable $e) {
            $waGatewayStatus = [
                'configured' => false,
                'connected' => false,
                'status' => 'error',
                'phone' => null,
                'error' => 'Gagal memeriksa gateway: '.$e->getMessage(),
            ];
        }

        return view('admin.settings.index', compact('waGatewayStatus', 'mediaStatus'));
    }

    public function update(SettingUpdateRequest $request)
    {
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

                    // Kredensial gateway tidak boleh publik: is_public = false.
                    $isPublic = $this->isSensitive($key) ? false : true;

                    Setting::set($key, $request->input($key), $storageType, $group, $isPublic);
                }
            }
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan berhasil diperbarui!');
    }

    /**
     * Kunci yang menyimpan rahasia: jangan dijadikan setting publik.
     */
    private function isSensitive(string $key): bool
    {
        return in_array($key, ['wa_gateway_key'], true);
    }

    private function storeImage(Request $request, string $key, string $group): void
    {
        if (! $request->hasFile($key)) {
            return;
        }

        try {
            $path = $request->file($key)->store('settings', 'public');
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
}
