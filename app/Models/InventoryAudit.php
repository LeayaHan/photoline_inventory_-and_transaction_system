<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryAudit extends Model
{
    protected $fillable = [
        'audit_number',
        'audit_date',
        'auditor_name',
        'auditor_role',
        'staff_id',
        'status',
        'remarks',
        'finalized_at',
        'finalized_by',
    ];

    protected function casts(): array
    {
        return [
            'audit_date' => 'date',
            'finalized_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventoryAuditItem::class);
    }

    public function isFinalized(): bool
    {
        return $this->status === 'finalized';
    }
}
