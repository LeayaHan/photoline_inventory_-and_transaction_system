<?php

namespace App\Http\Controllers;

use App\Models\AuditDetail;
use App\Models\InventoryAudit;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryAuditController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryAudit::with('user');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereDate('audit_date', $search)
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $audits = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('staff.audits.index', compact('audits'));
    }

    public function create()
    {
        $products = Product::orderBy('product_name')->get();

        return view('staff.audits.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'audit_date' => ['required', 'date'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.recorded_qty' => ['required', 'integer', 'min:0'],
            'products.*.counted_qty' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($validated) {
            $audit = InventoryAudit::create([
                'user_id' => auth()->id(),
                'audit_date' => $validated['audit_date'],
                'status' => 'Ongoing',
            ]);

            foreach ($validated['products'] as $product) {
                AuditDetail::create([
                    'inventory_audit_id' => $audit->id,
                    'product_id' => $product['product_id'],
                    'recorded_qty' => $product['recorded_qty'],
                    'counted_qty' => $product['counted_qty'],
                    'discrepancy' => $product['counted_qty'] - $product['recorded_qty'],
                ]);
            }
        });

        $audit = InventoryAudit::latest()->first();

        return redirect()
            ->route('staff.audits.show', $audit)
            ->with('success', 'Inventory audit created successfully.');
    }

    public function show(InventoryAudit $audit)
    {
        $audit->load(['user', 'details.product']);

        return view('staff.audits.show', compact('audit'));
    }

    public function edit(InventoryAudit $audit)
    {
        $audit->load('details.product');

        return view('staff.audits.edit', compact('audit'));
    }

    public function update(Request $request, InventoryAudit $audit)
    {
        $validated = $request->validate([
            'audit_date' => ['required', 'date'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.detail_id' => ['required', 'exists:audit_details,id'],
            'products.*.recorded_qty' => ['required', 'integer', 'min:0'],
            'products.*.counted_qty' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($validated, $audit) {
            $audit->update([
                'audit_date' => $validated['audit_date'],
            ]);

            foreach ($validated['products'] as $product) {
                $detail = AuditDetail::where('id', $product['detail_id'])
                    ->where('inventory_audit_id', $audit->id)
                    ->firstOrFail();

                $detail->update([
                    'recorded_qty' => $product['recorded_qty'],
                    'counted_qty' => $product['counted_qty'],
                    'discrepancy' => $product['counted_qty'] - $product['recorded_qty'],
                ]);
            }
        });

        return redirect()
            ->route('staff.audits.show', $audit)
            ->with('success', 'Inventory audit updated successfully.');
    }

    public function complete(InventoryAudit $audit)
    {
        $audit->update([
            'status' => 'Completed',
        ]);

        return redirect()
            ->route('staff.audits.show', $audit)
            ->with('success', 'Inventory audit marked as completed.');
    }
}