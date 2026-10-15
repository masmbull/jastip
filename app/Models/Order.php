<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_whatsapp',
        'customer_email',
        'customer_address',
        'customer_notes',
        'shipping_method',
        'city',
        'resi',
        'courier',
        'shipped_at',
        'payment_method',
        'subtotal',
        'shipping_cost',
        'fee',
        'down_payment',
        'coupon_id',
        'discount',
        'stock_committed',
        'total',
        'status',
        'paid_at',
        'payment_hash',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'integer',
            'shipping_cost' => 'integer',
            'fee' => 'integer',
            'down_payment' => 'integer',
            'discount' => 'integer',
            'stock_committed' => 'boolean',
            'total' => 'integer',
            'paid_at' => 'datetime',
            'shipped_at' => 'datetime',
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

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function paymentProof()
    {
        return $this->hasOne(PaymentProof::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getNoAttribute(): string
    {
        return $this->order_number;
    }

    /**
     * Token publik dibuat otomatis (acak) saat order pertama kali disimpan.
     * Dipakai untuk link PDF invoice yang pendek dan tidak bisa ditebak.
     */
    protected static function booted(): void
    {
        static::creating(function (self $order) {
            if (empty($order->public_token)) {
                $order->public_token = Str::random(16);
            }
        });
    }

    /**
     * Link pendek PDF invoice: {app}/i/{token}. Dipakai di pesan WhatsApp.
     */
    public function getPublicPdfUrlAttribute(): ?string
    {
        return $this->public_token ? route('invoice.public', ['token' => $this->public_token]) : null;
    }

    /**
     * URL lacak resi (agregator cekresi) bila resi tersedia.
     */
    public function getTrackingUrlAttribute(): ?string
    {
        if (! $this->resi) {
            return null;
        }

        $tpl = config('ekspedisi.tracking_aggregator.url', 'https://cekresi.com/?noresi=%s');

        return sprintf($tpl, urlencode($this->resi));
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

    public function getRemainingAttribute(): int
    {
        return max(0, (int) $this->total - $this->totalPaid);
    }

    public function getTotalPaidAttribute(): int
    {
        return (int) $this->down_payment + (int) $this->payments->sum('amount');
    }

    public function getPaymentStatusAttribute(): string
    {
        if ($this->is_paid) {
            return 'lunas';
        }
        if ($this->totalPaid > 0) {
            return 'dp';
        }

        return 'belum';
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return [
            'belum' => 'Belum Bayar',
            'dp' => 'Deposit',
            'lunas' => 'Lunas',
        ][$this->payment_status];
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp '.number_format($this->total, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp '.number_format($this->subtotal, 0, ',', '.');
    }

    public function getFormattedFeeAttribute(): string
    {
        return 'Rp '.number_format($this->fee ?? 0, 0, ',', '.');
    }

    public function getFormattedDiscountAttribute(): string
    {
        return 'Rp '.number_format($this->discount ?? 0, 0, ',', '.');
    }

    public static function generateOrderNumber(): string
    {
        // Generate order number with date + random hex suffix for uniqueness
        $date = now()->format('Ymd');
        $random = strtoupper(bin2hex(random_bytes(4)));

        return 'ND'.$date.'-'.$random;
    }

    /**
     * Generate payment hash to detect tampering
     */
    public function generatePaymentHash(): string
    {
        $data = [
            'order_number' => $this->order_number,
            'subtotal' => $this->subtotal,
            'shipping_cost' => $this->shipping_cost,
            'fee' => $this->fee,
            'total' => $this->total,
            'customer_name' => $this->customer_name,
            'customer_whatsapp' => $this->customer_whatsapp,
        ];

        return hash('sha256', json_encode($data));
    }

    /**
     * Validate payment integrity
     */
    public function isPaymentValid(): bool
    {
        if (! $this->payment_hash) {
            return false;
        }

        return $this->payment_hash === $this->generatePaymentHash();
    }

    /**
     * Mark as paid with hash validation
     */
    public function markAsPaid(): void
    {
        $this->paid_at = now();
        $this->payment_hash = $this->generatePaymentHash();
        $this->save();
    }
}
