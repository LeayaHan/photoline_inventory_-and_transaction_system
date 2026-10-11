<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Request;

/** Staff: read-only view of inventory. Changes go through requests. */
class StaffInventoryController extends Controller
{
    public function index(Request $request)
    {
        // which side of the switch we're on: merchandise or raw_material
        $itemType = InventoryItem::validType($request->query('item_type'));

        $items = InventoryItem::filter($itemType, $request->query('search'), $request->query('category'), $request->query('stock'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $lowCounts = InventoryItem::lowStockCounts();

        return view('staff.inventory.index', compact('items', 'itemType', 'lowCounts'));
    }
}
