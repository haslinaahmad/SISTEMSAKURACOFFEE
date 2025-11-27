<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'customer_name',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'cash_amount',
        'change_amount',
        'payment_method',
        'account_id',
        'status',
        'notes'
    ];

    // Relationships
    public function user() // Kasir
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}