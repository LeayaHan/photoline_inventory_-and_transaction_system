<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/branch-manager/users.css') }}">
    <div class="users-container">
        <div class="users-header">
            <div>
                <h1 class="users-title">Service Material Rules</h1>
                <p class="users-subtitle">Configure the raw-material stock used by each service option. Quantities are whole inventory units per one service unit.</p>
            </div>
            <a class="users-btn users-btn-secondary" href="{{ route('branch-manager.inventory.index') }}">Back to Inventory</a>
        </div>

        @if (session('success'))<div class="users-alert users-alert-success">{{ session('success') }}</div>@endif
        @if ($errors->any())<div class="users-alert" style="color:#b91c1c"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

        <div class="users-card" style="padding:22px;margin-bottom:22px">
            <h2 style="font-size:1.1rem;font-weight:700;margin-bottom:14px">Add or update a material rule</h2>
            <form method="POST" action="{{ route('branch-manager.service-material-rules.store') }}" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:14px;align-items:end">
                @csrf
                <label>Service option
                    <select name="service_key" required style="display:block;width:100%;margin-top:6px">
                        <option value="">Choose service option</option>
                        @foreach ($serviceKeys as $key => $label)
                            <option value="{{ $key }}" @selected(old('service_key') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Raw material
                    <select name="inventory_item_id" required style="display:block;width:100%;margin-top:6px">
                        <option value="">Choose raw material</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" @selected(old('inventory_item_id') == $item->id)>{{ $item->name }} ({{ $item->unit }})</option>
                        @endforeach
                    </select>
                </label>
                <label>Units used per service
                    <input type="number" name="quantity_per_service" min="1" max="1000000" step="1" value="{{ old('quantity_per_service', 1) }}" required style="display:block;width:100%;margin-top:6px">
                </label>
                <label>Usage unit (optional)
                    <input type="text" name="usage_unit" maxlength="30" value="{{ old('usage_unit') }}" placeholder="e.g. sheets" style="display:block;width:100%;margin-top:6px">
                </label>
                <button class="users-btn" type="submit">Save Rule</button>
            </form>
        </div>

        <div class="users-card" style="overflow-x:auto">
            <table class="users-table">
                <thead><tr><th>Service Option</th><th>Raw Material</th><th>Quantity per Service</th><th></th></tr></thead>
                <tbody>
                    @forelse ($rules as $rule)
                        <tr>
                            <td>{{ str_replace('|', ' / ', $rule->service_name) }}</td>
                            <td>{{ $rule->item?->name ?? 'Missing inventory item' }}</td>
                            <td>{{ rtrim(rtrim(number_format((float) $rule->quantity_per_service, 4), '0'), '.') }} {{ $rule->usage_unit ?: $rule->item?->unit }}</td>
                            <td><form method="POST" action="{{ route('branch-manager.service-material-rules.destroy', $rule) }}" data-confirm="Remove this material rule?" data-confirm-title="Remove rule" data-confirm-ok="Remove">@csrf @method('DELETE')<button class="users-btn users-btn-secondary" type="submit">Remove</button></form></td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No material rules configured yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p style="margin-top:14px;font-size:.9rem">Rules are applied when staff marks a transaction Completed. Pending transactions do not deduct stock. A service with no rule here still completes, but no raw material is deducted for it.</p>
    </div>
</x-app-layout>
