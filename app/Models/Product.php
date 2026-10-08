<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'sku',
        'product_name',
        'category',
        'unit',
        'quantity',
        'reorder_level',
    ];

    public function auditDetails(): HasMany
    {
        return $this->hasMany(AuditDetail::class);
    }
}