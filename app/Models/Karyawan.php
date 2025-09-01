<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawans';
    protected $fillable = [
        'nama_karyawan',
        'no_telepon',
        'aktif',
    ];

    public function transactions(): HasMany
{
    return $this->hasMany(Transaction::class, 'karyawan_id');
}

public function pengeluaran(): HasMany
{
    return $this->hasMany(Pengeluaran::class, 'karyawan_id');
}

public function helmitems(): HasMany
{
    return $this->hasMany(HelmItem::class);
}

    // Scope
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

}
