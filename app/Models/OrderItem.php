<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_price',
        'unit',
        'quantity',
        'status',
        'subtotal',
        'deposit',
    ];

    public const STATUS_READY = 'ready';

    public const STATUS_PO = 'po';

    public static function statuses(): array
    {
        return [
            self::STATUS_READY => 'Ready',
            self::STATUS_PO => 'PO',
        ];
    }

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'product_price' => 'integer',
            'quantity' => 'integer',
            'subtotal' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp '.number_format($this->product_price, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp '.number_format($this->subtotal, 0, ',', '.');
    }
}
