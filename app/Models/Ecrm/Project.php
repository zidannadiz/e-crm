<?php

namespace App\Models\Ecrm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $table = 'ecrm_projects';
    
    protected $fillable = [
        'client_id',
        'nama_proyek',
        'deskripsi',
        'jenis_desain',
        'status',
        'budget',
        'deadline',
        'tanggal_mulai',
        'tanggal_selesai',
        'revision_count',
        'catatan',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'deadline' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'revision_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'project_id');
    }

    public function getProgressAttribute()
    {
        $statusProgress = [
            'quotation' => 0,
            'approved' => 10,
            'in_progress' => 50,
            'review' => 75,
            'revision' => 60,
            'completed' => 100,
            'cancelled' => 0,
        ];

        return $statusProgress[$this->status] ?? 0;
    }
}

