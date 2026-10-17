<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingUpdateRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
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

                if ($request->has($key)) {
                    // Tipe disimpan sama seperti SettingSeeder supaya cara baca
                    // nilainya tidak berubah setelah admin mengedit satu field.
                    $storageType = match ($type) {
                        'boolean' => 'boolean',
                        'number' => 'integer',
                        default => 'string',
                    };

                    Setting::set($key, $request->input($key), $storageType, $group, true);
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
