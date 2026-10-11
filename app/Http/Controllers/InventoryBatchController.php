<?php

namespace App\Http\Controllers;

use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\ServiceMaterialRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryBatchController extends Controller
{
    public function index(Request $request)
    {
        $batches = InventoryBatch::with('item')
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = trim($request->string('search')->toString());
                $query->where(function ($subquery) use ($term) {
                    $subquery->where('batch_number', 'like', "%{$term}%")
                        ->orWhereHas('item', fn ($item) => $item->where('name', 'like', "%{$term}%")->orWhere('item_code', 'like', "%{$term}%"));
                });
            })
            ->when($request->status === 'expired', fn ($query) => $query->whereNotNull('expiry_date')->whereDate('expiry_date', '<', now()->toDateString()))
            ->when($request->status === 'near', fn ($query) => $query->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(30)->toDateString()]))
            ->orderByRaw('expiry_date IS NULL')
            ->orderBy('expiry_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $items = InventoryItem::orderBy('name')->get();
        $rules = ServiceMaterialRule::with('item')->orderBy('service_name')->get();

        $summary = [
            'batches' => InventoryBatch::count(),
            'expired' => InventoryBatch::whereNotNull('expiry_date')->whereDate('expiry_date', '<', now()->toDateString())->where('quantity', '>', 0)->count(),
            'near' => InventoryBatch::whereNotNull('expiry_date')->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(30)->toDateString()])->where('quantity', '>', 0)->count(),
        ];

        return view('staff.inventory-batches.index', compact('batches', 'items', 'rules', 'summary'));
    }

    public function storeBatch(Request $request)
    {
        $data = $request->validate([
            'inventory_item_id' => ['required', 'integer', 'exists:inventory_items,id'],
            'batch_number' => ['nullable', 'string', 'max:80'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000000'],
            'received_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:received_date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($data) {
            $item = InventoryItem::whereKey($data['inventory_item_id'])->lockForUpdate()->firstOrFail();

            InventoryBatch::create($data);

            $item->quantity = InventoryBatch::where('inventory_item_id', $item->id)->sum('quantity');
            $item->save();
        });

        return back()->with('success', 'Stock batch added and inventory quantity synchronized.');
    }

    public function storeRule(Request $request)
    {
        $data = $request->validate([
            'service_name' => ['required', 'string', 'max:100'],
            'inventory_item_id' => ['required', 'integer', 'exists:inventory_items,id'],
            'quantity_per_service' => ['required', 'numeric', 'gt:0', 'max:100000'],
            'usage_unit' => ['nullable', 'string', 'max:30'],
        ]);

        $item = InventoryItem::findOrFail($data['inventory_item_id']);
        if ($item->item_type !== 'raw_material') {
            throw ValidationException::withMessages([
                'inventory_item_id' => 'Material usage rules must use a Raw Materials inventory item.',
            ]);
        }

        ServiceMaterialRule::updateOrCreate(
            ['service_name' => trim($data['service_name']), 'inventory_item_id' => $data['inventory_item_id']],
            ['quantity_per_service' => $data['quantity_per_service'], 'usage_unit' => $data['usage_unit'] ?? null]
        );

        return back()->with('success', 'Material usage rule saved. The usage amount is a configurable estimate and should match your actual printer and service.');
    }

    public function destroyRule(ServiceMaterialRule $rule)
    {
        $rule->delete();

        return back()->with('success', 'Material usage rule removed.');
    }
}
