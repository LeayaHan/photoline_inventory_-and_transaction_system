<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionPayment extends Model
{
    // The payment methods staff can pick from
    public const METHODS = ['Cash', 'GCash', 'Card'];

    protected $fillable = [
        'customer_transaction_id',
        'staff_id',
        'amount',
        'payment_type',
        'paid_at',
        'is_initial',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'is_initial' => 'boolean',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(CustomerTransaction::class, 'customer_transaction_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
