<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryBatch extends Model
{
    protected $fillable = [
        'inventory_item_id',
        'batch_number',
        'quantity',
        'received_date',
        'expiry_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'received_date' => 'date',
            'expiry_date' => 'date',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function isExpired(): bool
    {
        return $this->expiry_date !== null && $this->expiry_date->isPast() && !$this->expiry_date->isToday();
    }

    public function isNearExpiry(int $days = 30): bool
    {
        return $this->expiry_date !== null
            && !$this->isExpired()
            && $this->expiry_date->lte(now()->addDays($days)->startOfDay());
    }
}
