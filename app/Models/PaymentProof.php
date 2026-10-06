<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentProof extends Model
{
    protected $fillable = [
        'order_id',
        'file_path',
        'file_name',
        'file_size',
        'status',
        'admin_notes',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function verify(string $notes = null): void
    {
        $this->status = 'verified';
        $this->verified_at = now();
        $this->admin_notes = $notes;
        $this->save();

        // Update order status to confirmed
        $this->order->update(['status' => Order::STATUS_CONFIRMED]);
    }

    public function reject(string $notes): void
    {
        $this->status = 'rejected';
        $this->admin_notes = $notes;
        $this->save();

        // Update order status back to awaiting payment
        $this->order->update(['status' => Order::STATUS_AWAITING_PAYMENT]);
    }
}
