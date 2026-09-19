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
        'slug',
        'description',
        'price',
        'image',
        'stock',
        'unit',
        'sku',
        'is_active',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'stock' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
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

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image && Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        if ($this->image && !filter_var($this->image, FILTER_VALIDATE_URL)) {
            return asset('storage/' . $this->image);
        }

        // Placeholder image
        return 'https://placehold.co/600x600/FFE8F0/999999?text=' . urlencode($this->name);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
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