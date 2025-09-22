<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class HelmItem extends Model
{
    use HasFactory;

    protected $table = 'helm_items';
    protected $fillable = [
        'helm_transaction_id',
        'karyawan_id',
        'nama_helm',
        'type_helm',
        'harga',
    ];

    // Relasi ke transaksi
    public function helmtransaction():BelongsTo
    {
        return $this->belongsTo(HelmTransaction::class, 'helm_transaction_id');
    }

    // Relasi ke karyawan
    public function karyawan():BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function transaction()
    {
        return $this->belongsTo(HelmTransaction::class, 'helm_transaction_id');
    }

    protected $casts = [
    'date' => 'datetime',
];
}
