<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_whatsapp',
        'customer_address',
        'customer_notes',
        'shipping_method',
        'payment_method',
        'subtotal',
        'shipping_cost',
        'total',
        'status',
        'paid_at',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'integer',
            'shipping_cost' => 'integer',
            'total' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public const STATUS_AWAITING_PAYMENT = 'awaiting_payment';
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_READY = 'ready';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public static function statuses(): array
    {
        return [
            self::STATUS_AWAITING_PAYMENT => 'Menunggu Pembayaran',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CONFIRMED => 'Dikonfirmasi',
            self::STATUS_PROCESSING => 'Diproses',
            self::STATUS_READY => 'Siap Ambil',
            self::STATUS_SHIPPED => 'Dikirim',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getNoAttribute(): string
    {
        return $this->order_number;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_AWAITING_PAYMENT => 'bg-amber-50 text-amber-700',
            self::STATUS_PENDING => 'bg-amber-100 text-amber-800',
            self::STATUS_CONFIRMED => 'bg-blue-100 text-blue-800',
            self::STATUS_PROCESSING => 'bg-purple-100 text-purple-800',
            self::STATUS_READY => 'bg-slate-100 text-slate-600',
            self::STATUS_SHIPPED => 'bg-sky-100 text-sky-800',
            self::STATUS_COMPLETED => 'bg-emerald-100 text-emerald-800',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getIsPaidAttribute(): bool
    {
        return ! is_null($this->paid_at);
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public static function generateOrderNumber(): string
    {
        return 'ND' . now()->format('Ymd') . '-' . str_pad((string) Order::count() + 1, 4, '0', STR_PAD_LEFT);
    }
}