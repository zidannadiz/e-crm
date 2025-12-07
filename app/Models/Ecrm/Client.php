<?php

namespace App\Models\Ecrm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $table = 'ecrm_clients';
    
    protected $fillable = [
        'nama',
        'email',
        'telepon',
        'perusahaan',
        'alamat',
        'tipe',
        'status',
        'catatan',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'client_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'client_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    public function getTotalProjectsAttribute()
    {
        return $this->projects()->count();
    }

    public function getTotalRevenueAttribute()
    {
        return $this->projects()
            ->where('status', 'completed')
            ->sum('budget');
    }
}

