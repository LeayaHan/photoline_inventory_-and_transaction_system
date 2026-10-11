<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceMaterialRule extends Model
{
    protected $fillable = [
        'service_name',
        'inventory_item_id',
        'quantity_per_service',
        'usage_unit',
    ];

    protected function casts(): array
    {
        return ['quantity_per_service' => 'decimal:4'];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}
