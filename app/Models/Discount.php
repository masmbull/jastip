<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discount extends Model
{
    protected $fillable = [
        'product_id',
        'category_id',
        'type',
        'value',
        'valid_from',
        'valid_to',
        'is_active',
        'description',
        'priority',
    ];
    
    protected $casts = [
        'value' => 'decimal:2',
        'is_active' => 'boolean',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
    ];
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    
    /**
     * Check if discount is valid
     */
    public function isValid(): bool
    {
        return $this->is_active
            && now() >= $this->valid_from
            && now() <= $this->valid_to;
    }
    
    /**
     * Calculate discount amount
     */
    public function calculateDiscount($price): float
    {
        if ($this->type === 'percentage') {
            return ($price * $this->value) / 100;
        }
        return $this->value;
    }
}
