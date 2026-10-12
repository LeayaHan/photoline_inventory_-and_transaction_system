<?php

namespace App\Http\Controllers;

use App\Models\InventoryAudit;
use Illuminate\Http\Request;

class ManagerAuditController extends Controller
{
    /**
     * Display inventory audit records for the Branch Manager.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->isManager()) {
            abort(403, 'Unauthorized access.');
        }

        $query = InventoryAudit::with([
            'user',
            'details.product',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereDate('audit_date', $search)

                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    })

                    ->orWhereHas('details.product', function ($productQuery) use ($search) {
                        $productQuery
                            ->where(
                                'product_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'sku',
                                'like',
                                "%{$search}%"
                            );
                    });
            });
        }

        $audits = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'manager.audits.index',
            compact('audits')
        );
    }

    /**
     * Display a single inventory audit for manager review.
     */
    public function show(InventoryAudit $audit)
    {
        if (!auth()->user()->isManager()) {
            abort(403, 'Unauthorized access.');
        }

        $audit->load([
            'user',
            'details.product',
        ]);

        return view(
            'manager.audits.show',
            compact('audit')
        );
    }
}