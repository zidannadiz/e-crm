<?php

namespace App\Models\Ecrm;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $table = 'ecrm_chat_messages';
    
    protected $fillable = [
        'order_id',
        'user_id',
        'pesan',
        'quick_reply_id',
        'is_ai_generated',
        'is_read',
    ];

    protected $casts = [
        'is_ai_generated' => 'boolean',
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function quickReply(): BelongsTo
    {
        return $this->belongsTo(QuickReply::class, 'quick_reply_id');
    }
}

