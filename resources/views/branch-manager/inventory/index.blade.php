<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/branch-manager/users.css') }}">
    <link rel="stylesheet" href="{{ asset('css/branch-manager/inventory.css') }}">

    <div class="users-container">
        <div class="users-header">
            <div>
                <h1 class="users-title">Inventory</h1>
                @if ($lowCounts[$itemType])
                    <p class="users-subtitle">
                        <strong style="color:#b45309;">{{ $lowCounts[$itemType] }} item(s) need restocking.</strong>
                    </p>
                @endif
            </div>
        </div>

        @if (session('success'))
            <div class="users-alert users-alert-success">{{ session('success') }}</div>
        @endif

        <x-inventory-switch :current="$itemType" :low-counts="$lowCounts" route-name="branch-manager.inventory.index" :hint="false" />

        <form method="GET" class="inv-toolbar">
            {{-- keep the same side of the switch when searching/filtering --}}
            <input type="hidden" name="item_type" value="{{ $itemType }}">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or code">

            <select name="category" onchange="this.form.submit()">
                <option value="">All categories</option>
                @foreach (\App\Models\InventoryItem::CATEGORIES[$itemType] as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>

            <select name="stock" onchange="this.form.submit()">
                <option value="">All stock levels</option>
                <option value="low" {{ request('stock') === 'low' ? 'selected' : '' }}>Low / out of stock</option>
            </select>

            <button type="submit" class="users-btn users-btn-secondary">Search</button>
        </form>

        <div class="users-card inv-table-wrap">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Item</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Expiry Date</th>
                        <th>Reorder Level</th>
                        <th>{{ \App\Models\InventoryItem::PRICE_LABELS[$itemType] }}</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td class="inv-code">{{ $item->item_code }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->category }}</td>
                            <td>{{ $item->quantity }} {{ $item->unit }}</td>
                            <td>
                                @if ($item->isOut())
                                    <span class="inv-pill inv-pill-out">Out of stock</span>
                                @elseif ($item->isLow())
                                    <span class="inv-pill inv-pill-low">Low stock</span>
                                @else
                                    <span class="inv-pill inv-pill-ok">In stock</span>
                                @endif
                            </td>
                            <td>
                                @if ($item->expiry_date && $item->expiry_date->lt(today()))
                                    <span class="inv-pill inv-pill-out">Expired · {{ $item->expiry_date->format('M d, Y') }}</span>
                                @elseif ($item->expiry_date && $item->expiry_date->lte(today()->addDays(30)))
                                    <span class="inv-pill inv-pill-low">Expiring · {{ $item->expiry_date->format('M d, Y') }}</span>
                                @else
                                    {{ $item->expiry_date?->format('M d, Y') ?? '—' }}
                                @endif
                            </td>
                            <td>{{ $item->reorder_level }} {{ $item->unit }}</td>
                            <td>₱{{ number_format($item->unit_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="users-empty">No {{ strtolower(\App\Models\InventoryItem::TYPES[$itemType]) }} found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="users-pagination">{{ $items->links() }}</div>
    </div>
</x-app-layout>
