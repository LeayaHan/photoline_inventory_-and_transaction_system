<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\ServiceMaterialRule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * The branch manager sets how much raw material one service uses
 * (for example: one 2x2 ID photo = 1 sheet of photo paper).
 * When staff press "Complete" on a transaction, that raw material is taken out of the inventory.
 */
class ServiceMaterialRuleController extends Controller
{
    public function index()
    {
        $rules = ServiceMaterialRule::with('item')->orderBy('service_name')->orderBy('inventory_item_id')->get();
        $items = InventoryItem::where('item_type', 'raw_material')->orderBy('name')->get();
        $serviceKeys = $this->serviceKeys();

        return view('branch-manager.service-material-rules.index', compact('rules', 'items', 'serviceKeys'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_key' => ['required', 'string', Rule::in(array_keys($this->serviceKeys()))],
            'inventory_item_id' => ['required', 'integer', Rule::exists('inventory_items', 'id')->where('item_type', 'raw_material')],
            'quantity_per_service' => ['required', 'numeric', 'gt:0', 'integer', 'max:1000000'],
            'usage_unit' => ['nullable', 'string', 'max:30'],
        ]);

        ServiceMaterialRule::updateOrCreate(
            ['service_name' => $data['service_key'], 'inventory_item_id' => $data['inventory_item_id']],
            ['quantity_per_service' => $data['quantity_per_service'], 'usage_unit' => $data['usage_unit'] ?? null]
        );

        return redirect()->route('branch-manager.service-material-rules.index')->with('success', 'Material rule saved.');
    }

    public function destroy(ServiceMaterialRule $serviceMaterialRule)
    {
        $serviceMaterialRule->delete();

        return redirect()->route('branch-manager.service-material-rules.index')->with('success', 'Material rule removed.');
    }

    /**
     * Every service + size that can have a rule, like  "ID Photos|2×2|"  =>  "ID Photos / 2×2".
     *
     * The key has to match what CustomerTransactionController::complete() looks up
     * (service|size|). Transactions do not record a print layout, so the layout
     * (WB, HB, Option 1...) is not part of the key. Before, ID Photos only offered keys
     * with a layout, so a rule could never match and completing always failed.
     */
    private function serviceKeys(): array
    {
        $keys = [];

        foreach (config('photoline_services.catalog', []) as $serviceName => $service) {
            foreach (($service['sizes'] ?? []) as $size => $unused) {
                $keys[$serviceName . '|' . $size . '|'] = $serviceName . ' / ' . $size;
            }
        }

        return $keys;
    }
}
