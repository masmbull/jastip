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

    public function test_uploading_setting_image_accepts_files_up_to_ten_megabytes(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.settings.update'), [
            'brand_logo' => UploadedFile::fake()->image('logo.png')->size(10240),
        ])->assertSessionHasNoErrors();

        Storage::disk('public')->assertExists(Setting::get('brand_logo'));
    }

    public function test_uploading_setting_image_rejects_files_over_ten_megabytes_with_a_message(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.settings.update'), [
            'brand_logo' => UploadedFile::fake()->image('logo.png')->size(10241),
        ])->assertSessionHasErrors([
            'brand_logo' => 'Ukuran logo maksimal 10 MB.',
        ]);
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

    public function test_settings_page_reports_uploaded_media_without_public_storage_link(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'admin')->firstOrFail();
        $path = 'settings/test-only-logo.png';

        Storage::disk('public')->put($path, 'image-data');
        Setting::set('brand_logo', $path, 'string', 'branding', true);

        $this->actingAs($admin)
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('File logo ada, tetapi belum bisa diakses publik.');
    }

    public function test_settings_page_navigation_links_match_the_sections(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('href="#settings-branding"', false)
            ->assertSee('href="#settings-payment"', false)
            ->assertSee('href="#settings-bank"', false)
            ->assertSee('id="settings-form"', false);
    }

    public function test_settings_update_persists_values_and_unchecked_qris_state(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.settings.update'), [
            'brand_name' => 'Toko Contoh',
            'qris_enabled' => '0',
            'shipping_cost' => '0',
        ])->assertRedirect(route('admin.settings.index'))
            ->assertSessionHas('success', 'Pengaturan berhasil diperbarui!');

        $this->assertSame('Toko Contoh', Setting::get('brand_name'));
        $this->assertFalse(Setting::get('qris_enabled'));
        $this->assertSame(0, Setting::get('shipping_cost'));
    }

    public function test_invalid_contact_and_gateway_values_do_not_change_settings(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        Setting::set('whatsapp', '6281234567890', 'string', 'contact', true);
        Setting::set('wa_gateway_url', 'https://gateway.example.test', 'string', 'whatsapp_gateway', true);

        $this->actingAs($admin)->post(route('admin.settings.update'), [
            'whatsapp' => '0812 3456',
            'wa_gateway_url' => 'not-a-url',
            'shipping_cost' => '-100',
        ])->assertSessionHasErrors(['whatsapp', 'wa_gateway_url', 'shipping_cost']);

        $this->assertSame('6281234567890', Setting::get('whatsapp'));
        $this->assertSame('https://gateway.example.test', Setting::get('wa_gateway_url'));
    }
}
