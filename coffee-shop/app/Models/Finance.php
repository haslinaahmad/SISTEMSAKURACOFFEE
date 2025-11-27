<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'type', // income, expense
        'category',
        'amount',
        'description',
        'transaction_date',
        'reference_proof',
        'user_id'
    ];

    protected $casts = [
        'transaction_date' => 'date'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes untuk mempermudah filtering
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }
}