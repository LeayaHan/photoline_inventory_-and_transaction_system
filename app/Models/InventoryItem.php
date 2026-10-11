<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InventoryItem extends Model
{
    // The two kinds of stock the branch keeps:
    //   merchandise  = things we sell to customers (cameras, frames, SD cards...)
    //   raw_material = supplies we use up when making prints (photo paper, ink...)
    public const TYPES = [
        'merchandise' => 'Merchandise',
        'raw_material' => 'Raw Materials',
    ];

    // Short blurb shown under the switch so people know what belongs where
    public const DESCRIPTIONS = [
        'merchandise' => 'Products we sell to customers: cameras, batteries, memory cards, frames and albums.',
        'raw_material' => 'Supplies we use up for printing and services: photo paper, ink, packaging.',
    ];

    // Each type has its own list of categories
    public const CATEGORIES = [
        'merchandise' => ['Cameras', 'Camera Batteries', 'Memory Cards', 'Frames', 'Albums', 'Accessories', 'Others'],
        'raw_material' => ['Photo Paper', 'Ink & Toner', 'Packaging', 'Others'],
    ];

    // Merchandise has a selling price, raw materials only have what we paid for them
    public const PRICE_LABELS = [
        'merchandise' => 'Selling Price',
        'raw_material' => 'Cost per Unit',
    ];

    // Units an item can be counted in. Pick the unit you buy and store the item in
    // (packs of paper, bottles of ink, single cameras), so audits stay quick and clear.
    public const UNITS = ['pcs', 'packs', 'boxes', 'bottles', 'rolls', 'sets', 'pairs'];

    // Item codes look like MER-000001 or RAW-000001
    public const CODE_PREFIXES = [
        'merchandise' => 'MER',
        'raw_material' => 'RAW',
    ];

    /** Fields describing an item (everything except stock quantity and type). */
    public const DETAIL_FIELDS = ['name', 'category', 'unit', 'reorder_level', 'unit_price', 'description', 'expiry_date'];

    protected $fillable = [
        'item_code', 'item_type', 'name', 'category', 'unit',
        'quantity', 'reorder_level', 'unit_price', 'description', 'expiry_date',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'reorder_level' => 'integer',
            'unit_price' => 'decimal:2',
            'expiry_date' => 'date',
        ];
    }

    /** Takes whatever came from the URL and gives back a real type (merchandise if it's missing or wrong). */
    public static function validType(?string $type): string
    {
        return array_key_exists($type ?? '', self::TYPES) ? $type : 'merchandise';
    }

    /** Validation rules for an item's details. The category has to belong to the item's type. */
    public static function detailRules(string $itemType): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(self::CATEGORIES[$itemType])],
            'unit' => ['required', 'string', 'max:30'],
            'reorder_level' => ['required', 'integer', 'min:0'],
            'unit_price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'description' => ['nullable', 'string', 'max:1000'],
            'expiry_date' => ['nullable', 'date'],
        ];
    }

    /** Create an item and give it its MER-000001 / RAW-000001 style code. $data must include item_type. */
    public static function createWithCode(array $data): self
    {
        return DB::transaction(function () use ($data) {
            // we only know the id after saving, so use a temporary code first
            $item = static::create($data + ['item_code' => 'TMP-' . Str::uuid()]);

            $prefix = self::CODE_PREFIXES[$item->item_type];
            $item->update(['item_code' => $prefix . '-' . str_pad((string) $item->id, 6, '0', STR_PAD_LEFT)]);

            return $item;
        });
    }

    /** How many items are low or out of stock, for each type. */
    public static function lowStockCounts(): array
    {
        $counts = [];

        foreach (array_keys(self::TYPES) as $type) {
            $counts[$type] = static::where('item_type', $type)
                ->whereColumn('quantity', '<=', 'reorder_level')
                ->count();
        }

        return $counts;
    }

    public function isOut(): bool
    {
        return $this->quantity <= 0;
    }

    public function isLow(): bool
    {
        return $this->quantity > 0 && $this->quantity <= $this->reorder_level;
    }

    /** Warnings for this item in plain words: Out of stock, Low stock, Expired, Expiring soon. Empty list = all fine. */
    public function stockFlags(): array
    {
        $flags = [];

        if ($this->isOut()) {
            $flags[] = 'Out of stock';
        } elseif ($this->isLow()) {
            $flags[] = 'Low stock';
        }

        // Expiry only matters while there is still stock
        if ($this->quantity > 0 && $this->expiry_date) {
            if ($this->expiry_date->lt(today())) {
                $flags[] = 'Expired';
            } elseif ($this->expiry_date->lte(today()->addDays(30))) {
                $flags[] = 'Expiring soon';
            }
        }

        return $flags;
    }

    public function scopeFilter(Builder $query, string $itemType, ?string $search, ?string $category, ?string $stock): Builder
    {
        return $query
            ->where('item_type', $itemType)
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('item_code', 'like', "%{$search}%");
            }))
            ->when($category, fn ($q) => $q->where('category', $category))
            ->when($stock === 'low', fn ($q) => $q->whereColumn('quantity', '<=', 'reorder_level'));
    }
}
