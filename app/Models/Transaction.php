<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transactions';
    protected $fillable = [
        'date',
        'karyawan_id',
        'motor_id',
        'tip',
        'total',
        'payment_status',
        'payment_method',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'midtrans_payment_type',
        'qr_url',
        'expiry_time',
        'qr_string',
        'food_items',
    ];

    protected $casts = [
    'date' => 'datetime',
    'food_items' => 'array',
    
    ];

    /**
     * Get the motor that owns the transaction.
     */
    public function motor(): BelongsTo
    {
        return $this->belongsTo(Motor::class);
    }

    /**
     * Get the karyawan that owns the transaction.
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }
}