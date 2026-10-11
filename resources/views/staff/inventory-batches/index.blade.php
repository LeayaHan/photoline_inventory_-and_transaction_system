<x-staff-layout title="Stock Batches & Expiry">
    <link rel="stylesheet" href="{{ asset('css/inventory-batches.css') }}">
    <div class="staffui-pagehead">
        <div>
            <h1>Stock Batches & Expiry</h1>
        </div>
    </div>

    @if (session('success'))
        <div class="batch-alert batch-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="batch-alert batch-error"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="batch-summary">
        <div><span>Total Batches</span><strong>{{ $summary['batches'] }}</strong></div>
        <div><span>Expired With Stock</span><strong>{{ $summary['expired'] }}</strong></div>
        <div><span>Expiring Within 30 Days</span><strong>{{ $summary['near'] }}</strong></div>
    </div>

    <div class="batch-columns">
        <section class="batch-card">
            <h2>Add Stock Batch</h2>
            <form method="POST" action="{{ route('staff.inventory-batches.store') }}" class="batch-form">
                @csrf
                <label>Inventory Item<select name="inventory_item_id" required><option value="">Choose item</option>@foreach ($items as $item)<option value="{{ $item->id }}">{{ $item->item_code }} · {{ $item->name }} ({{ $item->item_type === 'raw_material' ? 'Raw material' : 'Merchandise' }})</option>@endforeach</select></label>
                <label>Batch Number <span>(optional)</span><input name="batch_number" maxlength="80" placeholder="Supplier batch / lot number"></label>
                <label>Quantity Received<input type="number" name="quantity" min="1" max="100000000" required></label>
                <div class="batch-form-grid">
                    <label>Received Date<input type="date" name="received_date" value="{{ now()->format('Y-m-d') }}"></label>
                    <label>Expiry Date <span>(optional)</span><input type="date" name="expiry_date"></label>
                </div>
                <label>Notes<input name="notes" maxlength="500" placeholder="Supplier or storage notes"></label>
                <button type="submit" class="staffui-btn">Add Batch</button>
            </form>
        </section>

        <section class="batch-card">
            <h2>Configure Material Usage</h2>
            <form method="POST" action="{{ route('staff.inventory-batches.rules.store') }}" class="batch-form">
                @csrf
                <label>Service Name<input name="service_name" maxlength="100" required placeholder="e.g. 4R Photo Print"></label>
                <label>Raw Material<select name="inventory_item_id" required><option value="">Choose raw material</option>@foreach ($items->where('item_type', 'raw_material') as $item)<option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>@endforeach</select></label>
                <label>Quantity Used Per Service<input type="number" name="quantity_per_service" min="0.0001" max="100000" step="0.0001" required placeholder="e.g. 1"></label>
                <label>Usage Unit <span>(optional)</span><input name="usage_unit" maxlength="30" placeholder="sheets, ml, grams"></label>
                <button type="submit" class="staffui-btn">Save Usage Rule</button>
            </form>
            <div class="batch-rules">
                <h3>Configured Rules</h3>
                @forelse ($rules as $rule)
                    <div class="batch-rule">
                        <div><strong>{{ $rule->service_name }}</strong><span>{{ $rule->item->name }} · {{ rtrim(rtrim(number_format((float) $rule->quantity_per_service, 4), '0'), '.') }} {{ $rule->usage_unit ?: $rule->item->unit }} per service</span></div>
                        <form method="POST" action="{{ route('staff.inventory-batches.rules.destroy', $rule) }}" data-confirm="Remove this material usage rule?" data-confirm-title="Remove rule" data-confirm-ok="Remove">@csrf @method('DELETE')<button type="submit" class="batch-remove">Remove</button></form>
                    </div>
                @empty
                    <p class="batch-muted">No material usage rules configured yet.</p>
                @endforelse
            </div>
        </section>
    </div>

    <section class="batch-card batch-list-card">
        <div class="batch-list-head"><div><h2>Stock Batch History</h2></div>
            <form method="GET" class="batch-search"><input name="search" value="{{ request('search') }}" placeholder="Search item or batch"><select name="status"><option value="">All batches</option><option value="expired" @selected(request('status') === 'expired')>Expired</option><option value="near" @selected(request('status') === 'near')>Expiring within 30 days</option></select><button class="staffui-btn" type="submit">Filter</button></form>
        </div>
        <div class="batch-table-wrap"><table class="batch-table"><thead><tr><th>Item</th><th>Batch</th><th>Remaining Qty</th><th>Received</th><th>Expiry</th><th>Status</th></tr></thead><tbody>
            @forelse ($batches as $batch)
                @php
                    $expired = $batch->expiry_date && $batch->expiry_date->lt(today());
                    $near = $batch->expiry_date && !$expired && $batch->expiry_date->lte(today()->addDays(30));
                @endphp
                <tr><td><strong>{{ $batch->item->name }}</strong><small>{{ $batch->item->item_code }} · {{ $batch->item->item_type === 'raw_material' ? 'Raw material' : 'Merchandise' }}</small></td><td>{{ $batch->batch_number ?: 'Not provided' }}</td><td>{{ number_format($batch->quantity) }} {{ $batch->item->unit }}</td><td>{{ $batch->received_date?->format('M d, Y') ?? '—' }}</td><td>{{ $batch->expiry_date?->format('M d, Y') ?? 'No date recorded' }}</td><td>@if ($batch->quantity <= 0)<span class="batch-status">Depleted</span>@elseif ($expired)<span class="batch-status batch-status-expired">Expired</span>@elseif ($near)<span class="batch-status batch-status-near">Expiring soon</span>@else<span class="batch-status batch-status-active">{{ $batch->expiry_date ? 'Active' : 'No expiry set' }}</span>@endif</td></tr>
            @empty
                <tr><td colspan="6" class="batch-empty">No stock batches found.</td></tr>
            @endforelse
        </tbody></table></div>
        <div class="batch-pagination">{{ $batches->links() }}</div>
    </section>
</x-staff-layout>
