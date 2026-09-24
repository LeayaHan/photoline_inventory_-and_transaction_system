<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Audit #{{ $audit->id }} - Manager</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f6f8;
            color: #1f2937;
        }

        .navbar {
            background: #111827;
            color: white;
            padding: 18px 30px;

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            margin: 0;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 14px;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }

        .user-area {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .user-area span {
            font-size: 14px;
        }

        .logout-button {
            background: #dc2626;
            color: white;
            border: none;
            padding: 9px 14px;
            border-radius: 6px;
            cursor: pointer;
        }

        .container {
            width: 92%;
            max-width: 1200px;
            margin: 35px auto;
        }

        .page-header {
            margin-bottom: 25px;
        }

        .page-header h1 {
            margin: 0 0 8px;
            font-size: 30px;
        }

        .page-header p {
            margin: 0;
            color: #6b7280;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            margin-bottom: 25px;
        }

        .card h2 {
            margin-top: 0;
            margin-bottom: 20px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .info-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
        }

        .info-label {
            display: block;
            color: #6b7280;
            font-size: 12px;
            margin-bottom: 6px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .info-value {
            font-size: 16px;
            font-weight: bold;
        }

        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-completed {
            background: #dcfce7;
            color: #166534;
        }

        .status-ongoing {
            background: #fef3c7;
            color: #92400e;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f3f4f6;
            text-align: left;
            padding: 13px;
            border-bottom: 1px solid #d1d5db;
            font-size: 13px;
        }

        td {
            padding: 13px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }

        .difference-none {
            color: #059669;
            font-weight: bold;
        }

        .difference-shortage {
            color: #dc2626;
            font-weight: bold;
        }

        .difference-excess {
            color: #d97706;
            font-weight: bold;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .summary-box {
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
        }

        .summary-number {
            display: block;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .summary-label {
            color: #6b7280;
            font-size: 13px;
        }

        .summary-shortage .summary-number {
            color: #dc2626;
        }

        .summary-excess .summary-number {
            color: #d97706;
        }

        .summary-none .summary-number {
            color: #059669;
        }

        .button {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }

        .button-gray {
            background: #374151;
            color: white;
        }

        .button-gray:hover {
            background: #1f2937;
        }

        .back-area {
            margin-top: 25px;
        }

        @media (max-width: 800px) {

            .navbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .nav-links {
                flex-wrap: wrap;
            }

            .user-area {
                width: 100%;
                justify-content: space-between;
            }

            .info-grid,
            .summary-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="navbar">

    <h2>Photoline Abreeza</h2>

    <div class="nav-links">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('manager.transactions.index') }}">Transactions</a>
        <a href="{{ route('manager.audits.index') }}">Inventory Audits</a>
        <a href="{{ route('reports.index') }}">Reports</a>
    </div>

    <div class="user-area">

        <span>
            {{ auth()->user()->name }} — Branch Manager
        </span>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button
                type="submit"
                class="logout-button"
            >
                Logout
            </button>
        </form>

    </div>

</div>

<div class="container">

    <div class="page-header">

        <h1>
            Inventory Audit #{{ $audit->id }}
        </h1>

        <p>
            Detailed inventory audit review
        </p>

    </div>

    <div class="card">

        <h2>Audit Information</h2>

        <div class="info-grid">

            <div class="info-box">

                <span class="info-label">
                    Audit ID
                </span>

                <span class="info-value">
                    #{{ $audit->id }}
                </span>

            </div>

            <div class="info-box">

                <span class="info-label">
                    Audit Date
                </span>

                <span class="info-value">
                    {{ \Carbon\Carbon::parse($audit->audit_date)->format('F d, Y') }}
                </span>

            </div>

            <div class="info-box">

                <span class="info-label">
                    Auditor
                </span>

                <span class="info-value">
                    {{ $audit->user->name ?? 'N/A' }}
                </span>

            </div>

            <div class="info-box">

                <span class="info-label">
                    Status
                </span>

                <span class="info-value">

                    @if($audit->status === 'Completed')

                        <span class="status status-completed">
                            Completed
                        </span>

                    @else

                        <span class="status status-ongoing">
                            Ongoing
                        </span>

                    @endif

                </span>

            </div>

            <div class="info-box">

                <span class="info-label">
                    Total Items
                </span>

                <span class="info-value">
                    {{ $audit->details->count() }}
                </span>

            </div>

        </div>

    </div>

    @php
        $shortageCount = $audit->details
            ->where('discrepancy', '<', 0)
            ->count();

        $excessCount = $audit->details
            ->where('discrepancy', '>', 0)
            ->count();

        $noDifferenceCount = $audit->details
            ->where('discrepancy', '=', 0)
            ->count();
    @endphp

    <div class="card">

        <h2>Discrepancy Summary</h2>

        <div class="summary-grid">

            <div class="summary-box summary-shortage">

                <span class="summary-number">
                    {{ $shortageCount }}
                </span>

                <span class="summary-label">
                    Shortage Items
                </span>

            </div>

            <div class="summary-box summary-excess">

                <span class="summary-number">
                    {{ $excessCount }}
                </span>

                <span class="summary-label">
                    Excess Items
                </span>

            </div>

            <div class="summary-box summary-none">

                <span class="summary-number">
                    {{ $noDifferenceCount }}
                </span>

                <span class="summary-label">
                    No Difference
                </span>

            </div>

        </div>

    </div>

    <div class="card">

        <h2>Inventory Count Details</h2>

        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>
                        <th>SKU</th>
                        <th>Product</th>
                        <th>POS Quantity</th>
                        <th>Physical Count</th>
                        <th>Discrepancy</th>
                        <th>Finding</th>
                    </tr>

                </thead>

                <tbody>

                @forelse($audit->details as $detail)

                    <tr>

                        <td>
                            {{ $detail->product->sku ?? 'N/A' }}
                        </td>

                        <td>
                            {{ $detail->product->product_name ?? 'N/A' }}
                        </td>

                        <td>
                            {{ $detail->recorded_qty }}
                        </td>

                        <td>
                            {{ $detail->counted_qty }}
                        </td>

                        <td>

                            @if($detail->discrepancy < 0)

                                <span class="difference-shortage">
                                    {{ $detail->discrepancy }}
                                </span>

                            @elseif($detail->discrepancy > 0)

                                <span class="difference-excess">
                                    +{{ $detail->discrepancy }}
                                </span>

                            @else

                                <span class="difference-none">
                                    0
                                </span>

                            @endif

                        </td>

                        <td>

                            @if($detail->discrepancy < 0)

                                <span class="difference-shortage">
                                    Shortage
                                </span>

                            @elseif($detail->discrepancy > 0)

                                <span class="difference-excess">
                                    Excess
                                </span>

                            @else

                                <span class="difference-none">
                                    No Difference
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            style="text-align:center; padding:30px; color:#6b7280;"
                        >
                            No audit details found.
                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <div class="back-area">

        <a
            href="{{ route('manager.audits.index') }}"
            class="button button-gray"
        >
            ← Back to Audit Records
        </a>

    </div>

</div>

</body>
</html>