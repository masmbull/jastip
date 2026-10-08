<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    public const SENDER_CUSTOMER = 'customer';
    public const SENDER_ADMIN = 'admin';

    protected $fillable = ['user_id', 'sender', 'body', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isFromAdmin(): bool
    {
        return $this->sender === self::SENDER_ADMIN;
    }
}
