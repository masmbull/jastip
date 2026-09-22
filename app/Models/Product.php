<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
{
        protected $fillable = [
        'category_id',
        'name',
        'brand',
        'slug',
        'description',
        'price',
        'setbiaya_fee',
        'price_max',
        'image',
        'stock',
        'rating',
        'sold_count',
        'unit',
        'sku',
        'is_active',
        'is_featured',
        'is_viral',
        'viral_rank',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'setbiaya_fee' => 'integer',
            'price_max' => 'integer',
            'stock' => 'integer',
            'rating' => 'decimal:1',
            'sold_count' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_viral' => 'boolean',
            'viral_rank' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->where('is_active', true);
    }

    public function scopeByCategory($query, $categorySlug)
    {
        return $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
    }

    /**
     * Produk yang sedang viral (diurutkan sesuai peringkat viral).
     */
    public function scopeViral($query)
    {
        return $query->where('is_viral', true)
            ->where('is_active', true)
            ->orderByRaw('CASE WHEN viral_rank IS NULL THEN 1 ELSE 0 END')
            ->orderBy('viral_rank');
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image && Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        // Path lokal di public/ (contoh: images/viral/nama-produk.jpg)
        if ($this->image && !filter_var($this->image, FILTER_VALIDATE_URL)) {
            if (Str::startsWith($this->image, ['images/', 'build/', 'img/'])) {
                return asset($this->image);
            }

            return asset('storage/' . $this->image);
        }

        // Placeholder image (palet cream/orange, konsisten dengan tema)
        return 'https://placehold.co/600x600/FFF7ED/EA580C?text=' . urlencode($this->name);
    }

        public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Biaya / fee layanan produk ini.
     * Ditampilkan di admin panel dan dipakai saat checkout.
     */
    public function getFormattedSetbiayaFeeAttribute(): string
    {
        return 'Rp ' . number_format($this->setbiaya_fee ?? 0, 0, ',', '.');
    }

    /**
     * Harga pasaran sebagai rentang, contoh: "Rp 65.000 – Rp 89.000".
     */
    public function getFormattedPriceRangeAttribute(): string
    {
        if ($this->price_max && $this->price_max > $this->price) {
            return 'Rp ' . number_format($this->price, 0, ',', '.')
                . ' – Rp ' . number_format($this->price_max, 0, ',', '.');
        }

        return $this->formatted_price;
    }

    public function getRatingTextAttribute(): ?string
    {
        return $this->rating ? number_format((float) $this->rating, 1, ',', '.') : null;
    }

    /**
     * Jumlah terjual versi ringkas, contoh: 12.400+ atau 1,2 rb+.
     */
    public function getSoldTextAttribute(): ?string
    {
        if ($this->sold_count <= 0) {
            return null;
        }

        if ($this->sold_count >= 1000) {
            return number_format($this->sold_count / 1000, 1, ',', '.') . ' rb+ terjual';
        }

        return $this->sold_count . '+ terjual';
    }

    public function getAvailabilityBadgeAttribute(): array
    {
        if ($this->stock <= 0) {
            return ['text' => 'Stok Habis', 'class' => 'bg-red-100 text-red-700'];
        }

        if ($this->stock <= 5) {
            return ['text' => 'Stok Terbatas (' . $this->stock . ')', 'class' => 'bg-amber-100 text-amber-700'];
        }

        return ['text' => 'Tersedia (' . $this->stock . ')', 'class' => 'bg-green-100 text-green-700'];
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }
}