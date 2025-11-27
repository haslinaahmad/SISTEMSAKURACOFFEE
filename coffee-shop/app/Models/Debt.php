<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', // payable, receivable
        'party_name',
        'amount',
        'remaining_amount',
        'due_date',
        'status', // pending, partial, paid
        'description'
    ];

    protected $casts = [
        'due_date' => 'date'
    ];

    public function payments()
    {
        return $this->hasMany(DebtPayment::class);
    }
}