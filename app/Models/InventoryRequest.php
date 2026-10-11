<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventoryRequest extends Model
{
    public const TYPES = [
        'add' => 'Add new item',
        'edit' => 'Edit item details',
        'stock' => 'Stock adjustment',
        'delete' => 'Remove item',
    ];

    public const LABELS = [
        'name' => 'Name',
        'category' => 'Category',
        'unit' => 'Unit',
        'reorder_level' => 'Reorder level',
        'unit_price' => 'Unit price',
        'description' => 'Description',
        'expiry_date' => 'Expiry date',
    ];

    protected $fillable = [
        'type', 'inventory_item_id', 'item_name', 'payload', 'reason', 'status',
        'staff_id', 'reviewed_by', 'reviewed_at', 'manager_remarks',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'reviewed_at' => 'datetime',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /** Human readable list of what this request would change: label => text */
    public function describe(): array
    {
        $p = $this->payload ?? [];
        $item = $this->item;
        $money = fn ($v) => '₱' . number_format((float) $v, 2);

        switch ($this->type) {
            case 'add':
                $lines = [
                    'Type' => InventoryItem::TYPES[$p['item_type'] ?? 'merchandise'] ?? '-',
                    'Name' => $p['name'] ?? '-',
                    'Category' => $p['category'] ?? '-',
                    'Opening stock' => ($p['quantity'] ?? 0) . ' ' . ($p['unit'] ?? ''),
                    'Reorder level' => $p['reorder_level'] ?? 0,
                    'Unit price' => $money($p['unit_price'] ?? 0),
                ];
                if (! empty($p['description'])) {
                    $lines['Description'] = $p['description'];
                }
                if (! empty($p['expiry_date'])) {
                    $lines['Expiry date'] = $p['expiry_date'];
                }
                return $lines;

            case 'edit':
                if (! $item) {
                    return ['Item' => 'This item no longer exists'];
                }
                $lines = [];
                foreach (self::LABELS as $key => $label) {
                    if (! array_key_exists($key, $p)) {
                        continue;
                    }
                    $old = $item->{$key};
                    $new = $p[$key];
                    $same = (is_numeric($old) && is_numeric($new))
                        ? abs((float) $old - (float) $new) < 0.005
                        : (string) $old === (string) $new;
                    if (! $same) {
                        $fmt = fn ($v) => $key === 'unit_price' ? $money($v) : (($v === null || $v === '') ? '(empty)' : $v);
                        $lines[$label] = $fmt($old) . '  →  ' . $fmt($new);
                    }
                }
                return $lines ?: ['Changes' => 'No differences from the current details'];

            case 'stock':
                $adj = (int) ($p['adjustment'] ?? 0);
                $lines = [
                    'Adjustment' => ($adj > 0 ? '+' : '') . $adj . ' ' . ($item->unit ?? ''),
                ];
                if ($item) {
                    $lines['Stock level'] = $item->quantity . '  →  ' . ($item->quantity + $adj);
                }
                return $lines;

            case 'delete':
                $lines = ['Action' => 'Remove this item from inventory'];
                if ($item) {
                    $lines['Current stock'] = $item->quantity . ' ' . $item->unit;
                }
                return $lines;
        }

        return [];
    }

    /** Apply the change and mark approved. Throws RuntimeException with a user-facing message. */
    public function approve(int $managerId, ?string $remarks = null): void
    {
        DB::transaction(function () use ($managerId, $remarks) {
            $req = static::whereKey($this->id)->lockForUpdate()->firstOrFail();

            if (! $req->isPending()) {
                throw new RuntimeException('This request has already been reviewed.');
            }

            $req->applyChange();

            $req->update([
                'status' => 'approved',
                'reviewed_by' => $managerId,
                'reviewed_at' => now(),
                'manager_remarks' => $remarks,
            ]);
        });
    }

    public function decline(int $managerId, string $remarks): void
    {
        DB::transaction(function () use ($managerId, $remarks) {
            $req = static::whereKey($this->id)->lockForUpdate()->firstOrFail();

            if (! $req->isPending()) {
                throw new RuntimeException('This request has already been reviewed.');
            }

            $req->update([
                'status' => 'declined',
                'reviewed_by' => $managerId,
                'reviewed_at' => now(),
                'manager_remarks' => $remarks,
            ]);
        });
    }

    protected function applyChange(): void
    {
        $p = $this->payload ?? [];
        $item = $this->item;

        if ($this->type !== 'add' && ! $item) {
            throw new RuntimeException('This item no longer exists, so the request cannot be approved. Please decline it.');
        }

        switch ($this->type) {
            case 'add':
                $data = Arr::only($p, [...InventoryItem::DETAIL_FIELDS, 'quantity', 'item_type']);

                // requests sent before the merchandise/raw material split have no item_type,
                // so work it out from the category
                $data['item_type'] ??= in_array($data['category'], ['Photo Paper', 'Ink & Toner', 'Packaging'])
                    ? 'raw_material'
                    : 'merchandise';

                $new = InventoryItem::createWithCode($data);
                $this->inventory_item_id = $new->id;
                break;

            case 'edit':
                $item->update(Arr::only($p, InventoryItem::DETAIL_FIELDS));
                $this->item_name = $item->name;
                break;

            case 'stock':
                $newQty = $item->quantity + (int) ($p['adjustment'] ?? 0);
                if ($newQty < 0) {
                    throw new RuntimeException("Not enough stock: only {$item->quantity} {$item->unit} available now, so this removal cannot be approved.");
                }
                $item->update(['quantity' => $newQty]);
                break;

            case 'delete':
                $item->delete();
                break;
        }
    }
}
