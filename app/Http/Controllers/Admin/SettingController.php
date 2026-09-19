<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingUpdateRequest;
use App\Models\Setting;
use Illuminate\Http\Request;

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
                if (($definition['type'] ?? 'text') === 'image') {
                    $this->storeImage($request, $key, $group);

                    continue;
                }

                if ($request->has($key)) {
                    Setting::set($key, $request->input($key, ''), 'string', $group, true);
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

        Setting::set($key, $request->file($key)->store('settings', 'public'), 'string', $group, true);
    }
}