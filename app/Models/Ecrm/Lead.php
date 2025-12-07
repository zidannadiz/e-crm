<?php

namespace App\Models\Ecrm;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    protected $table = 'ecrm_leads';
    
    protected $fillable = [
        'nama',
        'email',
        'telepon',
        'sumber',
        'kebutuhan',
        'status',
        'estimated_value',
        'assigned_to',
        'tanggal_kontak_terakhir',
        'catatan',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'tanggal_kontak_terakhir' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}

