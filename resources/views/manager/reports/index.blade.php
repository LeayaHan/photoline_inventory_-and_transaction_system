@php
    $activeTab = in_array(request('report'), ['transactions', 'audits'])
        ? request('report')
        : 'transactions';
@endphp

<x-manager-layout title="Manager Reports | Photoline Abreeza">

@push('styles')
<style>
    /* PAGE */
    .page { width: 92%; max-width: 1400px; margin: 35px auto; }
    .intro { margin-bottom: 20px; }
    .intro h2 { margin: 0 0 6px; font-size: 28px; }
    .intro p { margin: 0; color: #6b7280; font-size: 14px; }

    /* REPORT TABS */
    .report-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 16px;
    }
    .tab-button {
        border: 1px solid #d1d5db;
        background: white;
        color: #374151;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
    }
    .tab-button:hover { background: #f3f4f6; }
    .tab-button.is-active {
        background: #111827;
        border-color: #111827;
        color: white;
    }

    /* FILTERS */
    .control-panel {
        background: white;
        border-radius: 10px;
        padding: 20px 23px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
    }
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
    }
    .filter-group label {
        display: block;
        margin-bottom: 6px;
        font-size: 12px;
        font-weight: bold;
        color: #4b5563;
    }
    .filter-group input,
    .filter-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: white;
    }
    .filter-actions { display: flex; gap: 10px; margin-top: 17px; }
    .apply-button {
        border: none;
        background: #111827;
        color: white;
        padding: 10px 16px;
        border-radius: 6px;
        cursor: pointer;
    }
    .clear-button {
        text-decoration: none;
        background: #e5e7eb;
        color: #374151;
        padding: 10px 16px;
        border-radius: 6px;
    }

    /* SWITCHING (only the active tab is shown) */
    .tab-panel { display: none; }
    .tab-panel.is-active { display: block; }

    /* REPORT BLOCK */
    .report-block {
        background: white;
        border-radius: 10px;
        padding: 23px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
    }
    .report-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .report-heading h3 { margin: 0; font-size: 19px; }
    .report-heading p { margin: 5px 0 0; font-size: 12px; color: #6b7280; }
    .report-actions { display: flex; gap: 8px; }
    .export-button {
        text-decoration: none;
        background: #2563eb;
        color: white;
        padding: 9px 13px;
        border-radius: 6px;
        font-size: 12px;
    }
    .print-button {
        border: none;
        background: #059669;
        color: white;
        padding: 9px 13px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
    }

    /* TABLE */
    .table-container { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th {
        background: #111827;
        color: white;
        text-align: left;
        padding: 11px;
        font-size: 12px;
        white-space: nowrap;
    }
    td {
        padding: 11px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 12px;
        white-space: nowrap;
    }
    tr:hover td { background: #f9fafb; }

    /* STATUS */
    .status {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: bold;
    }
    .pending { background: #fef3c7; color: #92400e; }
    .claimed { background: #d1fae5; color: #065f46; }
    .voided  { background: #fee2e2; color: #991b1b; }
    .shortage { color: #dc2626; font-weight: bold; }
    .excess   { color: #d97706; font-weight: bold; }
    .matched  { color: #059669; font-weight: bold; }
    .empty { text-align: center; padding: 30px; color: #6b7280; }

    /* RESPONSIVE */
    @media (max-width: 1000px) {
        .filter-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 650px) {
        .filter-grid { grid-template-columns: 1fr; }
        .report-tabs { flex-direction: column; }
        .report-heading {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
    }

    /* PRINT (prints only the report currently shown) */
    @media print {
        .report-tabs, .control-panel, .report-actions { display: none !important; }
        body { background: white; }
        .page { width: 100%; margin: 0; }
        .report-block { box-shadow: none; border: 1px solid #ddd; }
    }
</style>
@endpush

    <main class="page">

        <section class="intro">
            <h2>Management Reports</h2>
            <p>
                Choose a report, set your filters, and review the results.
            </p>
        </section>


        {{-- ========================================================= --}}
        {{-- REPORT SWITCHER --}}
        {{-- ========================================================= --}}

        <nav class="report-tabs">

            <button
                type="button"
                class="tab-button {{ $activeTab === 'transactions' ? 'is-active' : '' }}"
                data-tab="transactions"
            >
                Transaction Report
            </button>

            <button
                type="button"
                class="tab-button {{ $activeTab === 'audits' ? 'is-active' : '' }}"
                data-tab="audits"
            >
                Inventory Audit Report
            </button>

        </nav>


        {{-- ========================================================= --}}
        {{-- FILTERS (fields change depending on the selected report) --}}
        {{-- ========================================================= --}}

        <section class="control-panel">

            <form method="GET" action="{{ route('reports.index') }}">

                <input type="hidden" name="report" id="report-input" value="{{ $activeTab }}">

                {{-- Transaction filters --}}
                <div
                    class="filter-grid tab-panel {{ $activeTab === 'transactions' ? 'is-active' : '' }}"
                    data-panel="transactions"
                >

                    <div class="filter-group">
                        <label>From</label>
                        <input type="date" name="transaction_from" value="{{ request('transaction_from') }}">
                    </div>

                    <div class="filter-group">
                        <label>To</label>
                        <input type="date" name="transaction_to" value="{{ request('transaction_to') }}">
                    </div>

                    <div class="filter-group">
                        <label>Status</label>
                        <select name="transaction_status">
                            <option value="">All Statuses</option>
                            @foreach (['Pending', 'Claimed', 'Voided'] as $status)
                                <option
                                    value="{{ $status }}"
                                    {{ request('transaction_status') === $status ? 'selected' : '' }}
                                >{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Service Type</label>
                        <select name="service_type">
                            <option value="">All Services</option>
                            @foreach ($serviceTypes as $serviceType)
                                <option
                                    value="{{ $serviceType }}"
                                    {{ request('service_type') === $serviceType ? 'selected' : '' }}
                                >{{ $serviceType }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>


                {{-- Audit filters --}}
                <div
                    class="filter-grid tab-panel {{ $activeTab === 'audits' ? 'is-active' : '' }}"
                    data-panel="audits"
                >

                    <div class="filter-group">
                        <label>From</label>
                        <input type="date" name="audit_from" value="{{ request('audit_from') }}">
                    </div>

                    <div class="filter-group">
                        <label>To</label>
                        <input type="date" name="audit_to" value="{{ request('audit_to') }}">
                    </div>

                    <div class="filter-group">
                        <label>Auditor</label>
                        <select name="auditor">
                            <option value="">All Auditors</option>
                            @foreach ($auditors as $auditor)
                                <option
                                    value="{{ $auditor->id }}"
                                    {{ request('auditor') == $auditor->id ? 'selected' : '' }}
                                >{{ $auditor->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Product</label>
                        <select name="audit_product">
                            <option value="">All Products</option>
                            @foreach ($products as $product)
                                <option
                                    value="{{ $product->id }}"
                                    {{ request('audit_product') == $product->id ? 'selected' : '' }}
                                >{{ $product->product_name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>


                <div class="filter-actions">
                    <button type="submit" class="apply-button">Apply Filters</button>
                    <a href="{{ route('reports.index', ['report' => $activeTab]) }}" class="clear-button" id="clear-filters">
                        Clear Filters
                    </a>
                </div>

            </form>

        </section>


        {{-- ========================================================= --}}
        {{-- TRANSACTION REPORT --}}
        {{-- ========================================================= --}}

        <section
            class="report-block tab-panel {{ $activeTab === 'transactions' ? 'is-active' : '' }}"
            data-panel="transactions"
        >

            <div class="report-heading">
                <div>
                    <h3>Transaction Report</h3>
                    <p>Consolidated transaction records for branch management review.</p>
                </div>

                <div class="report-actions">
                    <a
                        href="{{ route('reports.transactions.export', request()->except('report')) }}"
                        class="export-button"
                    >Export Transactions</a>

                    <button type="button" class="print-button" onclick="window.print()">Print</button>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Control Number</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Recorded By</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->control_number }}</td>
                                <td>{{ $transaction->customer_name }}</td>
                                <td>{{ $transaction->service_type }}</td>
                                <td>{{ $transaction->transaction_date?->format('M d, Y') }}</td>
                                <td>{{ $transaction->item_description }}</td>
                                <td>
                                    <span class="status {{ strtolower($transaction->status) }}">
                                        {{ $transaction->status }}
                                    </span>
                                </td>
                                <td>{{ $transaction->creator?->name ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty">No transaction records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </section>


        {{-- ========================================================= --}}
        {{-- INVENTORY AUDIT REPORT --}}
        {{-- ========================================================= --}}

        <section
            class="report-block tab-panel {{ $activeTab === 'audits' ? 'is-active' : '' }}"
            data-panel="audits"
        >

            <div class="report-heading">
                <div>
                    <h3>Inventory Audit Report</h3>
                    <p>Consolidated inventory discrepancies for branch management review.</p>
                </div>

                <div class="report-actions">
                    <a
                        href="{{ route('reports.audits.export', request()->except('report')) }}"
                        class="export-button"
                    >Export Audit Report</a>

                    <button type="button" class="print-button" onclick="window.print()">Print</button>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Audit</th>
                            <th>Date</th>
                            <th>Auditor</th>
                            <th>Status</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Recorded</th>
                            <th>Counted</th>
                            <th>Difference</th>
                            <th>Result</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($audits as $audit)
                            @foreach ($audit->details as $detail)
                                <tr>
                                    <td>#{{ $audit->id }}</td>
                                    <td>{{ $audit->audit_date?->format('M d, Y') }}</td>
                                    <td>{{ $audit->user?->name ?? 'N/A' }}</td>
                                    <td>{{ $audit->status }}</td>
                                    <td>{{ $detail->product?->product_name ?? 'N/A' }}</td>
                                    <td>{{ $detail->product?->sku ?? 'N/A' }}</td>
                                    <td>{{ $detail->recorded_qty }}</td>
                                    <td>{{ $detail->counted_qty }}</td>
                                    <td>{{ $detail->discrepancy }}</td>
                                    <td>
                                        @if ($detail->discrepancy < 0)
                                            <span class="shortage">Shortage</span>
                                        @elseif ($detail->discrepancy > 0)
                                            <span class="excess">Excess</span>
                                        @else
                                            <span class="matched">Matched</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="10" class="empty">No inventory audit records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </section>

    </main>


    @push('scripts')
    <script>
        (function () {
            var buttons = document.querySelectorAll('.tab-button');
            var panels  = document.querySelectorAll('.tab-panel');
            var input   = document.getElementById('report-input');
            var clear   = document.getElementById('clear-filters');
            var base    = "{{ route('reports.index') }}";

            function show(tab) {
                buttons.forEach(function (b) {
                    b.classList.toggle('is-active', b.dataset.tab === tab);
                });
                panels.forEach(function (p) {
                    p.classList.toggle('is-active', p.dataset.panel === tab);
                });
                input.value = tab;
                clear.href = base + '?report=' + tab;
            }

            buttons.forEach(function (b) {
                b.addEventListener('click', function () { show(b.dataset.tab); });
            });
        })();
    </script>
    @endpush

</x-manager-layout>