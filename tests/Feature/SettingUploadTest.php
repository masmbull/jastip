<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_uploading_brand_logo_persists_the_new_path(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'admin')->firstOrFail();

        $old = Setting::get('brand_logo');

        $res = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'brand_logo' => UploadedFile::fake()->image('logo.png', 200, 60),
        ]);

        $res->assertSessionHasNoErrors();
        $res->assertRedirect(route('admin.settings.index'));

        $new = Setting::get('brand_logo');
        $this->assertNotSame($old, $new, 'brand_logo harus berubah setelah upload');
        $this->assertNotNull($new);
        Storage::disk('public')->assertExists($new);
    }

    public function test_uploading_qris_image_persists(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.settings.update'), [
            'qris_enabled' => '1',
            'qris_image' => UploadedFile::fake()->image('qris.png', 300, 300),
        ])->assertSessionHasNoErrors();

        $path = Setting::get('qris_image');
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_query_string_cache_buster_not_needed_but_value_readable(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.settings.update'), [
            'brand_favicon' => UploadedFile::fake()->image('fav.png', 32, 32),
        ])->assertSessionHasNoErrors();

        $this->assertNotNull(Setting::get('brand_favicon'));
    }
}
