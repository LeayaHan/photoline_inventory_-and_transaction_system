<x-manager-layout>
    <style>
        :root {
            --photo-navy: #102a56;
            --photo-blue: #1f6fd6;
            --photo-red: #d92d3f;
            --photo-border: #dfe5ee;
            --photo-muted: #667085;
            --photo-bg: #f7f9fc;
        }

        .inventory-page {
            min-height: calc(100vh - 86px);
            background: var(--photo-bg);
            padding: 34px 42px 48px;
        }

        .inventory-wrap {
            max-width: 1420px;
            margin: 0 auto;
        }

        .inventory-heading {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 24px;
            margin-bottom: 24px;
        }

        .inventory-heading h1 {
            margin: 0;
            color: var(--photo-navy);
            font-size: 30px;
            font-weight: 800;
            letter-spacing: -.02em;
        }

        .inventory-heading p {
            margin: 7px 0 0;
            color: var(--photo-muted);
            font-size: 14px;
        }

        .inventory-total {
            background: #fff;
            border: 1px solid var(--photo-border);
            border-radius: 12px;
            padding: 13px 18px;
            min-width: 150px;
            box-shadow: 0 4px 14px rgba(16, 42, 86, .05);
        }

        .inventory-total span {
            display: block;
            color: var(--photo-muted);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .inventory-total strong {
            display: block;
            margin-top: 3px;
            color: var(--photo-navy);
            font-size: 24px;
        }

        .inventory-card {
            overflow: hidden;
            background: #fff;
            border: 1px solid var(--photo-border);
            border-radius: 16px;
            box-shadow: 0 7px 24px rgba(16, 42, 86, .06);
        }

        .inventory-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 18px 20px;
            border-bottom: 1px solid var(--photo-border);
        }

        .inventory-toolbar h2 {
            margin: 0;
            color: var(--photo-navy);
            font-size: 17px;
        }

        .inventory-toolbar small {
            display: block;
            margin-top: 3px;
            color: var(--photo-muted);
        }

        .inventory-search {
            display: flex;
            gap: 8px;
            width: min(430px, 100%);
        }

        .inventory-search input {
            flex: 1;
            min-width: 0;
            height: 42px;
            border: 1px solid #cfd7e3;
            border-radius: 9px;
            padding: 0 13px;
            color: #172033;
            background: #fff;
            outline: none;
        }

        .inventory-search input:focus {
            border-color: var(--photo-blue);
            box-shadow: 0 0 0 3px rgba(31, 111, 214, .10);
        }

        .inventory-search button {
            height: 42px;
            border: 0;
            border-radius: 9px;
            padding: 0 17px;
            background: var(--photo-blue);
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }

        .inventory-table-wrap {
            overflow-x: auto;
        }

        .inventory-table {
            width: 100%;
            border-collapse: collapse;
        }

        .inventory-table th {
            background: #f3f6fa;
            color: #475467;
            padding: 13px 20px;
            text-align: left;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .045em;
            white-space: nowrap;
        }

        .inventory-table td {
            padding: 16px 20px;
            border-top: 1px solid #e9edf3;
            color: #344054;
            font-size: 14px;
        }

        .inventory-table tbody tr:hover {
            background: #fbfcfe;
        }

        .sku {
            color: var(--photo-blue);
            font-weight: 800;
            white-space: nowrap;
        }

        .product-name {
            color: var(--photo-navy);
            font-weight: 700;
        }

        .category-pill, .unit-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: 700;
        }

        .category-pill {
            background: #edf4ff;
            color: #1859a8;
        }

        .unit-pill {
            background: #fff1f2;
            color: #b42335;
        }

        .inventory-empty {
            padding: 56px 20px;
            text-align: center;
            color: var(--photo-muted);
        }

        .inventory-pagination {
            padding: 17px 20px;
            border-top: 1px solid var(--photo-border);
        }

        @media (max-width: 800px) {
            .inventory-page { padding: 24px 16px 36px; }
            .inventory-heading, .inventory-toolbar { align-items: stretch; flex-direction: column; }
            .inventory-search { width: 100%; }
        }
    </style>

    <main class="inventory-page">
        <div class="inventory-wrap">
            <header class="inventory-heading">
                <div>
                    <h1>Inventory</h1>
                    <p>View the branch's current inventory item master list.</p>
                </div>

                <div class="inventory-total">
                    <span>Total Items</span>
                    <strong>{{ number_format($totalProducts) }}</strong>
                </div>
            </header>

            <section class="inventory-card">
                <div class="inventory-toolbar">
                    <div>
                        <h2>Inventory Items</h2>
                        <small>Read-only view for branch management.</small>
                    </div>

                    <form class="inventory-search" method="GET" action="{{ route('manager.inventory.index') }}">
                        <input
                            type="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search SKU, product, or category..."
                            aria-label="Search inventory items"
                        >
                        <button type="submit">Search</button>
                    </form>
                </div>

                <div class="inventory-table-wrap">
                    <table class="inventory-table">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th>Current Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                <tr>
                                    <td class="sku">{{ $product->sku }}</td>
                                    <td class="product-name">{{ $product->product_name }}</td>
                                    <td><span class="category-pill">{{ $product->category }}</span></td>
                                    <td><span class="unit-pill">{{ $product->unit }}</span></td>
                                    <td><strong>{{ number_format($product->quantity) }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="inventory-empty">
                                            No inventory items found.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($products->hasPages())
                    <div class="inventory-pagination">
                        {{ $products->links() }}
                    </div>
                @endif
            </section>
        </div>
    </main>
</x-manager-layout>
