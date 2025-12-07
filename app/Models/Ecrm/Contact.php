<?php

namespace App\Models\Ecrm;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    protected $table = 'ecrm_contacts';
    
    protected $fillable = [
        'client_id',
        'project_id',
        'user_id',
        'tipe',
        'subjek',
        'pesan',
        'tanggal_kontak',
        'arah',
    ];

    protected $casts = [
        'tanggal_kontak' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

