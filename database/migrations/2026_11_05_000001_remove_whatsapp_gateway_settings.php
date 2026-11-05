<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $keys = ['wa_gateway_url', 'wa_gateway_key', 'wa_gateway_session'];
        DB::table('settings')->whereIn('key', $keys)->delete();

        foreach ($keys as $key) {
            Cache::forget('setting_'.$key);
        }
    }

    public function down(): void
    {
        // Removed credential values are not restored during rollback.
    }
};
