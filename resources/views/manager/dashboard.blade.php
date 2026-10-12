<x-manager-layout title="Manager Dashboard - Photoline Abreeza">

@push('styles')
<style>
    .manager-dashboard {
        width: min(94%, 1240px);
        margin: 30px auto 50px;
        color: #1f2937;
    }

    .manager-hero {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        margin-bottom: 24px;
    }

    .manager-hero h1 {
        margin: 0 0 7px;
        color: #123b70;
        font-size: clamp(27px, 3vw, 34px);
        line-height: 1.1;
        letter-spacing: -.02em;
    }

    .manager-hero p {
        margin: 0;
        color: #6b7280;
        font-size: 14px;
        line-height: 1.6;
    }

    .hero-label {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 10px;
        color: #c62828;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .hero-label::before {
        content: '';
        width: 24px;
        height: 3px;
        border-radius: 99px;
        background: #c62828;
    }

    .hero-date {
        flex: 0 0 auto;
        padding: 10px 14px;
        border: 1px solid #e5e7eb;
        border-radius: 9px;
        background: #fff;
        color: #4b5563;
        font-size: 12px;
        font-weight: 700;
        box-shadow: 0 2px 7px rgba(15, 35, 65, .05);
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 15px;
        margin-bottom: 22px;
    }

    .summary-card {
        position: relative;
        min-height: 118px;
        padding: 18px 19px;
        overflow: hidden;
        border: 1px solid #e7ebf0;
        border-radius: 11px;
        background: #fff;
        box-shadow: 0 3px 10px rgba(15, 35, 65, .055);
    }

    .summary-card::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 4px;
        background: #1769c2;
    }

    .summary-card:nth-child(2)::before,
    .summary-card:nth-child(4)::before {
        background: #c62828;
    }

    .summary-label {
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .02em;
    }

    .summary-value {
        margin-top: 10px;
        color: #123b70;
        font-size: 31px;
        font-weight: 800;
        line-height: 1;
    }

    .summary-note {
        margin-top: 9px;
        color: #9ca3af;
        font-size: 11px;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.55fr) minmax(300px, .85fr);
        gap: 18px;
        align-items: start;
    }

    .stack {
        display: grid;
        gap: 18px;
    }

    .panel {
        border: 1px solid #e7ebf0;
        border-radius: 11px;
        background: #fff;
        box-shadow: 0 3px 10px rgba(15, 35, 65, .05);
        overflow: hidden;
    }

    .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 17px 19px 14px;
        border-bottom: 1px solid #edf0f3;
    }

    .panel-header h2 {
        margin: 0;
        color: #173f73;
        font-size: 16px;
    }

    .panel-header p {
        margin: 3px 0 0;
        color: #8a929d;
        font-size: 11px;
    }

    .panel-link {
        color: #1769c2;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
    }

    .panel-link:hover {
        color: #c62828;
    }

    .table-wrap {
        overflow-x: auto;
    }

    .manager-table {
        width: 100%;
        border-collapse: collapse;
    }

    .manager-table th,
    .manager-table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #f0f2f4;
        font-size: 12px;
    }

    .manager-table th {
        background: #f8fafc;
        color: #667085;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .manager-table td {
        color: #374151;
    }

    .manager-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .record-id {
        color: #123b70;
        font-weight: 800;
    }

    .customer-name {
        color: #1f2937;
        font-weight: 700;
    }

    .muted {
        color: #8a929d;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        min-width: 68px;
        justify-content: center;
        padding: 4px 8px;
        border-radius: 999px;
        background: #eef5fc;
        color: #1769c2;
        font-size: 10px;
        font-weight: 800;
    }

    .status-pill.pending {
        background: #fff6df;
        color: #946200;
    }

    .status-pill.claimed,
    .status-pill.completed {
        background: #edf8f1;
        color: #277445;
    }

    .status-pill.voided {
        background: #fff0f0;
        color: #b42318;
    }

    .overview-list {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .overview-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 13px 19px;
        border-bottom: 1px solid #f0f2f4;
    }

    .overview-row:last-child {
        border-bottom: 0;
    }

    .overview-label {
        color: #667085;
        font-size: 12px;
    }

    .overview-value {
        color: #173f73;
        font-size: 15px;
        font-weight: 800;
    }

    .attention {
        margin: 16px 19px 19px;
        padding: 13px 14px;
        border: 1px solid #f3d1d1;
        border-left: 4px solid #c62828;
        border-radius: 7px;
        background: #fff8f8;
    }

    .attention strong {
        display: block;
        margin-bottom: 3px;
        color: #a52121;
        font-size: 12px;
    }

    .attention span {
        color: #7c4a4a;
        font-size: 11px;
        line-height: 1.5;
    }

    .quick-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 9px;
        padding: 16px 19px 19px;
    }

    .quick-action {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 8px 10px;
        border: 1px solid #d9e4ef;
        border-radius: 7px;
        background: #f8fbff;
        color: #1769c2;
        font-size: 11px;
        font-weight: 800;
        text-decoration: none;
        text-align: center;
        transition: .15s ease;
    }

    .quick-action:hover {
        border-color: #1769c2;
        background: #eef6ff;
    }

    .quick-action.primary {
        border-color: #1769c2;
        background: #1769c2;
        color: #fff;
    }

    .quick-action.primary:hover {
        background: #12579f;
    }

    .empty-state {
        padding: 30px 20px;
        color: #98a1ad;
        font-size: 12px;
        text-align: center;
    }

    .footer-note {
        margin-top: 18px;
        color: #9aa1aa;
        font-size: 11px;
        text-align: right;
    }

    @media (max-width: 1000px) {
        .summary-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 620px) {
        .manager-dashboard {
            width: 92%;
            margin-top: 22px;
        }

        .manager-hero {
            display: block;
        }

        .hero-date {
            display: inline-block;
            margin-top: 13px;
        }

        .summary-grid {
            grid-template-columns: 1fr;
        }

        .quick-actions {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

<div class="manager-dashboard">
    <header class="manager-hero">
        <div>
            <div class="hero-label">Branch Management</div>
            <h1>Manager Dashboard</h1>
            <p>Monitor daily operations, transactions, inventory activity, and branch performance.</p>
        </div>
        <div class="hero-date">{{ now()->format('l, M d, Y') }}</div>
    </header>

    <section class="summary-grid" aria-label="Branch summary">
        <article class="summary-card">
            <div class="summary-label">Today's Transactions</div>
            <div class="summary-value">{{ $todayTransactions }}</div>
            <div class="summary-note">Transactions recorded today</div>
        </article>

        <article class="summary-card">
            <div class="summary-label">Pending Transactions</div>
            <div class="summary-value">{{ $pendingTransactions }}</div>
            <div class="summary-note">Requires staff attention</div>
        </article>

        <article class="summary-card">
            <div class="summary-label">Inventory Items</div>
            <div class="summary-value">{{ $productCount }}</div>
            <div class="summary-note">Items currently in the master list</div>
        </article>

        <article class="summary-card">
            <div class="summary-label">Audit Discrepancies</div>
            <div class="summary-value">{{ $discrepancies }}</div>
            <div class="summary-note">Recorded differences requiring review</div>
        </article>
    </section>

    <div class="dashboard-grid">
        <div class="stack">
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2>Recent Transactions</h2>
                        <p>Latest customer transactions recorded by the branch</p>
                    </div>
                    <a class="panel-link" href="{{ route('manager.transactions.index') }}">View all</a>
                </div>

                @if($recentTransactions->count())
                    <div class="table-wrap">
                        <table class="manager-table">
                            <thead>
                                <tr>
                                    <th>Transaction</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                    @php
                                        $status = strtolower((string) ($transaction->status ?? 'pending'));
                                    @endphp
                                    <tr>
                                        <td class="record-id">#{{ $transaction->id }}</td>
                                        <td class="customer-name">{{ $transaction->customer_name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="status-pill {{ $status }}">{{ $transaction->status ?? 'N/A' }}</span>
                                        </td>
                                        <td class="muted">{{ $transaction->created_at?->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">No transactions have been recorded yet.</div>
                @endif
            </section>

            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2>Recent Inventory Audits</h2>
                        <p>Latest stock verification activity</p>
                    </div>
                    <a class="panel-link" href="{{ route('manager.audits.index') }}">View all</a>
                </div>

                @if($recentAudits->count())
                    <div class="table-wrap">
                        <table class="manager-table">
                            <thead>
                                <tr>
                                    <th>Audit</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAudits as $audit)
                                    @php
                                        $auditStatus = strtolower((string) ($audit->status ?? 'ongoing'));
                                    @endphp
                                    <tr>
                                        <td class="record-id">#{{ $audit->id }}</td>
                                        <td><span class="status-pill {{ $auditStatus }}">{{ $audit->status ?? 'N/A' }}</span></td>
                                        <td class="muted">{{ $audit->created_at?->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">No inventory audits have been recorded yet.</div>
                @endif
            </section>
        </div>

        <aside class="stack">
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2>Branch Overview</h2>
                        <p>Current operational snapshot</p>
                    </div>
                </div>

                <ul class="overview-list">
                    <li class="overview-row">
                        <span class="overview-label">Inventory items</span>
                        <strong class="overview-value">{{ $productCount }}</strong>
                    </li>
                    <li class="overview-row">
                        <span class="overview-label">Pending transactions</span>
                        <strong class="overview-value">{{ $pendingTransactions }}</strong>
                    </li>
                    <li class="overview-row">
                        <span class="overview-label">Inventory audits</span>
                        <strong class="overview-value">{{ $totalAudits }}</strong>
                    </li>
                    <li class="overview-row">
                        <span class="overview-label">Audit discrepancies</span>
                        <strong class="overview-value">{{ $discrepancies }}</strong>
                    </li>
                </ul>

                @if($discrepancies > 0)
                    <div class="attention">
                        <strong>Inventory review needed</strong>
                        <span>{{ $discrepancies }} audit record{{ $discrepancies === 1 ? '' : 's' }} currently show a discrepancy.</span>
                    </div>
                @endif
            </section>

            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2>Manager Shortcuts</h2>
                        <p>Go directly to frequently used areas</p>
                    </div>
                </div>

                <div class="quick-actions">
                    <a class="quick-action primary" href="{{ route('manager.transactions.index') }}">Transactions</a>
                    <a class="quick-action" href="{{ route('manager.inventory.index') }}">Inventory</a>
                    <a class="quick-action" href="{{ route('manager.audits.index') }}">Inventory Audits</a>
                    <a class="quick-action" href="{{ route('reports.index') }}">Reports</a>
                    <a class="quick-action" href="{{ route('users.index') }}">Users</a>
                </div>
            </section>
        </aside>
    </div>

    <div class="footer-note">Photoline Abreeza · Manager operational overview</div>
</div>

</x-manager-layout>
