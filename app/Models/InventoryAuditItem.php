<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryAuditItem extends Model
{
    protected $fillable = [
        'inventory_audit_id',
        'inventory_item_id',
        'item_code',
        'item_name',
        'unit',
        'recorded_quantity',
        'counted_quantity',
        'physical_quantity',
        'discrepancy',
        'status',
        'remarks',
        'counted_at',
    ];

    protected function casts(): array
    {
        return [
            'recorded_quantity' => 'integer',
            'counted_quantity' => 'integer',
            'physical_quantity' => 'integer',
            'discrepancy' => 'integer',
            'counted_at' => 'datetime',
        ];
    }

    public function audit(): BelongsTo
    {
        return $this->belongsTo(InventoryAudit::class, 'inventory_audit_id');
    }
}
