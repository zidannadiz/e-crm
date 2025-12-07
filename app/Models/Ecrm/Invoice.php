<?php

namespace App\Models\Ecrm;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $table = 'ecrm_invoices';
    
    protected $fillable = [
        'order_id',
        'client_id',
        'nomor_invoice',
        'tanggal_invoice',
        'tanggal_jatuh_tempo',
        'subtotal',
        'pajak',
        'diskon',
        'total',
        'status',
        'catatan',
        'deskripsi',
    ];

    protected $casts = [
        'tanggal_invoice' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'subtotal' => 'decimal:2',
        'pajak' => 'decimal:2',
        'diskon' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments()
            ->where('status', 'verified')
            ->sum('jumlah');
    }

    public function getRemainingAmountAttribute()
    {
        return $this->total - $this->total_paid;
    }

    public function getIsPaidAttribute()
    {
        return $this->remaining_amount <= 0;
    }

    public function getIsOverdueAttribute()
    {
        return $this->status !== 'paid' && 
               $this->tanggal_jatuh_tempo < now() && 
               $this->status !== 'cancelled';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->nomor_invoice)) {
                $invoice->nomor_invoice = static::generateInvoiceNumber();
            }
        });
    }

    public static function generateInvoiceNumber()
    {
        $year = date('Y');
        $month = date('m');
        $lastInvoice = static::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastInvoice ? (int) substr($lastInvoice->nomor_invoice, -4) + 1 : 1;

        return 'INV-' . $year . $month . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}

