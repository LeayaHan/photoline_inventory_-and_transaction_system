<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    private function ensureStaff(): void
    {
        abort_unless(auth()->user()?->isStaff(), 403);
    }

    public function index(Request $request)
    {
        $this->ensureStaff();

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

        return view('staff.inventory.index', compact('products'));
    }

    public function create()
    {
        $this->ensureStaff();

        return view('staff.inventory.create');
    }

    public function store(Request $request)
    {
        $this->ensureStaff();

        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'product_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:50'],
            'quantity' => ['required', 'integer', 'min:0'],
            'reorder_level' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['reorder_level'] = $validated['reorder_level'] ?? 5;

        Product::create($validated);

        return redirect()
            ->route('inventory.index')
            ->with('success', 'Inventory item added successfully.');
    }

    public function edit(Product $inventory)
    {
        $this->ensureStaff();

        return view('staff.inventory.edit', ['product' => $inventory]);
    }

    public function update(Request $request, Product $inventory)
    {
        $this->ensureStaff();

        $validated = $request->validate([
            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($inventory->id),
            ],
            'product_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:50'],
        ]);

        $inventory->update($validated);

        return redirect()
            ->route('inventory.index')
            ->with('success', 'Inventory item updated successfully.');
    }

    public function destroy(Product $inventory)
    {
        $this->ensureStaff();

        if ($inventory->auditDetails()->exists()) {
            return redirect()
                ->route('inventory.index')
                ->with('error', 'This item cannot be deleted because it is already used in an inventory audit.');
        }

        $inventory->delete();

        return redirect()
            ->route('inventory.index')
            ->with('success', 'Inventory item deleted successfully.');
    }
}
