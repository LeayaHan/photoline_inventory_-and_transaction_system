<?php

namespace App\Http\Controllers;

use App\Models\InventoryAudit;
use App\Models\InventoryAuditItem;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryAuditController extends Controller
{
    public function managerIndex(Request $request)
    {
        // The manager sees EVERY audit here: drafts (still being counted) and finalized ones.
        // The Status filter is only there to narrow the list down when wanted.
        $audits = InventoryAudit::query()
            ->withCount([
                'items',
                'items as counted_items_count' => fn ($query) => $query->whereNotNull('physical_quantity'),
            ])
            ->when($request->filled('status') && in_array($request->status, ['draft', 'finalized'], true),
                fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('auditor'), fn ($query) => $query->where('auditor_name', 'like', '%' . $request->auditor . '%'))
            ->when($request->filled('product'), fn ($query) => $query->whereHas('items', fn ($items) => $items
                ->where('item_name', 'like', '%' . $request->product . '%')
                ->orWhere('item_code', 'like', '%' . $request->product . '%')))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('audit_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('audit_date', '<=', $request->date_to))
            ->latest('audit_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $finalizedItems = InventoryAuditItem::whereHas('audit', fn ($query) => $query->where('status', 'finalized'))
            ->whereNotNull('physical_quantity');

        $summary = [
            'audits' => InventoryAudit::count(),
            'items_checked' => (clone $finalizedItems)->count(),
            'shortages' => (clone $finalizedItems)->whereColumn('physical_quantity', '<', 'recorded_quantity')->count(),
            'excesses' => (clone $finalizedItems)->whereColumn('physical_quantity', '>', 'recorded_quantity')->count(),
            'matched' => (clone $finalizedItems)->whereColumn('physical_quantity', '=', 'recorded_quantity')->count(),
            'draft' => InventoryAudit::where('status', 'draft')->count(),
            'finalized' => InventoryAudit::where('status', 'finalized')->count(),
        ];

        return view('branch-manager.audits.index', compact('audits', 'summary'));
    }

    public function managerShow(InventoryAudit $audit)
    {
        $audit->load('items');

        return view('branch-manager.audits.show', compact('audit'));
    }

    public function staffIndex(Request $request)
    {
        $staffId = auth('staff')->id();

        $audits = InventoryAudit::query()
            ->where('staff_id', $staffId)
            ->withCount('items')
            ->with('items')
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('audit_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('audit_date', '<=', $request->date_to))
            ->when($request->filled('status') && $request->status !== 'all', fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('auditor'), fn ($query) => $query->where('auditor_name', 'like', '%' . $request->auditor . '%'))
            ->when($request->filled('product'), function ($query) use ($request) {
                $query->whereHas('items', function ($items) use ($request) {
                    $items->where('item_name', 'like', '%' . $request->product . '%')
                        ->orWhere('item_code', 'like', '%' . $request->product . '%');
                });
            })
            ->latest('audit_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $summaryItems = InventoryAuditItem::query()->whereHas('audit', function ($query) use ($staffId, $request) {
            $query->where('staff_id', $staffId)
                ->when($request->filled('date_from'), fn ($q) => $q->whereDate('audit_date', '>=', $request->date_from))
                ->when($request->filled('date_to'), fn ($q) => $q->whereDate('audit_date', '<=', $request->date_to));
        });

        $summary = [
            'audits' => InventoryAudit::where('staff_id', $staffId)
                ->when($request->filled('date_from'), fn ($q) => $q->whereDate('audit_date', '>=', $request->date_from))
                ->when($request->filled('date_to'), fn ($q) => $q->whereDate('audit_date', '<=', $request->date_to))
                ->count(),
            'items_checked' => (clone $summaryItems)->whereNotNull('physical_quantity')->count(),
            'shortages' => (clone $summaryItems)->where('status', 'Shortage')->count(),
            'excesses' => (clone $summaryItems)->where('status', 'Excess')->count(),
            'matched' => (clone $summaryItems)->where('status', 'Matched')->count(),
        ];

        return view('staff.audits.index', compact('audits', 'summary'));
    }

    public function create()
    {
        if (! InventoryItem::exists()) {
            return redirect()->route('staff.audits')->with('error', 'Add inventory items before creating an audit.');
        }

        $staff = auth('staff')->user();

        $audit = DB::transaction(function () use ($staff) {
            $audit = InventoryAudit::create([
                'audit_number' => 'TEMP-' . strtoupper(bin2hex(random_bytes(4))),
                'audit_date' => now()->toDateString(),
                'auditor_name' => $staff->full_name,
                'auditor_role' => $staff->position ?? 'Staff',
                'staff_id' => $staff->id,
                'status' => 'draft',
            ]);

            $audit->update(['audit_number' => 'AUD-' . str_pad((string) $audit->id, 6, '0', STR_PAD_LEFT)]);

            foreach (InventoryItem::orderBy('name')->get() as $item) {
                $audit->items()->create([
                    'inventory_item_id' => $item->id,
                    'item_code' => $item->item_code,
                    'item_name' => $item->name,
                    'unit' => $item->unit,
                    'recorded_quantity' => $item->quantity,
                    'counted_quantity' => 0,
                    'physical_quantity' => null,
                    'discrepancy' => 0,
                    'status' => 'Not Counted',
                ]);
            }

            return $audit;
        });

        return redirect()->route('staff.audits.show', $audit)->with('success', 'Audit created. Enter the physical counts, save your changes, and finalize when complete.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'audit_date' => ['required', 'date'],
        ]);

        $staff = auth('staff')->user();

        $audit = DB::transaction(function () use ($data, $staff) {
            $audit = InventoryAudit::create([
                'audit_number' => 'TEMP-' . strtoupper(bin2hex(random_bytes(4))),
                'audit_date' => $data['audit_date'],
                'auditor_name' => $staff->full_name,
                'auditor_role' => $staff->position ?? 'Staff',
                'staff_id' => $staff->id,
                'status' => 'draft',
            ]);

            $audit->update(['audit_number' => 'AUD-' . str_pad((string) $audit->id, 6, '0', STR_PAD_LEFT)]);

            foreach (InventoryItem::orderBy('name')->get() as $item) {
                $audit->items()->create([
                    'inventory_item_id' => $item->id,
                    'item_code' => $item->item_code,
                    'item_name' => $item->name,
                    'unit' => $item->unit,
                    'recorded_quantity' => $item->quantity,
                    'counted_quantity' => 0,
                    'physical_quantity' => null,
                    'discrepancy' => 0,
                    'status' => 'Not Counted',
                ]);
            }

            return $audit;
        });

        return redirect()->route('staff.audits.show', $audit)->with('success', 'Audit created. Enter the physical counts, save your changes, and finalize when complete.');
    }

    public function staffShow(InventoryAudit $audit)
    {
        $this->ensureStaffOwnsAudit($audit);
        $audit->load(['items' => fn ($query) => $query->orderBy('item_name')]);

        return view('staff.audits.show', compact('audit'));
    }

    public function update(Request $request, InventoryAudit $audit)
    {
        $this->ensureStaffOwnsAudit($audit);

        $data = $request->validate([
            'items' => ['sometimes', 'array'],
            'items.*.physical_quantity' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
            'items.*.remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($audit, $data) {
            $lockedAudit = InventoryAudit::whereKey($audit->id)->lockForUpdate()->firstOrFail();

            if ($lockedAudit->isFinalized()) {
                abort(403, 'Finalized audits cannot be edited.');
            }


            foreach (($data['items'] ?? []) as $itemId => $values) {
                $item = $lockedAudit->items()->whereKey($itemId)->first();

                if (! $item) {
                    continue;
                }

                $physical = isset($values['physical_quantity']) && $values['physical_quantity'] !== ''
                    ? (int) $values['physical_quantity']
                    : null;
                $wasPhysical = $item->physical_quantity;
                if (! is_null($physical) && (is_null($wasPhysical) || (int) $wasPhysical !== $physical)) {
                    $currentStock = InventoryItem::whereKey($item->inventory_item_id)->lockForUpdate()->value('quantity');
                    if ($currentStock !== null) {
                        $item->recorded_quantity = (int) $currentStock;
                    }
                }
                $difference = is_null($physical) ? 0 : $physical - (int) $item->recorded_quantity;
                $result = is_null($physical) ? 'Not Counted' : ($difference < 0 ? 'Shortage' : ($difference > 0 ? 'Excess' : 'Matched'));

                $item->update([
                    'recorded_quantity' => $item->recorded_quantity,
                    'physical_quantity' => $physical,
                    'counted_quantity' => $physical ?? 0,
                    'discrepancy' => $difference,
                    'status' => $result,
                    'counted_at' => is_null($physical) ? null : now(),
                    'remarks' => $values['remarks'] ?? null,
                ]);
            }
        });

        return redirect()->route('staff.audits.show', $audit)->with('success', 'Audit changes saved. You can continue editing until you finalize it.');
    }

    public function finalize(InventoryAudit $audit)
    {
        $this->ensureStaffOwnsAudit($audit);

        DB::transaction(function () use ($audit) {
            $lockedAudit = InventoryAudit::whereKey($audit->id)->lockForUpdate()->firstOrFail();

            if ($lockedAudit->isFinalized()) {
                abort(403, 'This audit is already finalized and cannot be changed.');
            }

            if ($lockedAudit->items()->whereNull('physical_quantity')->exists()) {
                throw ValidationException::withMessages([
                    'items' => 'Enter a physical count for every listed item before finalizing this audit.',
                ]);
            }

            $lockedAudit->update([
                'status' => 'finalized',
                'finalized_at' => now(),
                'finalized_by' => auth('staff')->id(),
            ]);
        });

        return redirect()->route('staff.audits.show', $audit)->with('success', 'Audit finalized successfully. It is now read-only.');
    }

    private function ensureStaffOwnsAudit(InventoryAudit $audit): void
    {
        abort_unless((int) $audit->staff_id === (int) auth('staff')->id(), 403);
    }
}
