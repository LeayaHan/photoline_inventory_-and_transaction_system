<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ReplenishmentController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isManager()) {
            abort(403, 'Unauthorized access.');
        }

        $products = Product::whereColumn('quantity', '<=', 'reorder_level')
            ->orderBy('quantity')
            ->orderBy('product_name')
            ->get();

        return view('manager.replenishments.index', compact('products'));
    }
}
