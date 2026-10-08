@php
    $activeTab = in_array(request('report'), ['transactions', 'audits'])
        ? request('report')
        : 'transactions';
@endphp

<x-manager-layout title="Manager Reports | Photoline Abreeza">

@push('styles')
<style>
    :root {
        --photo-navy: #0b2d5c;
        --photo-blue: #1f66d1;
        --photo-red: #c62828;
        --photo-red-soft: #fff1f1;
        --photo-blue-soft: #eef5ff;
        --ink: #172033;
        --muted: #667085;
        --line: #dfe5ee;
        --surface: #ffffff;
        --page: #f7f9fc;
    }

    * { box-sizing: border-box; }

    .page {
        width: min(94%, 1440px);
        margin: 34px auto 50px;
        color: var(--ink);
    }

    .intro {
        background: var(--surface);
        border: 1px solid var(--line);
        border-left: 5px solid var(--photo-red);
        border-radius: 12px;
        padding: 22px 24px;
        margin-bottom: 18px;
        box-shadow: 0 4px 14px rgba(11, 45, 92, .06);
    }

    .intro h2 {
        margin: 0 0 5px;
        color: var(--photo-navy);
        font-size: 28px;
        line-height: 1.2;
        letter-spacing: -.3px;
    }

    .intro p {
        margin: 0;
        color: var(--muted);
        font-size: 14px;
    }

    .report-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 18px;
    }

    .tab-button {
        border: 1px solid #cfd8e6;
        background: #fff;
        color: var(--photo-navy);
        padding: 11px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: .15s ease;
    }

    .tab-button:hover { border-color: var(--photo-blue); color: var(--photo-blue); }

    .tab-button.is-active {
        background: var(--photo-blue);
        border-color: var(--photo-blue);
        color: #fff;
        box-shadow: 0 3px 8px rgba(31, 102, 209, .18);
    }

    .control-panel {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 21px 23px;
        margin-bottom: 20px;
        box-shadow: 0 4px 14px rgba(11, 45, 92, .05);
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .filter-group label {
        display: block;
        margin-bottom: 6px;
        color: var(--photo-navy);
        font-size: 12px;
        font-weight: 700;
    }

    .filter-group input,
    .filter-group select {
        width: 100%;
        min-height: 42px;
        padding: 9px 11px;
        border: 1px solid #cfd8e6;
        border-radius: 7px;
        background: #fff;
        color: var(--ink);
        font: inherit;
        outline: none;
    }

    .filter-group input:focus,
    .filter-group select:focus {
        border-color: var(--photo-blue);
        box-shadow: 0 0 0 3px rgba(31, 102, 209, .10);
    }

    .filter-actions {
        display: flex;
        gap: 9px;
        margin-top: 17px;
        padding-top: 16px;
        border-top: 1px solid #edf0f5;
    }

    .apply-button,
    .clear-button {
        min-height: 40px;
        padding: 9px 16px;
        border-radius: 7px;
        font-size: 13px;
        font-weight: 700;
    }

    .apply-button {
        border: 1px solid var(--photo-blue);
        background: var(--photo-blue);
        color: #fff;
        cursor: pointer;
    }

    .clear-button {
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        border: 1px solid #cfd8e6;
        background: #fff;
        color: var(--photo-navy);
    }

    .tab-panel { display: none; }
    .tab-panel.is-active { display: block; }

    .report-block {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 25px;
        box-shadow: 0 5px 16px rgba(11, 45, 92, .06);
    }

    .report-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        padding-bottom: 17px;
        margin-bottom: 16px;
        border-bottom: 2px solid var(--photo-navy);
    }

    .report-heading h3 {
        margin: 0 0 5px;
        color: var(--photo-navy);
        font-size: 20px;
    }

    .report-heading p {
        margin: 0;
        color: var(--muted);
        font-size: 12px;
    }

    .report-actions { display: flex; gap: 8px; flex-shrink: 0; }

    .export-button,
    .print-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 8px 13px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
    }

    .export-button {
        background: var(--photo-blue);
        border: 1px solid var(--photo-blue);
        color: #fff;
    }

    .print-button {
        background: #fff;
        border: 1px solid var(--photo-red);
        color: var(--photo-red);
    }

    .print-button:hover { background: var(--photo-red-soft); }

    .table-container {
        width: 100%;
        overflow: visible;
    }

    table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
    }

    th {
        background: var(--photo-navy);
        color: #fff;
        text-align: left;
        padding: 11px 10px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .2px;
        border-right: 1px solid rgba(255,255,255,.12);
        white-space: normal;
    }

    th:last-child { border-right: 0; }

    td {
        padding: 11px 10px;
        border-bottom: 1px solid var(--line);
        color: #344054;
        font-size: 12px;
        line-height: 1.35;
        overflow-wrap: anywhere;
        word-break: normal;
        vertical-align: middle;
    }

    tbody tr:nth-child(even) td { background: #fbfcfe; }
    tbody tr:hover td { background: var(--photo-blue-soft); }

    .status {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .pending { background: #fff4d6; color: #8a5a00; }
    .claimed { background: #e7f6ed; color: #167044; }
    .voided  { background: #fde9e9; color: #b42318; }
    .shortage { color: var(--photo-red); font-weight: 800; }
    .excess   { color: #a15c00; font-weight: 800; }
    .matched  { color: #167044; font-weight: 800; }
    .empty { text-align: center; padding: 34px 15px; color: var(--muted); }

    /* Give the wide audit report sensible column proportions without causing a scrollbar. */
    .report-block[data-panel="audits"] th:nth-child(1),
    .report-block[data-panel="audits"] td:nth-child(1) { width: 7%; }
    .report-block[data-panel="audits"] th:nth-child(2),
    .report-block[data-panel="audits"] td:nth-child(2) { width: 10%; }
    .report-block[data-panel="audits"] th:nth-child(3),
    .report-block[data-panel="audits"] td:nth-child(3) { width: 12%; }
    .report-block[data-panel="audits"] th:nth-child(4),
    .report-block[data-panel="audits"] td:nth-child(4) { width: 9%; }
    .report-block[data-panel="audits"] th:nth-child(5),
    .report-block[data-panel="audits"] td:nth-child(5) { width: 17%; }
    .report-block[data-panel="audits"] th:nth-child(6),
    .report-block[data-panel="audits"] td:nth-child(6) { width: 11%; }
    .report-block[data-panel="audits"] th:nth-child(n+7),
    .report-block[data-panel="audits"] td:nth-child(n+7) { width: 8.5%; }

    @media (max-width: 1000px) {
        .filter-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .table-container { overflow-x: auto; }
        table { min-width: 850px; }
    }

    @media (max-width: 650px) {
        .filter-grid { grid-template-columns: 1fr; }
        .report-tabs { flex-direction: column; }
        .report-heading { flex-direction: column; }
        .report-actions { width: 100%; }
        .export-button, .print-button { flex: 1; }
    }

    @media print {
        @page { size: landscape; margin: 12mm; }

        html, body {
            background: #fff !important;
            color: #172033 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .navbar,
        .navbar-staff,
        .navbar-manager,
        .report-tabs,
        .control-panel,
        .report-actions {
            display: none !important;
        }

        .page {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
        }

        .intro {
            border: 0 !important;
            border-left: 5px solid var(--photo-red) !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            padding: 0 0 12px 12px !important;
            margin-bottom: 15px !important;
        }

        .intro h2 { font-size: 22px; }
        .intro p { font-size: 10px; }

        .report-block {
            display: none !important;
            border: 0 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .report-block.is-active { display: block !important; }

        .report-heading {
            border-bottom: 2px solid var(--photo-navy) !important;
            padding-bottom: 9px !important;
            margin-bottom: 10px !important;
        }

        .report-heading h3 { font-size: 16px; }
        .report-heading p { font-size: 9px; }

        .table-container {
            width: 100% !important;
            overflow: visible !important;
        }

        table {
            width: 100% !important;
            min-width: 0 !important;
            table-layout: fixed !important;
            page-break-inside: auto;
        }

        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }

        th {
            padding: 7px 6px !important;
            font-size: 8px !important;
            white-space: normal !important;
        }

        td {
            padding: 7px 6px !important;
            font-size: 8px !important;
            white-space: normal !important;
            overflow-wrap: anywhere !important;
        }

        .status {
            padding: 2px 5px !important;
            font-size: 7px !important;
        }
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