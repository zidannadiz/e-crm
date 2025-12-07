<?php

namespace App\Models\Ecrm;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $table = 'ecrm_orders';
    
    protected $fillable = [
        'client_id',
        'user_id',
        'nomor_order',
        'jenis_desain',
        'deskripsi',
        'kebutuhan',
        'status',
        'produk_status',
        'budget',
        'deadline',
        'catatan_admin',
        'desain_file',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'deadline' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'order_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'order_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->nomor_order)) {
                $order->nomor_order = static::generateOrderNumber();
            }
        });
    }

    public static function generateOrderNumber()
    {
        $year = date('Y');
        $month = date('m');
        $lastOrder = static::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastOrder ? (int) substr($lastOrder->nomor_order, -4) + 1 : 1;

        return 'ORD-' . $year . $month . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}

