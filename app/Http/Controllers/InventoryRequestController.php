<?php

namespace App\Http\Controllers;

use App\Models\InventoryRequest;
use Illuminate\Http\Request;
use RuntimeException;

/** Branch manager: review staff inventory requests. */
class InventoryRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $requests = InventoryRequest::with(['item', 'staff', 'reviewer'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $counts = [
            'pending' => InventoryRequest::where('status', 'pending')->count(),
            'approved' => InventoryRequest::where('status', 'approved')->count(),
            'declined' => InventoryRequest::where('status', 'declined')->count(),
        ];

        return view('branch-manager.inventory-requests.index', compact('requests', 'status', 'counts'));
    }

    public function approve(Request $request, InventoryRequest $inventoryRequest)
    {
        $data = $request->validate(['remarks' => ['nullable', 'string', 'max:500']]);

        try {
            $inventoryRequest->approve(auth('web')->id(), $data['remarks'] ?? null);
        } catch (RuntimeException $e) {
            return back()->withErrors(['request_' . $inventoryRequest->id => $e->getMessage()]);
        }

        return back()->with('success', 'Request approved and inventory updated.');
    }

    public function decline(Request $request, InventoryRequest $inventoryRequest)
    {
        $data = $request->validate(
            ['remarks' => ['required', 'string', 'max:500']],
            ['remarks.required' => 'Please give a reason for declining.']
        );

        try {
            $inventoryRequest->decline(auth('web')->id(), $data['remarks']);
        } catch (RuntimeException $e) {
            return back()->withErrors(['request_' . $inventoryRequest->id => $e->getMessage()]);
        }

        return back()->with('success', 'Request declined.');
    }
}
