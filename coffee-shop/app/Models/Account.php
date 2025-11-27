<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type', // cash, bank, ewallet
        'balance',
        'account_number',
        'is_active'
    ];

    // Relationships
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function finances()
    {
        return $this->hasMany(Finance::class);
    }

    public function debtPayments()
    {
        return $this->hasMany(DebtPayment::class);
    }
}