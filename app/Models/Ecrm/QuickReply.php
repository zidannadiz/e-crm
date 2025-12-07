<?php

namespace App\Models\Ecrm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuickReply extends Model
{
    protected $table = 'ecrm_quick_replies';
    
    protected $fillable = [
        'pertanyaan',
        'jawaban',
        'kategori',
        'use_ai',
        'order',
        'aktif',
    ];

    protected $casts = [
        'use_ai' => 'boolean',
        'aktif' => 'boolean',
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'quick_reply_id');
    }
}

