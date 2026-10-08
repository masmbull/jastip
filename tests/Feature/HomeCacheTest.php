<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Homepage caches Eloquent collections. With the real "database" cache store
 * (default in prod) a misconfigured serializable_classes allow-list makes the
 * second, cache-hit render blow up with __PHP_Incomplete_Class -> 500.
 * Test suite normally runs on the "array" store, so this pins to "database".
 */
class HomeCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_on_cache_hit_with_database_store(): void
    {
        Config::set('cache.default', 'database');
        Http::fake();
        $this->seed();

        // First hit populates the cache, second hit reads it back.
        $this->get(route('home'))->assertOk();
        $this->get(route('home'))->assertOk();
    }
}
