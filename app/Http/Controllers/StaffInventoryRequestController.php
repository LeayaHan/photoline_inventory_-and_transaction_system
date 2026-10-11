<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

/** Staff: submit and track inventory requests. */
class StaffInventoryRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');

        $requests = auth('staff')->user()->inventoryRequests()
            ->with(['item', 'reviewer'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('staff.inventory.requests', compact('requests', 'status'));
    }

    public function create(Request $request)
    {
        $type = $request->query('type');
        abort_unless(array_key_exists($type, InventoryRequest::TYPES), 404);

        if ($type === 'add') {
            // new item: the type comes from whichever side of the switch staff was on
            $item = null;
            $itemType = InventoryItem::validType($request->query('item_type'));
        } else {
            // existing item: it already has a type
            $item = InventoryItem::findOrFail($request->query('item'));
            $itemType = $item->item_type;
        }

        return view('staff.inventory.request-form', compact('type', 'item', 'itemType'));
    }

    public function store(Request $request)
    {
        $type = $request->validate([
            'type' => ['required', Rule::in(array_keys(InventoryRequest::TYPES))],
        ])['type'];

        $reasonRequired = ['required', 'string', 'max:500'];
        $reasonOptional = ['nullable', 'string', 'max:500'];
        $item = null;

        if ($type === 'add') {
            $itemType = InventoryItem::validType($request->input('item_type'));

            $data = $request->validate(InventoryItem::detailRules($itemType) + [
                'quantity' => ['required', 'integer', 'min:0'],
                'reason' => $reasonOptional,
            ]);
            $payload = Arr::only($data, [...InventoryItem::DETAIL_FIELDS, 'quantity']);
            $payload['item_type'] = $itemType;
            $itemName = $data['name'];
        } else {
            $request->validate(['item_id' => ['required', 'exists:inventory_items,id']]);
            $item = InventoryItem::findOrFail($request->input('item_id'));
            $itemName = $item->name;

            if ($type === 'edit') {
                $data = $request->validate(InventoryItem::detailRules($item->item_type) + ['reason' => $reasonRequired]);
                $payload = Arr::only($data, InventoryItem::DETAIL_FIELDS);

                $unchanged = collect($payload)->every(function ($new, $key) use ($item) {
                    $old = $item->{$key};
                    return (is_numeric($old) && is_numeric($new))
                        ? abs((float) $old - (float) $new) < 0.005
                        : (string) $old === (string) $new;
                });
                if ($unchanged) {
                    return back()->withInput()->withErrors(['name' => 'You have not changed anything.']);
                }
            } elseif ($type === 'stock') {
                $data = $request->validate([
                    'direction' => ['required', 'in:in,out'],
                    'quantity' => ['required', 'integer', 'min:1', 'max:1000000'],
                    'reason' => $reasonRequired,
                ]);
                if ($data['direction'] === 'out' && $data['quantity'] > $item->quantity) {
                    return back()->withInput()->withErrors([
                        'quantity' => "Only {$item->quantity} {$item->unit} in stock right now.",
                    ]);
                }
                $payload = ['adjustment' => $data['direction'] === 'in' ? (int) $data['quantity'] : -(int) $data['quantity']];
            } else { // delete
                $data = $request->validate(['reason' => $reasonRequired]);
                $payload = null;
            }
        }

        InventoryRequest::create([
            'type' => $type,
            'inventory_item_id' => $item?->id,
            'item_name' => $itemName,
            'payload' => $payload,
            'reason' => $data['reason'] ?? null,
            'status' => 'pending',
            'staff_id' => auth('staff')->id(),
        ]);

        return redirect()->route('staff.inventory.requests')
            ->with('success', 'Request submitted. The branch manager will review it.');
    }
}
