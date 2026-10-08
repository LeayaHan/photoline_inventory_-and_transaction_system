<x-manager-layout title="Manager Dashboard - Photoline Abreeza">

@push('styles')
<style>
    .dashboard {
        width: 90%;
        max-width: 1200px;
        margin: 35px auto;
    }

    .dashboard-header {
        margin-bottom: 25px;
    }

    .dashboard-header h1 {
        margin: 0 0 6px;
        font-size: 28px;
        color: #111827;
    }

    .dashboard-header p {
        margin: 0;
        color: #6b7280;
    }

    .summary {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 22px;
    }

    .card {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
    }

    .card-label {
        color: #6b7280;
        font-size: 14px;
        margin-bottom: 8px;
    }

    .card-value {
        color: #111827;
        font-size: 28px;
        font-weight: 700;
    }

    .content {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 20px;
    }

    .panel {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
    }

    .panel h2 {
        margin: 0 0 15px;
        font-size: 18px;
        color: #111827;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 10px 8px;
        text-align: left;
        border-bottom: 1px solid #eee;
        font-size: 13px;
    }

    th {
        color: #6b7280;
        font-size: 12px;
    }

    td {
        color: #374151;
    }

    .status {
        font-size: 12px;
        font-weight: 600;
    }

    .empty {
        color: #9ca3af;
        padding: 20px 0;
        text-align: center;
    }

    .link {
        display: inline-block;
        margin-top: 15px;
        color: #2563eb;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
    }

    @media (max-width: 900px) {
        .summary {
            grid-template-columns: repeat(2, 1fr);
        }

        .content {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .summary {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush


<div class="dashboard">

    <div class="dashboard-header">
        <h1>Manager Dashboard</h1>
        <p>Branch overview and operational status for Photoline Abreeza</p>
    </div>


    <div class="summary">

        <div class="card">
            <div class="card-label">Today's Transactions</div>
            <div class="card-value">{{ $todayTransactions }}</div>
        </div>

        <div class="card">
            <div class="card-label">Pending Transactions</div>
            <div class="card-value">{{ $pendingTransactions }}</div>
        </div>

        <div class="card">
            <div class="card-label">Inventory Audits</div>
            <div class="card-value">{{ $totalAudits }}</div>
        </div>

        <div class="card">
            <div class="card-label">Discrepancies</div>
            <div class="card-value">{{ $discrepancies }}</div>
        </div>

    </div>


    <div class="content">

        <div class="panel">

            <h2>Recent Transactions</h2>

            @if($recentTransactions->count())

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($recentTransactions as $transaction)
                            <tr>
                                <td>#{{ $transaction->id }}</td>

                                <td>
                                    {{ $transaction->customer_name ?? 'N/A' }}
                                </td>

                                <td class="status">
                                    {{ $transaction->status ?? 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            @else

                <div class="empty">
                    No transactions recorded yet.
                </div>

            @endif

            <a
                href="{{ route('manager.transactions.index') }}"
                class="link"
            >
                View Transactions →
            </a>

        </div>


        <div class="panel">

            <h2>Branch Overview</h2>

            <table>
                <tbody>

                    <tr>
                        <td>Products</td>
                        <td>{{ $productCount }}</td>
                    </tr>

                    <tr>
                        <td>Pending Transactions</td>
                        <td>{{ $pendingTransactions }}</td>
                    </tr>

                    <tr>
                        <td>Inventory Audits</td>
                        <td>{{ $totalAudits }}</td>
                    </tr>

                    <tr>
                        <td>Discrepancies</td>
                        <td>{{ $discrepancies }}</td>
                    </tr>

                </tbody>
            </table>

            <a
                href="{{ route('reports.index') }}"
                class="link"
            >
                View Reports →
            </a>

        </div>


        <div class="panel">

            <h2>Recent Inventory Audits</h2>

            @if($recentAudits->count())

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($recentAudits as $audit)
                            <tr>
                                <td>#{{ $audit->id }}</td>

                                <td class="status">
                                    {{ $audit->status ?? 'N/A' }}
                                </td>

                                <td>
                                    {{ $audit->created_at?->format('M d, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            @else

                <div class="empty">
                    No inventory audits recorded yet.
                </div>

            @endif

            <a
                href="{{ route('manager.audits.index') }}"
                class="link"
            >
                View Audits →
            </a>

        </div>


        <div class="panel">

            <h2>Reports</h2>

            <p style="color:#6b7280; line-height:1.5;">
                Review transaction and inventory audit information
                for the branch.
            </p>

            <a
                href="{{ route('reports.index') }}"
                class="link"
            >
                Open Reports →
            </a>

        </div>

    </div>

</div>

</x-manager-layout>