<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
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

        return view('inventory.index', compact('products'));
    }

    public function create()
    {
        return view('inventory.create');
    }

    public function store(Request $request)
    {
        Product::create($this->validated($request));

        return redirect()
            ->route('products.index')
            ->with('success', 'Inventory item added successfully.');
    }

    public function edit(Product $product)
    {
        return view('inventory.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->validated($request, $product));

        return redirect()
            ->route('products.index')
            ->with('success', 'Inventory item updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Items that were already counted in an audit must stay, otherwise
        // the audit history would lose its records.
        if ($product->auditDetails()->exists()) {
            return redirect()
                ->route('products.index')
                ->with('error', 'This item is part of an inventory audit and cannot be deleted. Set its quantity to 0 instead.');
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Inventory item deleted successfully.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'sku')->ignore($product?->id),
            ],
            'product_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'quantity' => ['required', 'integer', 'min:0'],
            'reorder_level' => ['required', 'integer', 'min:0'],
        ]);
    }
}
