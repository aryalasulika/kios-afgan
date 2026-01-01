<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'kasir_id',
        'total_amount',
        'payment_method',
        'cash_received',
        'change_amount',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'cash_received' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
