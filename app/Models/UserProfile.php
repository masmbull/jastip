<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'date_of_birth',
        'gender',
        'profile_picture',
        'bio',
        'loyalty_points',
        'membership_tier',
        'last_login',
    ];
    
    protected $casts = [
        'date_of_birth' => 'date',
        'last_login' => 'datetime',
        'loyalty_points' => 'integer',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Add loyalty points
     */
    public function addPoints($points)
    {
        $this->increment('loyalty_points', $points);
        $this->updateMembershipTier();
    }
    
    /**
     * Update membership tier based on loyalty points
     */
    public function updateMembershipTier()
    {
        $points = $this->loyalty_points;
        
        if ($points >= 10000) {
            $tier = 'platinum';
        } elseif ($points >= 5000) {
            $tier = 'gold';
        } elseif ($points >= 2000) {
            $tier = 'silver';
        } else {
            $tier = 'bronze';
        }
        
        $this->update(['membership_tier' => $tier]);
    }
}
