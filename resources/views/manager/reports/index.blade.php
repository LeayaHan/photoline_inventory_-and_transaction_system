@extends('layouts.panel')

@section('title', 'Reports')

@push('styles')
<style>
        

        .page {

            width: 92%;

            max-width: 1400px;

            margin: 35px auto;

        }
        .intro {

            margin-bottom: 25px;

        }
        .intro h2 {

            margin: 0 0 6px;

            font-size: 28px;

        }
        .intro p {

            margin: 0;

            color: #6b7280;

            font-size: 14px;

        }
        

        .overview {

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 16px;

            margin-bottom: 25px;

        }
        .card {

            background: white;

            border-radius: 10px;

            padding: 22px;

            box-shadow:
                0 2px 8px
                rgba(0, 0, 0, 0.07);

        }
        .card-label {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 10px;

        }
        .card-number {

            font-size: 30px;

            font-weight: bold;

        }
        .card-note {

            margin-top: 7px;

            font-size: 12px;

            color: #9ca3af;

        }
        

        .management-grid {

            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 20px;

            margin-bottom: 25px;

        }
        .management-card {

            background: white;

            border-radius: 10px;

            padding: 23px;

            box-shadow:
                0 2px 8px
                rgba(0, 0, 0, 0.07);

        }
        .management-card h3 {

            margin: 0;

            font-size: 18px;

        }
        .management-card p {

            color: #6b7280;

            font-size: 13px;

            margin: 6px 0 20px;

        }
        .metric-row {

            display: flex;

            justify-content: space-between;

            align-items: center;

            padding: 13px 0;

            border-bottom:
                1px solid #e5e7eb;

        }
        .metric-row:last-child {

            border-bottom: none;

        }
        .metric-name {

            font-size: 13px;

            color: #4b5563;

        }
        .metric-value {

            font-size: 18px;

            font-weight: bold;

        }
        

        .control-panel {

            background: white;

            border-radius: 10px;

            padding: 23px;

            margin-bottom: 25px;

            box-shadow:
                0 2px 8px
                rgba(0, 0, 0, 0.07);

        }
        .control-panel h3 {

            margin: 0;

            font-size: 18px;

        }
        .control-panel p {

            color: #6b7280;

            font-size: 13px;

            margin: 5px 0 20px;

        }
        .filter-grid {

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

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

            border:
                1px solid #d1d5db;

            border-radius: 6px;

            background: white;

        }
        .filter-actions {

            display: flex;

            gap: 10px;

            margin-top: 17px;

        }
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
        

        .report-block {

            background: white;

            border-radius: 10px;

            padding: 23px;

            margin-bottom: 25px;

            box-shadow:
                0 2px 8px
                rgba(0, 0, 0, 0.07);

        }
        .report-heading {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 20px;

        }
        .report-heading h3 {

            margin: 0;

            font-size: 19px;

        }
        .report-heading p {

            margin: 5px 0 0;

            font-size: 12px;

            color: #6b7280;

        }
        .report-actions {

            display: flex;

            gap: 8px;

        }
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
        

        .table-container {

            overflow-x: auto;

        }
        table {

            width: 100%;

            border-collapse: collapse;

        }
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

            border-bottom:
                1px solid #e5e7eb;

            font-size: 12px;

            white-space: nowrap;

        }
        tr:hover td {

            background: #f9fafb;

        }
        

        .status {

            display: inline-block;

            padding: 4px 8px;

            border-radius: 20px;

            font-size: 10px;

            font-weight: bold;

        }
        .pending {

            background: #fef3c7;

            color: #92400e;

        }
        .claimed {

            background: #d1fae5;

            color: #065f46;

        }
        .voided {

            background: #fee2e2;

            color: #991b1b;

        }
        .shortage {

            color: #dc2626;

            font-weight: bold;

        }
        .excess {

            color: #d97706;

            font-weight: bold;

        }
        .matched {

            color: #059669;

            font-weight: bold;

        }
        .empty {

            text-align: center;

            padding: 30px;

            color: #6b7280;

        }
        

        @media (max-width: 1000px) {.overview {

                grid-template-columns:
                    repeat(2, 1fr);

            }
            .management-grid {

                grid-template-columns: 1fr;

            }
            .filter-grid {

                grid-template-columns:
                    repeat(2, 1fr);

            }}
        @media (max-width: 650px) {.overview {

                grid-template-columns: 1fr;

            }
            .filter-grid {

                grid-template-columns: 1fr;

            }
            .report-heading {

                flex-direction: column;

                align-items: flex-start;

                gap: 15px;

            }}
        

        @media print {.header,

            .control-panel,

            .report-actions {

                display: none !important;

            }
            .page {

                width: 100%;

                margin: 0;

            }
            .card,

            .management-card,

            .report-block {

                box-shadow: none;

                border:
                    1px solid #ddd;

            }}
    
</style>
@endpush

@section('content')
    

    



    <main class="page">


        {{-- ============================================================= --}}
        {{-- INTRO --}}
        {{-- ============================================================= --}}

        <section class="intro">

            <h2>
                Management Reports
            </h2>

            <p>
                Monitor branch transactions and inventory audit
                results through consolidated management reports.
            </p>

        </section>



        {{-- ============================================================= --}}
        {{-- TRANSACTION OVERVIEW --}}
        {{-- ============================================================= --}}

        <section class="overview">


            <div class="card">

                <div class="card-label">
                    Total Transactions
                </div>

                <div class="card-number">
                    {{ $transactionSummary['total'] }}
                </div>

                <div class="card-note">
                    Recorded transactions
                </div>

            </div>


            <div class="card">

                <div class="card-label">
                    Pending
                </div>

                <div class="card-number">
                    {{ $transactionSummary['pending'] }}
                </div>

                <div class="card-note">
                    Awaiting completion
                </div>

            </div>


            <div class="card">

                <div class="card-label">
                    Claimed
                </div>

                <div class="card-number">
                    {{ $transactionSummary['claimed'] }}
                </div>

                <div class="card-note">
                    Completed transactions
                </div>

            </div>


            <div class="card">

                <div class="card-label">
                    Voided
                </div>

                <div class="card-number">
                    {{ $transactionSummary['voided'] }}
                </div>

                <div class="card-note">
                    Voided transactions
                </div>

            </div>


            <div class="card">

                <div class="card-label">
                    Inventory Audits
                </div>

                <div class="card-number">
                    {{ $auditSummary['audits'] }}
                </div>

                <div class="card-note">
                    Completed audit records
                </div>

            </div>


            <div class="card">

                <div class="card-label">
                    Shortages
                </div>

                <div class="card-number">
                    {{ $auditSummary['shortage'] }}
                </div>

                <div class="card-note">
                    Inventory discrepancies
                </div>

            </div>


            <div class="card">

                <div class="card-label">
                    Excess
                </div>

                <div class="card-number">
                    {{ $auditSummary['excess'] }}
                </div>

                <div class="card-note">
                    Inventory discrepancies
                </div>

            </div>


            <div class="card">

                <div class="card-label">
                    Matched
                </div>

                <div class="card-number">
                    {{ $auditSummary['matched'] }}
                </div>

                <div class="card-note">
                    Matching inventory counts
                </div>

            </div>


        </section>



        {{-- ============================================================= --}}
        {{-- MANAGEMENT SUMMARY --}}
        {{-- ============================================================= --}}

        <section class="management-grid">


            <div class="management-card">

                <h3>
                    Transaction Status
                </h3>

                <p>
                    Current distribution of recorded transactions.
                </p>


                <div class="metric-row">

                    <span class="metric-name">
                        Total Transactions
                    </span>

                    <span class="metric-value">
                        {{ $transactionSummary['total'] }}
                    </span>

                </div>


                <div class="metric-row">

                    <span class="metric-name">
                        Pending
                    </span>

                    <span class="metric-value">
                        {{ $transactionSummary['pending'] }}
                    </span>

                </div>


                <div class="metric-row">

                    <span class="metric-name">
                        Claimed
                    </span>

                    <span class="metric-value">
                        {{ $transactionSummary['claimed'] }}
                    </span>

                </div>


                <div class="metric-row">

                    <span class="metric-name">
                        Voided
                    </span>

                    <span class="metric-value">
                        {{ $transactionSummary['voided'] }}
                    </span>

                </div>

            </div>



            <div class="management-card">

                <h3>
                    Inventory Audit Status
                </h3>

                <p>
                    Summary of physical inventory audit findings.
                </p>


                <div class="metric-row">

                    <span class="metric-name">
                        Audits Conducted
                    </span>

                    <span class="metric-value">
                        {{ $auditSummary['audits'] }}
                    </span>

                </div>


                <div class="metric-row">

                    <span class="metric-name">
                        Items Checked
                    </span>

                    <span class="metric-value">
                        {{ $auditSummary['items'] }}
                    </span>

                </div>


                <div class="metric-row">

                    <span class="metric-name">
                        Shortages
                    </span>

                    <span class="metric-value">
                        {{ $auditSummary['shortage'] }}
                    </span>

                </div>


                <div class="metric-row">

                    <span class="metric-name">
                        Excess
                    </span>

                    <span class="metric-value">
                        {{ $auditSummary['excess'] }}
                    </span>

                </div>


                <div class="metric-row">

                    <span class="metric-name">
                        Matched
                    </span>

                    <span class="metric-value">
                        {{ $auditSummary['matched'] }}
                    </span>

                </div>

            </div>


        </section>



        {{-- ============================================================= --}}
        {{-- REPORT FILTERS --}}
        {{-- ============================================================= --}}

        <section class="control-panel">

            <h3>
                Report Filters
            </h3>

            <p>
                Filter the management reports by transaction and
                inventory audit criteria.
            </p>


            <form
                method="GET"
                action="{{ route('reports.index') }}"
            >


                <div class="filter-grid">


                    {{-- Transaction From --}}

                    <div class="filter-group">

                        <label>
                            Transaction From
                        </label>

                        <input
                            type="date"
                            name="transaction_from"
                            value="{{ request('transaction_from') }}"
                        >

                    </div>


                    {{-- Transaction To --}}

                    <div class="filter-group">

                        <label>
                            Transaction To
                        </label>

                        <input
                            type="date"
                            name="transaction_to"
                            value="{{ request('transaction_to') }}"
                        >

                    </div>


                    {{-- Transaction Status --}}

                    <div class="filter-group">

                        <label>
                            Transaction Status
                        </label>

                        <select
                            name="transaction_status"
                        >

                            <option value="">
                                All Statuses
                            </option>

                            <option
                                value="Pending"
                                {{ request('transaction_status') === 'Pending' ? 'selected' : '' }}
                            >
                                Pending
                            </option>

                            <option
                                value="Claimed"
                                {{ request('transaction_status') === 'Claimed' ? 'selected' : '' }}
                            >
                                Claimed
                            </option>

                            <option
                                value="Voided"
                                {{ request('transaction_status') === 'Voided' ? 'selected' : '' }}
                            >
                                Voided
                            </option>

                        </select>

                    </div>


                    {{-- Service Type --}}

                    <div class="filter-group">

                        <label>
                            Service Type
                        </label>

                        <select
                            name="service_type"
                        >

                            <option value="">
                                All Services
                            </option>

                            @foreach ($serviceTypes as $serviceType)

                                <option
                                    value="{{ $serviceType }}"
                                    {{ request('service_type') === $serviceType ? 'selected' : '' }}
                                >
                                    {{ $serviceType }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Audit From --}}

                    <div class="filter-group">

                        <label>
                            Audit From
                        </label>

                        <input
                            type="date"
                            name="audit_from"
                            value="{{ request('audit_from') }}"
                        >

                    </div>


                    {{-- Audit To --}}

                    <div class="filter-group">

                        <label>
                            Audit To
                        </label>

                        <input
                            type="date"
                            name="audit_to"
                            value="{{ request('audit_to') }}"
                        >

                    </div>


                    {{-- Auditor --}}

                    <div class="filter-group">

                        <label>
                            Auditor
                        </label>

                        <select
                            name="auditor"
                        >

                            <option value="">
                                All Auditors
                            </option>

                            @foreach ($auditors as $auditor)

                                <option
                                    value="{{ $auditor->id }}"
                                    {{ request('auditor') == $auditor->id ? 'selected' : '' }}
                                >
                                    {{ $auditor->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Product --}}

                    <div class="filter-group">

                        <label>
                            Product
                        </label>

                        <select
                            name="audit_product"
                        >

                            <option value="">
                                All Products
                            </option>

                            @foreach ($products as $product)

                                <option
                                    value="{{ $product->id }}"
                                    {{ request('audit_product') == $product->id ? 'selected' : '' }}
                                >
                                    {{ $product->product_name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                </div>


                <div class="filter-actions">

                    <button
                        type="submit"
                        class="apply-button"
                    >
                        Apply Filters
                    </button>


                    <a
                        href="{{ route('reports.index') }}"
                        class="clear-button"
                    >
                        Clear Filters
                    </a>

                </div>


            </form>

        </section>



        {{-- ============================================================= --}}
        {{-- TRANSACTION REPORT --}}
        {{-- ============================================================= --}}

        <section class="report-block">


            <div class="report-heading">

                <div>

                    <h3>
                        Transaction Report
                    </h3>

                    <p>
                        Consolidated transaction records for
                        branch management review.
                    </p>

                </div>


                <div class="report-actions">

                    <a
                        href="{{ route('reports.transactions.export', request()->query()) }}"
                        class="export-button"
                    >
                        Export Transactions
                    </a>


                    <button
                        type="button"
                        class="print-button"
                        onclick="window.print()"
                    >
                        Print
                    </button>

                </div>

            </div>



            <div class="table-container">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Control Number
                            </th>

                            <th>
                                Customer
                            </th>

                            <th>
                                Service
                            </th>

                            <th>
                                Date
                            </th>

                            <th>
                                Description
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Recorded By
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($transactions as $transaction)

                            <tr>

                                <td>
                                    {{ $transaction->control_number }}
                                </td>

                                <td>
                                    {{ $transaction->customer_name }}
                                </td>

                                <td>
                                    {{ $transaction->service_type }}
                                </td>

                                <td>
                                    {{ $transaction->transaction_date?->format('M d, Y') }}
                                </td>

                                <td>
                                    {{ $transaction->item_description }}
                                </td>

                                <td>

                                    <span
                                        class="status {{ strtolower($transaction->status) }}"
                                    >
                                        {{ $transaction->status }}
                                    </span>

                                </td>

                                <td>
                                    {{ $transaction->creator?->name ?? 'N/A' }}
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="7"
                                    class="empty"
                                >
                                    No transaction records found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>



        {{-- ============================================================= --}}
        {{-- INVENTORY AUDIT REPORT --}}
        {{-- ============================================================= --}}

        <section class="report-block">


            <div class="report-heading">

                <div>

                    <h3>
                        Inventory Audit Report
                    </h3>

                    <p>
                        Consolidated inventory discrepancies for
                        branch management review.
                    </p>

                </div>


                <div class="report-actions">

                    <a
                        href="{{ route('reports.audits.export', request()->query()) }}"
                        class="export-button"
                    >
                        Export Audit Report
                    </a>


                    <button
                        type="button"
                        class="print-button"
                        onclick="window.print()"
                    >
                        Print
                    </button>

                </div>

            </div>



            <div class="table-container">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Audit
                            </th>

                            <th>
                                Date
                            </th>

                            <th>
                                Auditor
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Product
                            </th>

                            <th>
                                SKU
                            </th>

                            <th>
                                Recorded
                            </th>

                            <th>
                                Counted
                            </th>

                            <th>
                                Difference
                            </th>

                            <th>
                                Result
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($audits as $audit)

                            @foreach ($audit->details as $detail)

                                <tr>

                                    <td>
                                        #{{ $audit->id }}
                                    </td>

                                    <td>
                                        {{ $audit->audit_date?->format('M d, Y') }}
                                    </td>

                                    <td>
                                        {{ $audit->user?->name ?? 'N/A' }}
                                    </td>

                                    <td>
                                        {{ $audit->status }}
                                    </td>

                                    <td>
                                        {{ $detail->product?->product_name ?? 'N/A' }}
                                    </td>

                                    <td>
                                        {{ $detail->product?->sku ?? 'N/A' }}
                                    </td>

                                    <td>
                                        {{ $detail->recorded_qty }}
                                    </td>

                                    <td>
                                        {{ $detail->counted_qty }}
                                    </td>

                                    <td>
                                        {{ $detail->discrepancy }}
                                    </td>

                                    <td>

                                        @if ($detail->discrepancy < 0)

                                            <span class="shortage">
                                                Shortage
                                            </span>

                                        @elseif ($detail->discrepancy > 0)

                                            <span class="excess">
                                                Excess
                                            </span>

                                        @else

                                            <span class="matched">
                                                Matched
                                            </span>

                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        @empty

                            <tr>

                                <td
                                    colspan="10"
                                    class="empty"
                                >
                                    No inventory audit records found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>


    </main>
@endsection
