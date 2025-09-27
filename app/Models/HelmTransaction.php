<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HelmTransaction extends Model
{
    use HasFactory;

    protected $table = 'helm_transactions';
    protected $fillable = [
        'nama_customer',
        'tanggal_cuci',
        'tanggal_selesai',
        'payment_status',
        'payment_method',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'midtrans_payment_type',
        'qr_url',
        'qr_string',
        'expiry_time',
    ];
    public function helmitems(): HasMany
    {
        return $this->hasMany(HelmItem::class);
    }

    protected $casts = [
        'tanggal_cuci' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];
}
