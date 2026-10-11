<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionDetail extends Model
{
    protected $fillable = [
        'transaction_id',
        'line_type',
        'inventory_item_id',
        'service_type',
        'description',
        'service_size',
        'print_layout',
        'order_notes',
        'quantity',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'amount' => 'decimal:2',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(CustomerTransaction::class, 'transaction_id');
    }
}
