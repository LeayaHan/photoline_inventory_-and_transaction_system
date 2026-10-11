<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Request;

/**
 * Branch manager: view-only inventory.
 * Inventory is changed only when the manager approves a staff request
 * (see InventoryRequestController).
 */
class InventoryController extends Controller
{
    public function index(Request $request)
    {
        // which side of the switch we're on: merchandise or raw_material
        $itemType = InventoryItem::validType($request->query('item_type'));

        $items = InventoryItem::filter($itemType, $request->query('search'), $request->query('category'), $request->query('stock'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        // low stock counts for both types (the switch shows them as little badges)
        $lowCounts = InventoryItem::lowStockCounts();

        return view('branch-manager.inventory.index', compact('items', 'itemType', 'lowCounts'));
    }
}
