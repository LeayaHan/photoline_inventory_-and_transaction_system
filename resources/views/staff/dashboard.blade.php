<x-staff-layout title="Staff Dashboard | Photoline">

@push('styles')
<style>
    .staff-dashboard {
        width: min(1180px, calc(100% - 36px));
        margin: 0 auto;
        padding: 34px 0 55px;
    }

    .staff-hero {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 25px;
        padding: 28px 30px;
        border-radius: 18px;
        color: #fff;
        background: linear-gradient(135deg, #0b47b7, #1769e8);
        box-shadow: 0 15px 35px rgba(23, 105, 232, .18);
    }

    .staff-hero small {
        color: #dceaff;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.3px;
        text-transform: uppercase;
    }

    .staff-hero h1 {
        margin: 7px 0 0;
        font-size: clamp(26px, 4vw, 36px);
        letter-spacing: -.8px;
    }

    .staff-hero p {
        margin: 8px 0 0;
        max-width: 580px;
        color: #dceaff;
        font-size: 14px;
        line-height: 1.6;
    }

    .staff-hero-actions {
        display: flex;
        gap: 9px;
        flex-shrink: 0;
    }

    .staff-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 15px;
        border-radius: 9px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
    }

    .staff-action.primary {
        color: #1769e8;
        background: #fff;
    }

    .staff-action.secondary {
        color: #fff;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.18);
    }

    .staff-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-top: 18px;
    }

    .staff-stat {
        padding: 20px;
        border: 1px solid #e5eaf2;
        border-radius: 14px;
        background: #fff;
    }

    .staff-stat span {
        color: #7b8799;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .7px;
    }

    .staff-stat strong {
        display: block;
        margin-top: 7px;
        color: #172033;
        font-size: 27px;
    }

    .staff-stat.pending strong {
        color: #1769e8;
    }

    .staff-stat.audit strong {
        color: #ed2b24;
    }

    .staff-table-card {
        margin-top: 18px;
        overflow: hidden;
        border: 1px solid #e5eaf2;
        border-radius: 14px;
        background: #fff;
    }

    .staff-card-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 22px;
        border-bottom: 1px solid #edf0f5;
    }

    .staff-card-heading h2 {
        margin: 0;
        font-size: 17px;
    }

    .staff-card-heading a {
        color: #1769e8;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
    }

    .staff-table-wrap {
        overflow-x: auto;
    }

    .staff-table {
        width: 100%;
        border-collapse: collapse;
    }

    .staff-table th,
    .staff-table td {
        padding: 14px 18px;
        border-bottom: 1px solid #edf0f5;
        text-align: left;
        font-size: 12px;
    }

    .staff-table th {
        color: #7b8799;
        background: #fafbfd;
        font-size: 10px;
        letter-spacing: .7px;
        text-transform: uppercase;
    }

    .staff-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .status {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
    }

    .status.pending {
        color: #1769e8;
        background: #eaf2ff;
    }

    .status.claimed {
        color: #20754a;
        background: #eaf8f0;
    }

    .status.voided {
        color: #b52a25;
        background: #fff0ef;
    }

    @media (max-width: 850px) {
        .staff-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .staff-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 520px) {
        .staff-dashboard {
            width: min(100% - 24px, 1180px);
            padding-top: 20px;
        }

        .staff-hero {
            padding: 23px;
        }

        .staff-hero-actions {
            width: 100%;
        }

        .staff-action {
            flex: 1;
        }

        .staff-stats {
            grid-template-columns: 1fr 1fr;
            gap: 9px;
        }
    }
</style>
@endpush

<div class="staff-dashboard">
    <section class="staff-hero">
        <div>
            <small>Staff Workspace</small>
            <h1>Good day, {{ auth()->user()->name }}.</h1>
            <p>
                Handle customer transactions, maintain inventory items, and complete
                inventory audits from one workspace.
            </p>
        </div>

        <div class="staff-hero-actions">
            <a href="{{ route('transactions.create') }}" class="staff-action primary">
                + New Transaction
            </a>
            <a href="{{ route('audits.create') }}" class="staff-action secondary">
                Start Audit
            </a>
        </div>
    </section>

    <section class="staff-stats">
        <div class="staff-stat">
            <span>Today's Transactions</span>
            <strong>{{ $todayTransactions }}</strong>
        </div>

        <div class="staff-stat pending">
            <span>Pending</span>
            <strong>{{ $pendingTransactions }}</strong>
        </div>

        <div class="staff-stat">
            <span>Inventory Items</span>
            <strong>{{ $productCount }}</strong>
        </div>

        <div class="staff-stat audit">
            <span>Ongoing Audits</span>
            <strong>{{ $ongoingAudits }}</strong>
        </div>
    </section>

    <section class="staff-table-card">
        <div class="staff-card-heading">
            <h2>Recent Transactions</h2>
            <a href="{{ route('transactions.index') }}">View all</a>
        </div>

        <div class="staff-table-wrap">
            <table class="staff-table">
                <thead>
                    <tr>
                        <th>Control Number</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->control_number }}</td>
                            <td>{{ $transaction->customer_name }}</td>
                            <td>{{ $transaction->service_type }}</td>
                            <td>{{ $transaction->transaction_date->format('M d, Y') }}</td>
                            <td>
                                <span class="status {{ strtolower($transaction->status) }}">
                                    {{ $transaction->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:30px;">
                                No transactions yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

</x-staff-layout>
