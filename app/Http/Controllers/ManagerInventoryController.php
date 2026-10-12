<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ManagerInventoryController extends Controller
{
    private function ensureManager(): void
    {
        abort_unless(auth()->user()?->isManager(), 403);
    }

    public function index(Request $request)
    {
        $this->ensureManager();

        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                    ->orWhere('product_name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $products = $query
            ->orderBy('product_name')
            ->paginate(10)
            ->withQueryString();

        $totalProducts = Product::count();

        return view('manager.inventory.index', compact('products', 'totalProducts'));
    }
}
