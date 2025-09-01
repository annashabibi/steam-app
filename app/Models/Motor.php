<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;



class Motor extends Model
{
    use HasFactory;

    protected $table = 'motors';
    protected $fillable = [
        'category_id',
        'nama_motor',
        'harga',
        'image',
    ];

    /**
     * Get the category that owns the motor
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the transactions for the motor.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
