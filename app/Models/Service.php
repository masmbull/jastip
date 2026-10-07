<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'last_checked',
        'response_time',
        'error_message'
    ];

    protected $casts = [
        'last_checked' => 'datetime',
        'response_time' => 'float'
    ];

    public function isOnline(): bool
    {
        return $this->status === 'online';
    }

    public function isOffline(): bool
    {
        return $this->status === 'offline';
    }
}
