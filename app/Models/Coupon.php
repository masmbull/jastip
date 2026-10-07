<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'minimum_purchase',
        'maximum_discount',
        'usage_limit',
        'usage_count',
        'per_user_limit',
        'valid_from',
        'valid_to',
        'is_active',
    ];
    
    protected $casts = [
        'value' => 'decimal:2',
        'minimum_purchase' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'is_active' => 'boolean',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
    ];
    
    /**
     * Check if coupon is valid
     */
    public function isValid($total = 0, $userId = null): bool
    {
        if (!$this->is_active) return false;
        
        if (now() < $this->valid_from || now() > $this->valid_to) return false;
        
        if ($this->minimum_purchase && $total < $this->minimum_purchase) return false;
        
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) return false;
        
        return true;
    }
    
    /**
     * Calculate discount amount
     */
    public function calculateDiscount($total): float
    {
        if ($this->type === 'percentage') {
            $discount = ($total * $this->value) / 100;
        } else {
            $discount = $this->value;
        }
        
        if ($this->maximum_discount) {
            $discount = min($discount, $this->maximum_discount);
        }
        
        return min($discount, $total);
    }
}
