<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class SitemapController extends Controller
{
    /**
     * sitemap.xml dinamis: halaman statis + produk + kategori.
     * Produk/kategori aktif saja yang didaftarkan.
     */
    public function index()
    {
        $urls = [
            ['loc' => route('home'), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => route('products.index'), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => route('products.viral'), 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => route('categories.index'), 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => route('about'), 'changefreq' => 'monthly', 'priority' => '0.4'],
            ['loc' => route('how-to'), 'changefreq' => 'monthly', 'priority' => '0.4'],
            ['loc' => route('shipping.index'), 'changefreq' => 'monthly', 'priority' => '0.4'],
            ['loc' => route('contact'), 'changefreq' => 'monthly', 'priority' => '0.4'],
            ['loc' => route('terms'), 'changefreq' => 'yearly', 'priority' => '0.2'],
            ['loc' => route('privacy'), 'changefreq' => 'yearly', 'priority' => '0.2'],
        ];

        Product::query()->select('slug', 'updated_at')->get()->each(function ($p) use (&$urls) {
            $urls[] = [
                'loc' => route('products.show', $p->slug),
                'lastmod' => optional($p->updated_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        });

        Category::query()->select('slug', 'updated_at')->get()->each(function ($c) use (&$urls) {
            $urls[] = [
                'loc' => route('categories.show', $c->slug),
                'lastmod' => optional($c->updated_at)->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ];
        });

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
