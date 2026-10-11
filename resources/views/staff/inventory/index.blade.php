<x-staff-layout title="Inventory">
    <div class="staffui-pagehead">
        <div>
            <h1>Inventory</h1>
        </div>

        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('staff.inventory.requests') }}" class="staffui-btn staffui-btn-outline">My Requests</a>
            <a href="{{ route('staff.inventory.requests.create', ['type' => 'add', 'item_type' => $itemType]) }}" class="staffui-btn">Request New Item</a>
        </div>
    </div>

    @if (session('success'))
        <div class="staffui-alert staffui-alert-success">{{ session('success') }}</div>
    @endif

    <x-inventory-switch :current="$itemType" :low-counts="$lowCounts" route-name="staff.inventory" :hint="false" />

    <form method="GET" class="staffui-filters">
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

        <button type="submit" class="staffui-btn staffui-btn-outline">Search</button>
    </form>

    <div class="staffui-tablecard">
        <table class="staffui-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Expiry Date</th>
                    <th>{{ \App\Models\InventoryItem::PRICE_LABELS[$itemType] }}</th>
                    <th>Request a change</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td class="staffui-code">{{ $item->item_code }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category }}</td>
                        <td>{{ $item->quantity }} {{ $item->unit }}</td>
                        <td>
                            @if ($item->isOut())
                                <span class="staffui-pill staffui-pill-out">Out of stock</span>
                            @elseif ($item->isLow())
                                <span class="staffui-pill staffui-pill-low">Low stock</span>
                            @else
                                <span class="staffui-pill staffui-pill-ok">In stock</span>
                            @endif
                        </td>
                        <td>
                            @if ($item->expiry_date && $item->expiry_date->lt(today()))
                                <span class="staffui-pill staffui-pill-out">Expired · {{ $item->expiry_date->format('M d, Y') }}</span>
                            @elseif ($item->expiry_date && $item->expiry_date->lte(today()->addDays(30)))
                                <span class="staffui-pill staffui-pill-low">Expiring · {{ $item->expiry_date->format('M d, Y') }}</span>
                            @else
                                {{ $item->expiry_date?->format('M d, Y') ?? '—' }}
                            @endif
                        </td>
                        <td>₱{{ number_format($item->unit_price, 2) }}</td>
                        <td>
                            <div class="staffui-rowactions">
                                <a
                                    class="staffui-btn staffui-btn-outline staffui-btn-sm"
                                    href="{{ route('staff.inventory.requests.create', ['type' => 'stock', 'item' => $item->id]) }}">Stock</a>
                                <a
                                    class="staffui-btn staffui-btn-outline staffui-btn-sm"
                                    href="{{ route('staff.inventory.requests.create', ['type' => 'edit', 'item' => $item->id]) }}">Edit</a>
                                <a
                                    class="staffui-btn staffui-btn-danger staffui-btn-sm"
                                    href="{{ route('staff.inventory.requests.create', ['type' => 'delete', 'item' => $item->id]) }}">Remove</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="staffui-emptyrow">No {{ strtolower(\App\Models\InventoryItem::TYPES[$itemType]) }} found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="staffui-pager">{{ $items->links() }}</div>
</x-staff-layout>
