<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_home_has_core_seo_tags(): void
    {
        $res = $this->get(route('home'));
        $res->assertOk();

        $res->assertSee('<title>', false);
        $res->assertSee('name="description"', false);
        $res->assertSee('rel="canonical"', false);
        $res->assertSee('property="og:title"', false);
        $res->assertSee('property="og:image"', false);
        $res->assertSee('twitter:card', false);
        $res->assertSee('application/ld+json', false);
    }

    public function test_sitemap_is_xml_and_lists_public_pages(): void
    {
        $res = $this->get(route('sitemap'));
        $res->assertOk();
        $res->assertHeader('Content-Type', 'application/xml');

        $body = $res->getContent();
        $this->assertStringContainsString('<urlset', $body);
        $this->assertStringContainsString(route('products.index'), $body);
        $this->assertStringContainsString(route('categories.index'), $body);
    }

    public function test_robots_points_to_sitemap(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('Sitemap:', $robots);
        $this->assertStringContainsString('Disallow: /admin', $robots);
    }

    public function test_product_page_exposes_product_json_ld(): void
    {
        $product = Product::firstOrFail();

        $res = $this->get(route('products.show', $product->slug));
        $res->assertOk();
        $res->assertSee('"@type":"Product"', false);
        $res->assertSee('"priceCurrency":"IDR"', false);
        $res->assertSee('BreadcrumbList', false);
    }
}
