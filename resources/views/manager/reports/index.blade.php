@extends('layouts.panel')

@section('title', 'Management Reports')

@push('styles')
<style>
    /* The page now inherits width, centering and spacing from .container,
       the same as the Audits page. */
    .reports-page {
        box-sizing: border-box;
    }

    .reports-page .page-header {
        display: block !important;
        margin-bottom: 28px;
    }

    /* No font-size here, so the title and subtitle inherit the same
       sizes as the Audits page. */
    .reports-page .page-header h1 {
        margin: 0 0 8px;
    }

    .reports-page .page-header p {
        margin: 0;
        color: #6b7280;
    }

    /* FILTERS */

    .filter-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        margin-bottom: 20px;
    }

    .filter-title {
        margin: 0 0 5px;
        font-size: 17px;
    }

    .filter-description {
        margin: 0 0 18px;
        color: #6b7280;
        font-size: 13px;
    }

    .filters {
        display: grid;
        grid-template-columns: 1.25fr 1fr 1fr 1fr;
        gap: 14px;
    }

    .filter-group label {
        display: block;
        margin-bottom: 6px;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
    }

    .filter-group input,
    .filter-group select {
        width: 100%;
        height: 38px;
        padding: 0 10px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: #fff;
        color: #1f2937;
        font-size: 13px;
        outline: none;
        box-sizing: border-box;
    }

    .filter-group input:focus,
    .filter-group select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, .08);
    }

    .filter-actions {
        display: flex;
        gap: 9px;
        margin-top: 16px;
    }

    .btn {
        border: none;
        border-radius: 6px;
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-primary {
        background: #2563eb;
        color: #fff;
    }

    .btn-primary:hover {
        background: #1d4ed8;
    }

    .btn-secondary {
        background: #e5e7eb;
        color: #374151;
    }

    .btn-secondary:hover {
        background: #d1d5db;
    }

    .btn-print {
        background: #374151;
        color: #fff;
    }

    .btn-print:hover {
        background: #1f2937;
    }

    /* REPORT TABS */

    .report-tabs {
        display: flex;
        gap: 2px;
        overflow-x: auto;
        background: #fff;
        border-radius: 10px 10px 0 0;
        padding: 0 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
    }

    .report-tab {
        flex-shrink: 0;
        border: none;
        border-bottom: 3px solid transparent;
        background: transparent;
        padding: 15px 17px;
        color: #6b7280;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .report-tab:hover {
        color: #2563eb;
    }

    .report-tab.active {
        color: #2563eb;
        border-bottom-color: #2563eb;
    }

    /* REPORT */

    .report-card {
        background: #fff;
        border-radius: 0 0 10px 10px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
        margin-bottom: 30px;
    }

    .report-content {
        display: none;
    }

    .report-content.active {
        display: block;
    }

    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 17px;
    }

    .report-title h2 {
        margin: 0 0 5px;
        font-size: 19px;
    }

    .report-title p {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
    }

    .report-actions {
        flex-shrink: 0;
    }

    .record-count {
        color: #6b7280;
        font-size: 12px;
    }

    /* TABLE */

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e5e7eb;
        border-radius: 7px;
    }

    .reports-page table {
        width: 100%;
        min-width: 850px;
        border-collapse: collapse;
    }

    .reports-page th {
        padding: 12px 11px;
        background: #f3f4f6;
        color: #1f2937;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
        border-bottom: 1px solid #e5e7eb;
    }

    .reports-page td {
        padding: 12px 11px;
        color: #374151;
        font-size: 13px;
        white-space: nowrap;
        border-bottom: 1px solid #edf0f4;
    }

    .reports-page tbody tr:hover {
        background: #f9fafb;
    }

    .reports-page tbody tr:last-child td {
        border-bottom: none;
    }

    /* STATUS */

    .badge {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .badge-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-claimed,
    .badge-completed,
    .badge-approved,
    .badge-matched {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-voided,
    .badge-shortage,
    .badge-rejected {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-excess {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-ongoing {
        background: #dbeafe;
        color: #1d4ed8;
    }

    /* FOOTER */

    .table-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin-top: 15px;
    }

    .pagination-buttons {
        display: flex;
        gap: 4px;
    }

    .page-btn {
        min-width: 30px;
        height: 30px;
        border: 1px solid #d1d5db;
        border-radius: 5px;
        background: #fff;
        color: #4b5563;
        cursor: pointer;
    }

    .page-btn.active {
        background: #2563eb;
        border-color: #2563eb;
        color: #fff;
    }

    /* RESPONSIVE */

    @media (max-width: 1050px) {
        .filters {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 700px) {
        .filters {
            grid-template-columns: 1fr;
        }

        .report-header {
            flex-direction: column;
        }

        .filter-actions {
            flex-wrap: wrap;
        }

        .table-footer {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    /* PRINT */

    @media print {
        body {
            background: #fff !important;
        }

        .filter-card,
        .report-tabs,
        .report-actions,
        .table-footer {
            display: none !important;
        }

        .reports-page,
        .report-card {
            width: 100%;
            padding: 0;
            margin: 0;
            box-shadow: none;
        }

        .report-content {
            display: none !important;
        }

        .report-content.active {
            display: block !important;
        }

        .table-wrapper {
            border: none;
        }

        .reports-page table {
            min-width: 100%;
        }
    }
</style>
@endpush

@section('content')

<div class="container reports-page">

    <div class="page-header">
        <h1>Management Reports</h1>
        <p>
            Monitor branch transactions and inventory records through consolidated management reports.
        </p>
    </div>

    {{-- REPORT FILTERS --}}

    <section class="filter-card">

        <h2 class="filter-title">
            Report Filters
        </h2>

        <p class="filter-description">
            Select a report and apply filters to view the required records.
        </p>

        <div class="filters">

            <div class="filter-group">
                <label for="reportType">Report Type</label>

                <select id="reportType">
                    <option value="transactions">Transaction Report</option>
                    <option value="audit">Inventory Audit Report</option>
                    <option value="services">Services Summary</option>
                    <option value="stockin">Stock-In Report</option>
                    <option value="replenishment">Replenishment Report</option>
                    <option value="income">Income Summary</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="dateFrom">Date From</label>
                <input type="date" id="dateFrom">
            </div>

            <div class="filter-group">
                <label for="dateTo">Date To</label>
                <input type="date" id="dateTo">
            </div>

            <div class="filter-group">
                <label for="statusFilter">Status</label>

                <select id="statusFilter">
                    <option value="">All Statuses</option>
                    <option>Pending</option>
                    <option>Claimed</option>
                    <option>Voided</option>
                    <option>Completed</option>
                    <option>Ongoing</option>
                    <option>Approved</option>
                    <option>Rejected</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="serviceFilter">Service Type</label>

                <select id="serviceFilter">
                    <option value="">All Services</option>
                    <option>Photo Printing</option>
                    <option>Reprint</option>
                    <option>ID Picture</option>
                    <option>Pictorial</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="staffFilter">Staff / Auditor</label>

                <select id="staffFilter">
                    <option value="">All Staff</option>
                    <option>Jeric</option>
                    <option>Mark</option>
                    <option>Staff 3</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="productFilter">Product</label>

                <select id="productFilter">
                    <option value="">All Products</option>
                    <option>Camera Battery</option>
                    <option>Digital Camera</option>
                    <option>Photo Album</option>
                    <option>Photo Frame</option>
                    <option>SD Card</option>
                    <option>Photo Paper</option>
                    <option>Ink Cartridge</option>
                </select>
            </div>

        </div>

        <div class="filter-actions">
            <button type="button" class="btn btn-primary" onclick="applyFilters()">
                Apply Filters
            </button>

            <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                Clear Filters
            </button>
        </div>

    </section>

    {{-- REPORT TABS --}}

    <div class="report-tabs">

        <button type="button"
                class="report-tab active"
                onclick="showReport('transactions', this)">
            Transactions
        </button>

        <button type="button"
                class="report-tab"
                onclick="showReport('audit', this)">
            Inventory Audit
        </button>

        <button type="button"
                class="report-tab"
                onclick="showReport('services', this)">
            Services
        </button>

        <button type="button"
                class="report-tab"
                onclick="showReport('stockin', this)">
            Stock-In
        </button>

        <button type="button"
                class="report-tab"
                onclick="showReport('replenishment', this)">
            Replenishment
        </button>

        <button type="button"
                class="report-tab"
                onclick="showReport('income', this)">
            Income
        </button>

    </div>

    <section class="report-card">

        {{-- TRANSACTION REPORT --}}

        <div id="transactions" class="report-content active">

            <div class="report-header">

                <div class="report-title">
                    <h2>Transaction Report</h2>
                    <p>
                        Consolidated customer service transaction records based on the selected filters.
                    </p>
                </div>

                <div class="report-actions">
                    <button type="button"
                            class="btn btn-print"
                            onclick="window.print()">
                        Print
                    </button>
                </div>

            </div>

            <div style="margin-bottom:12px;">
                <span class="record-count">24 transaction records found</span>
            </div>

            <div class="table-wrapper">

                <table>
                    <thead>
                        <tr>
                            <th>CONTROL NUMBER</th>
                            <th>CUSTOMER</th>
                            <th>SERVICE</th>
                            <th>DATE</th>
                            <th>DESCRIPTION</th>
                            <th>STATUS</th>
                            <th>RECORDED BY</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td>TRX-85QMDHQ</td>
                            <td>Michael Jordan</td>
                            <td>Picture</td>
                            <td>Sep 28, 2026</td>
                            <td>2x2 ID Picture</td>
                            <td><span class="badge badge-pending">Pending</span></td>
                            <td>Jeric #234</td>
                        </tr>

                        <tr>
                            <td>TRX-PSH8JAA</td>
                            <td>Robert</td>
                            <td>Photo Printing</td>
                            <td>Sep 28, 2026</td>
                            <td>4R Photo</td>
                            <td><span class="badge badge-claimed">Claimed</span></td>
                            <td>Jeric</td>
                        </tr>

                        <tr>
                            <td>TRX-91KDA22</td>
                            <td>Maria Santos</td>
                            <td>Reprint</td>
                            <td>Sep 27, 2026</td>
                            <td>Old Photo Reprint</td>
                            <td><span class="badge badge-voided">Voided</span></td>
                            <td>Mark</td>
                        </tr>

                        <tr>
                            <td>TRX-42KDM92</td>
                            <td>John Dela Cruz</td>
                            <td>Pictorial</td>
                            <td>Sep 26, 2026</td>
                            <td>Studio Pictorial</td>
                            <td><span class="badge badge-claimed">Claimed</span></td>
                            <td>Jeric</td>
                        </tr>

                    </tbody>
                </table>

            </div>

            <div class="table-footer">
                <span class="record-count">Showing 1–10 of 24 records</span>

                <div class="pagination-buttons">
                    <button class="page-btn">‹</button>
                    <button class="page-btn active">1</button>
                    <button class="page-btn">2</button>
                    <button class="page-btn">3</button>
                    <button class="page-btn">›</button>
                </div>
            </div>

        </div>

        {{-- INVENTORY AUDIT REPORT --}}

        <div id="audit" class="report-content">

            <div class="report-header">

                <div class="report-title">
                    <h2>Inventory Audit Report</h2>
                    <p>
                        Consolidated physical inventory audit records for branch management review.
                    </p>
                </div>

                <div class="report-actions">
                    <button type="button"
                            class="btn btn-print"
                            onclick="window.print()">
                        Print
                    </button>
                </div>

            </div>

            <div style="margin-bottom:12px;">
                <span class="record-count">42 audit records found</span>
            </div>

            <div class="table-wrapper">

                <table>
                    <thead>
                        <tr>
                            <th>AUDIT</th>
                            <th>DATE</th>
                            <th>AUDITOR</th>
                            <th>STATUS</th>
                            <th>PRODUCT</th>
                            <th>SKU</th>
                            <th>RECORDED</th>
                            <th>COUNTED</th>
                            <th>DIFFERENCE</th>
                            <th>RESULT</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td>A-1</td>
                            <td>Sep 28, 2026</td>
                            <td>Jeric #234</td>
                            <td><span class="badge badge-completed">Completed</span></td>
                            <td>Camera Battery</td>
                            <td>BAT-001</td>
                            <td>5</td>
                            <td>4</td>
                            <td>-1</td>
                            <td><span class="badge badge-shortage">Shortage</span></td>
                        </tr>

                        <tr>
                            <td>A-1</td>
                            <td>Sep 28, 2026</td>
                            <td>Jeric #234</td>
                            <td><span class="badge badge-completed">Completed</span></td>
                            <td>Digital Camera</td>
                            <td>CAM-001</td>
                            <td>6</td>
                            <td>8</td>
                            <td>+2</td>
                            <td><span class="badge badge-excess">Excess</span></td>
                        </tr>

                        <tr>
                            <td>A-1</td>
                            <td>Sep 28, 2026</td>
                            <td>Jeric #234</td>
                            <td><span class="badge badge-completed">Completed</span></td>
                            <td>Photo Album</td>
                            <td>ALB-001</td>
                            <td>7</td>
                            <td>7</td>
                            <td>0</td>
                            <td><span class="badge badge-matched">Matched</span></td>
                        </tr>

                        <tr>
                            <td>A-1</td>
                            <td>Sep 28, 2026</td>
                            <td>Jeric #234</td>
                            <td><span class="badge badge-completed">Completed</span></td>
                            <td>Photo Frame</td>
                            <td>FRM-001</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td><span class="badge badge-matched">Matched</span></td>
                        </tr>

                        <tr>
                            <td>A-2</td>
                            <td>Sep 29, 2026</td>
                            <td>Mark</td>
                            <td><span class="badge badge-ongoing">Ongoing</span></td>
                            <td>SD Card 64GB</td>
                            <td>SD-001</td>
                            <td>10</td>
                            <td>8</td>
                            <td>-2</td>
                            <td><span class="badge badge-shortage">Shortage</span></td>
                        </tr>

                    </tbody>
                </table>

            </div>

            <div class="table-footer">
                <span class="record-count">Showing 1–10 of 42 records</span>

                <div class="pagination-buttons">
                    <button class="page-btn">‹</button>
                    <button class="page-btn active">1</button>
                    <button class="page-btn">2</button>
                    <button class="page-btn">3</button>
                    <button class="page-btn">4</button>
                    <button class="page-btn">›</button>
                </div>
            </div>

        </div>

        {{-- SERVICES SUMMARY --}}

        <div id="services" class="report-content">

            <div class="report-header">

                <div class="report-title">
                    <h2>Services Summary</h2>
                    <p>
                        Summary of services recorded by the branch.
                    </p>
                </div>

                <div class="report-actions">
                    <button type="button"
                            class="btn btn-print"
                            onclick="window.print()">
                        Print
                    </button>
                </div>

            </div>

            <div class="table-wrapper">

                <table>
                    <thead>
                        <tr>
                            <th>SERVICE TYPE</th>
                            <th>TOTAL TRANSACTIONS</th>
                            <th>COMPLETED</th>
                            <th>PENDING</th>
                            <th>VOIDED</th>
                            <th>TOTAL AMOUNT</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td>Photo Printing</td>
                            <td>35</td>
                            <td>31</td>
                            <td>3</td>
                            <td>1</td>
                            <td>₱4,250.00</td>
                        </tr>

                        <tr>
                            <td>Reprint</td>
                            <td>18</td>
                            <td>16</td>
                            <td>2</td>
                            <td>0</td>
                            <td>₱2,100.00</td>
                        </tr>

                        <tr>
                            <td>ID Picture</td>
                            <td>42</td>
                            <td>40</td>
                            <td>2</td>
                            <td>0</td>
                            <td>₱3,360.00</td>
                        </tr>

                        <tr>
                            <td>Pictorial</td>
                            <td>9</td>
                            <td>9</td>
                            <td>0</td>
                            <td>0</td>
                            <td>₱5,400.00</td>
                        </tr>

                    </tbody>
                </table>

            </div>

        </div>

        {{-- STOCK-IN REPORT --}}

        <div id="stockin" class="report-content">

            <div class="report-header">

                <div class="report-title">
                    <h2>Stock-In Report</h2>
                    <p>
                        Records of merchandise and raw material stock-ins.
                    </p>
                </div>

                <div class="report-actions">
                    <button type="button"
                            class="btn btn-print"
                            onclick="window.print()">
                        Print
                    </button>
                </div>

            </div>

            <div class="table-wrapper">

                <table>
                    <thead>
                        <tr>
                            <th>STOCK-IN ID</th>
                            <th>DATE</th>
                            <th>PRODUCT</th>
                            <th>CATEGORY</th>
                            <th>QUANTITY</th>
                            <th>RECORDED BY</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td>SI-001</td>
                            <td>Sep 28, 2026</td>
                            <td>Photo Paper</td>
                            <td>Raw Material</td>
                            <td>100</td>
                            <td>Jeric</td>
                            <td><span class="badge badge-completed">Completed</span></td>
                        </tr>

                        <tr>
                            <td>SI-002</td>
                            <td>Sep 27, 2026</td>
                            <td>SD Card 64GB</td>
                            <td>Merchandise</td>
                            <td>20</td>
                            <td>Mark</td>
                            <td><span class="badge badge-completed">Completed</span></td>
                        </tr>

                        <tr>
                            <td>SI-003</td>
                            <td>Sep 26, 2026</td>
                            <td>Ink Cartridge</td>
                            <td>Raw Material</td>
                            <td>25</td>
                            <td>Jeric</td>
                            <td><span class="badge badge-completed">Completed</span></td>
                        </tr>

                    </tbody>
                </table>

            </div>

        </div>

        {{-- REPLENISHMENT REPORT --}}

        <div id="replenishment" class="report-content">

            <div class="report-header">

                <div class="report-title">
                    <h2>Replenishment Report</h2>
                    <p>
                        Records of inventory replenishment requests and approvals.
                    </p>
                </div>

                <div class="report-actions">
                    <button type="button"
                            class="btn btn-print"
                            onclick="window.print()">
                        Print
                    </button>
                </div>

            </div>

            <div class="table-wrapper">

                <table>
                    <thead>
                        <tr>
                            <th>REQUEST ID</th>
                            <th>DATE</th>
                            <th>REQUESTED BY</th>
                            <th>PRODUCT</th>
                            <th>CURRENT STOCK</th>
                            <th>REQUESTED QTY</th>
                            <th>STATUS</th>
                            <th>APPROVED BY</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td>REP-001</td>
                            <td>Sep 28, 2026</td>
                            <td>Jeric</td>
                            <td>Photo Paper</td>
                            <td>15</td>
                            <td>100</td>
                            <td><span class="badge badge-approved">Approved</span></td>
                            <td>Branch Manager</td>
                        </tr>

                        <tr>
                            <td>REP-002</td>
                            <td>Sep 29, 2026</td>
                            <td>Mark</td>
                            <td>SD Card 64GB</td>
                            <td>4</td>
                            <td>20</td>
                            <td><span class="badge badge-pending">Pending</span></td>
                            <td>—</td>
                        </tr>

                        <tr>
                            <td>REP-003</td>
                            <td>Sep 25, 2026</td>
                            <td>Jeric</td>
                            <td>Ink Cartridge</td>
                            <td>3</td>
                            <td>25</td>
                            <td><span class="badge badge-approved">Approved</span></td>
                            <td>Branch Manager</td>
                        </tr>

                    </tbody>
                </table>

            </div>

        </div>

        {{-- INCOME SUMMARY --}}

        <div id="income" class="report-content">

            <div class="report-header">

                <div class="report-title">
                    <h2>Income Summary</h2>
                    <p>
                        Summary of recorded transaction income.
                    </p>
                </div>

                <div class="report-actions">
                    <button type="button"
                            class="btn btn-print"
                            onclick="window.print()">
                        Print
                    </button>
                </div>

            </div>

            <div class="table-wrapper">

                <table>
                    <thead>
                        <tr>
                            <th>DATE</th>
                            <th>TRANSACTIONS</th>
                            <th>COMPLETED</th>
                            <th>VOIDED</th>
                            <th>TOTAL SALES</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td>Sep 28, 2026</td>
                            <td>24</td>
                            <td>22</td>
                            <td>2</td>
                            <td>₱3,850.00</td>
                        </tr>

                        <tr>
                            <td>Sep 27, 2026</td>
                            <td>31</td>
                            <td>30</td>
                            <td>1</td>
                            <td>₱4,420.00</td>
                        </tr>

                        <tr>
                            <td>Sep 26, 2026</td>
                            <td>28</td>
                            <td>26</td>
                            <td>2</td>
                            <td>₱3,940.00</td>
                        </tr>

                        <tr>
                            <td>Sep 25, 2026</td>
                            <td>20</td>
                            <td>19</td>
                            <td>1</td>
                            <td>₱2,900.00</td>
                        </tr>

                    </tbody>
                </table>

            </div>

        </div>

    </section>

</div>

@endsection

@push('scripts')
<script>
    function showReport(reportId, button) {

        document.querySelectorAll('.report-content').forEach(function (report) {
            report.classList.remove('active');
        });

        document.querySelectorAll('.report-tab').forEach(function (tab) {
            tab.classList.remove('active');
        });

        const selectedReport = document.getElementById(reportId);

        if (selectedReport) {
            selectedReport.classList.add('active');
        }

        if (button) {
            button.classList.add('active');
        }

        const reportType = document.getElementById('reportType');

        if (reportType) {
            reportType.value = reportId;
        }
    }

    document.getElementById('reportType').addEventListener('change', function () {

        const reportId = this.value;

        document.querySelectorAll('.report-tab').forEach(function (tab) {

            const onclickValue = tab.getAttribute('onclick') || '';

            if (onclickValue.includes("'" + reportId + "'")) {
                showReport(reportId, tab);
            }

        });
    });

    function applyFilters() {

        const reportName =
            document.getElementById('reportType')
                .selectedOptions[0].text;

        /*
         * Frontend prototype only.
         * These values can later be passed to Laravel
         * for database filtering.
         */

        const filters = {
            report: reportName,
            dateFrom: document.getElementById('dateFrom').value,
            dateTo: document.getElementById('dateTo').value,
            status: document.getElementById('statusFilter').value,
            service: document.getElementById('serviceFilter').value,
            staff: document.getElementById('staffFilter').value,
            product: document.getElementById('productFilter').value
        };

        console.log('Report filters:', filters);
    }

    function clearFilters() {

        document.getElementById('dateFrom').value = '';
        document.getElementById('dateTo').value = '';

        document.getElementById('statusFilter').selectedIndex = 0;
        document.getElementById('serviceFilter').selectedIndex = 0;
        document.getElementById('staffFilter').selectedIndex = 0;
        document.getElementById('productFilter').selectedIndex = 0;

    }
</script>
@endpush