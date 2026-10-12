<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditDetail extends Model
{
    protected $fillable = [
        'inventory_audit_id',
        'product_id',
        'recorded_qty',
        'counted_qty',
        'discrepancy',
    ];

    public function audit(): BelongsTo
    {
        return $this->belongsTo(InventoryAudit::class, 'inventory_audit_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}