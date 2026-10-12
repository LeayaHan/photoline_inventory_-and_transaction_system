<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Reports</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            color: #222;
        }

        nav {
            background: #222;
            padding: 15px 30px;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
        }

        .container {
            width: 94%;
            max-width: 1400px;
            margin: 30px auto;
        }

        .page-header,
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .page-header {
            margin-bottom: 25px;
        }

        h1,
        h2 {
            margin: 0;
        }

        h2 {
            margin-bottom: 18px;
        }

        .report-section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
        }

        .filters {
            display: grid;
            grid-template-columns:
                repeat(auto-fit, minmax(180px, 1fr));

            gap: 12px;

            margin: 18px 0;

            padding: 15px;

            background: #f8f8f8;

            border: 1px solid #ddd;

            border-radius: 6px;
        }

        label {
            display: block;

            font-size: 13px;

            font-weight: bold;

            margin-bottom: 6px;
        }

        input,
        select,
        button,
        .button {

            width: 100%;

            padding: 9px 11px;

            border: 1px solid #ccc;

            border-radius: 4px;

            font: inherit;
        }

        button,
        .button {

            background: #222;

            color: white;

            border: none;

            cursor: pointer;

            text-decoration: none;

            display: inline-block;

            text-align: center;
        }

        .button.secondary {
            background: #666;
        }

        .button.export {
            background: #287a3e;
        }

        .filter-actions {

            display: flex;

            align-items: end;

            gap: 8px;
        }

        .filter-actions > * {
            flex: 1;
        }

        .cards {

            display: grid;

            grid-template-columns:
                repeat(auto-fit, minmax(150px, 1fr));

            gap: 12px;

            margin-bottom: 20px;
        }

        .card {

            border: 1px solid #ddd;

            border-radius: 6px;

            padding: 16px;

            background: #fff;
        }

        .card .number {

            font-size: 25px;

            font-weight: bold;

            margin-bottom: 5px;
        }

        .card .label {

            color: #666;

            font-size: 13px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {

            width: 100%;

            border-collapse: collapse;
        }

        th,
        td {

            border: 1px solid #ddd;

            padding: 10px;

            text-align: left;

            white-space: nowrap;
        }

        th {
            background: #eee;
        }

        .shortage {

            color: #b42318;

            font-weight: bold;
        }

        .excess {

            color: #8a5a00;

            font-weight: bold;
        }

        .matched {

            color: #247a3d;

            font-weight: bold;
        }

        .empty {

            text-align: center;

            padding: 25px;

            color: #666;
        }

        .section-actions {

            display: flex;

            gap: 8px;

            min-width: 260px;
        }

        .section-actions .button {

            width: auto;

            flex: 1;
        }


        /*
        |--------------------------------------------------------------------------
        | PRINT
        |--------------------------------------------------------------------------
        */

        @media print {

            nav,
            .filters,
            .section-actions,
            .page-header,
            .no-print {

                display: none !important;
            }

            body {
                background: white;
            }

            .container {

                width: 100%;

                max-width: none;

                margin: 0;
            }

            .report-section {

                box-shadow: none;

                page-break-inside: avoid;
            }

            .report-section:not(:first-of-type) {

                page-break-before: always;
            }
        }

    </style>

</head>


<body>


<nav>

    <a href="{{ route('dashboard') }}">
        Dashboard
    </a>

    <a href="{{ route('transactions.index') }}">
        Transactions
    </a>

    <a href="{{ route('audits.index') }}">
        Audits
    </a>

    <a href="{{ route('reports.index') }}">
        Reports
    </a>

</nav>


<div class="container">


    <div class="page-header">

        <div>

            <h1>
                Reports
            </h1>

            <p>
                Transaction and inventory audit reports
            </p>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- TRANSACTION REPORT --}}
    {{-- ========================================================= --}}


    <section class="report-section">


        <div class="section-header">

            <h2>
                Customer Service Transaction Report
            </h2>


            <div class="section-actions">

                <a
                    class="button export"

                    href="{{ route(
                        'reports.transactions.export',
                        request()->only([
                            'transaction_from',
                            'transaction_to',
                            'transaction_status',
                            'service_type'
                        ])
                    ) }}"
                >
                    Export CSV
                </a>


                <button
                    type="button"
                    onclick="window.print()"
                >
                    Print
                </button>

            </div>

        </div>



        {{-- TRANSACTION FILTERS --}}

        <form
            method="GET"
            action="{{ route('reports.index') }}"
            class="filters"
        >


            <div>

                <label for="transaction_from">
                    From Date
                </label>

                <input
                    type="date"
                    id="transaction_from"
                    name="transaction_from"
                    value="{{ request('transaction_from') }}"
                >

            </div>



            <div>

                <label for="transaction_to">
                    To Date
                </label>

                <input
                    type="date"
                    id="transaction_to"
                    name="transaction_to"
                    value="{{ request('transaction_to') }}"
                >

            </div>



            <div>

                <label for="transaction_status">
                    Status
                </label>

                <select
                    id="transaction_status"
                    name="transaction_status"
                >

                    <option value="">
                        All Statuses
                    </option>

                    <option
                        value="Pending"
                        @selected(
                            request('transaction_status')
                            === 'Pending'
                        )
                    >
                        Pending
                    </option>

                    <option
                        value="Claimed"
                        @selected(
                            request('transaction_status')
                            === 'Claimed'
                        )
                    >
                        Claimed
                    </option>

                    <option
                        value="Voided"
                        @selected(
                            request('transaction_status')
                            === 'Voided'
                        )
                    >
                        Voided
                    </option>

                </select>

            </div>



            <div>

                <label for="service_type">
                    Service Type
                </label>

                <select
                    id="service_type"
                    name="service_type"
                >

                    <option value="">
                        All Services
                    </option>


                    @foreach($serviceTypes as $serviceType)

                        <option
                            value="{{ $serviceType }}"
                            @selected(
                                request('service_type')
                                === $serviceType
                            )
                        >

                            {{ $serviceType }}

                        </option>

                    @endforeach

                </select>

            </div>



            <div class="filter-actions">

                <button type="submit">
                    Apply Filters
                </button>

                <a
                    class="button secondary"
                    href="{{ route('reports.index') }}"
                >
                    Clear
                </a>

            </div>


        </form>



        {{-- TRANSACTION SUMMARY --}}


        <div class="cards">


            <div class="card">

                <div class="number">
                    {{ $transactionSummary['total'] }}
                </div>

                <div class="label">
                    Total Transactions
                </div>

            </div>



            <div class="card">

                <div class="number">
                    {{ $transactionSummary['pending'] }}
                </div>

                <div class="label">
                    Pending
                </div>

            </div>



            <div class="card">

                <div class="number">
                    {{ $transactionSummary['claimed'] }}
                </div>

                <div class="label">
                    Claimed
                </div>

            </div>



            <div class="card">

                <div class="number">
                    {{ $transactionSummary['voided'] }}
                </div>

                <div class="label">
                    Voided
                </div>

            </div>


        </div>



        {{-- TRANSACTION TABLE --}}


        <div class="table-wrap">

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
                            Item Description
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Created By
                        </th>

                    </tr>

                </thead>


                <tbody>


                    @forelse(
                        $transactions
                        as $transaction
                    )

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
                                {{ $transaction
                                    ->transaction_date
                                    ?->format('Y-m-d') }}
                            </td>

                            <td>
                                {{ $transaction->item_description ?: '—' }}
                            </td>

                            <td>
                                {{ $transaction->status }}
                            </td>

                            <td>
                                {{ $transaction->creator?->name ?: '—' }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="empty"
                            >
                                No transaction records found
                                for the selected filters.
                            </td>

                        </tr>

                    @endforelse


                </tbody>

            </table>

        </div>


    </section>



    {{-- ========================================================= --}}
    {{-- INVENTORY AUDIT REPORT --}}
    {{-- ========================================================= --}}


    <section class="report-section">


        <div class="section-header">

            <h2>
                Inventory Audit Report
            </h2>


            <div class="section-actions">

                <a
                    class="button export"

                    href="{{ route(
                        'reports.audits.export',
                        request()->only([
                            'audit_from',
                            'audit_to',
                            'auditor',
                            'audit_product'
                        ])
                    ) }}"
                >
                    Export CSV
                </a>


                <button
                    type="button"
                    onclick="window.print()"
                >
                    Print
                </button>

            </div>

        </div>



        {{-- AUDIT FILTERS --}}


        <form
            method="GET"
            action="{{ route('reports.index') }}"
            class="filters"
        >


            <div>

                <label for="audit_from">
                    From Date
                </label>

                <input
                    type="date"
                    id="audit_from"
                    name="audit_from"
                    value="{{ request('audit_from') }}"
                >

            </div>



            <div>

                <label for="audit_to">
                    To Date
                </label>

                <input
                    type="date"
                    id="audit_to"
                    name="audit_to"
                    value="{{ request('audit_to') }}"
                >

            </div>



            <div>

                <label for="auditor">
                    Auditor
                </label>

                <select
                    id="auditor"
                    name="auditor"
                >

                    <option value="">
                        All Auditors
                    </option>


                    @foreach($auditors as $auditor)

                        <option
                            value="{{ $auditor->id }}"

                            @selected(
                                (string) request('auditor')
                                ===
                                (string) $auditor->id
                            )
                        >

                            {{ $auditor->name }}

                        </option>

                    @endforeach

                </select>

            </div>



            <div>

                <label for="audit_product">
                    Product
                </label>

                <select
                    id="audit_product"
                    name="audit_product"
                >

                    <option value="">
                        All Products
                    </option>


                    @foreach($products as $product)

                        <option
                            value="{{ $product->id }}"

                            @selected(
                                (string) request('audit_product')
                                ===
                                (string) $product->id
                            )
                        >

                            {{ $product->product_name }}

                        </option>

                    @endforeach

                </select>

            </div>



            <div class="filter-actions">

                <button type="submit">
                    Apply Filters
                </button>

                <a
                    class="button secondary"
                    href="{{ route('reports.index') }}"
                >
                    Clear
                </a>

            </div>


        </form>



        {{-- AUDIT SUMMARY --}}


        <div class="cards">


            <div class="card">

                <div class="number">
                    {{ $auditSummary['audits'] }}
                </div>

                <div class="label">
                    Total Audits
                </div>

            </div>



            <div class="card">

                <div class="number">
                    {{ $auditSummary['items'] }}
                </div>

                <div class="label">
                    Items Audited
                </div>

            </div>



            <div class="card">

                <div class="number">
                    {{ $auditSummary['shortage'] }}
                </div>

                <div class="label">
                    Shortages
                </div>

            </div>



            <div class="card">

                <div class="number">
                    {{ $auditSummary['excess'] }}
                </div>

                <div class="label">
                    Excesses
                </div>

            </div>



            <div class="card">

                <div class="number">
                    {{ $auditSummary['matched'] }}
                </div>

                <div class="label">
                    Matched
                </div>

            </div>


        </div>



        {{-- AUDIT TABLE --}}


        <div class="table-wrap">

            <table>

                <thead>

                    <tr>

                        <th>
                            Audit ID
                        </th>

                        <th>
                            Audit Date
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
                            Discrepancy
                        </th>

                        <th>
                            Result
                        </th>

                    </tr>

                </thead>


                <tbody>


                    @php
                        $hasAuditDetails = false;
                    @endphp



                    @foreach($audits as $audit)


                        @foreach($audit->details as $detail)


                            @php

                                $hasAuditDetails = true;

                                $result =
                                    $detail->discrepancy < 0
                                        ? 'Shortage'
                                        : (
                                            $detail->discrepancy > 0
                                                ? 'Excess'
                                                : 'Matched'
                                        );

                            @endphp


                            <tr>

                                <td>
                                    {{ $audit->id }}
                                </td>

                                <td>
                                    {{ $audit
                                        ->audit_date
                                        ?->format('Y-m-d') }}
                                </td>

                                <td>
                                    {{ $audit->user?->name ?: '—' }}
                                </td>

                                <td>
                                    {{ $audit->status }}
                                </td>

                                <td>
                                    {{ $detail
                                        ->product
                                        ?->product_name ?: '—' }}
                                </td>

                                <td>
                                    {{ $detail
                                        ->product
                                        ?->sku ?: '—' }}
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

                                <td
                                    class="{{ strtolower($result) }}"
                                >
                                    {{ $result }}
                                </td>

                            </tr>


                        @endforeach


                    @endforeach



                    @if(!$hasAuditDetails)

                        <tr>

                            <td
                                colspan="10"
                                class="empty"
                            >
                                No audit records found
                                for the selected filters.
                            </td>

                        </tr>

                    @endif


                </tbody>

            </table>

        </div>


    </section>


</div>


</body>

</html>