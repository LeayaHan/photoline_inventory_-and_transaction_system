<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'control_number',
        'customer_name',
        'service_type',
        'transaction_date',
        'item_description',
        'status',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}