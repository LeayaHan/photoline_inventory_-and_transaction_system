<x-staff-layout title="Transactions | Photoline">

@push('styles')
<style>
    .staff-page {
        width: min(1180px, calc(100% - 36px));
        margin: 0 auto;
        padding: 32px 0 55px;
    }

    .page-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 18px;
        margin-bottom: 18px;
    }

    .page-heading small {
        color: #ed2b24;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.4px;
        text-transform: uppercase;
    }

    .page-heading h1 {
        margin: 5px 0 0;
        color: #0b2d5c;
        font-size: 29px;
        letter-spacing: -.7px;
    }

    .page-heading p {
        margin: 7px 0 0;
        color: #68738a;
        font-size: 13px;
    }

    .primary-action,
    .secondary-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 0 15px;
        border: 0;
        border-radius: 8px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .primary-action { color: #fff; background: #1769e8; }
    .secondary-action { color: #536078; background: #eef3f9; }

    .notice {
        padding: 12px 14px;
        margin-bottom: 14px;
        border-radius: 9px;
        font-size: 12px;
    }

    .notice.success { color: #206c43; background: #ebf8f0; border: 1px solid #ccebd9; }
    .notice.error { color: #a72520; background: #fff0ef; border: 1px solid #f5cecb; }

    .toolbar {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 150px auto auto;
        align-items: center;
        gap: 9px;
        margin-bottom: 14px;
        padding: 14px;
        border: 1px solid #e5eaf2;
        border-radius: 12px;
        background: #fff;
    }

    .search-input,
    .status-select {
        width: 100%;
        min-height: 40px;
        box-sizing: border-box;
        padding: 0 12px;
        border: 1px solid #dbe2ed;
        border-radius: 8px;
        outline: none;
        color: #172b4d;
        background: #fff;
        font-size: 12px;
    }

    .search-input:focus,
    .status-select:focus {
        border-color: #1769e8;
        box-shadow: 0 0 0 3px rgba(23,105,232,.09);
    }

    .table-card {
        overflow: hidden;
        border: 1px solid #e5eaf2;
        border-radius: 14px;
        background: #fff;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .data-table th,
    .data-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #edf0f5;
        text-align: left;
        vertical-align: middle;
        font-size: 12px;
    }

    .data-table th {
        color: #7b8799;
        background: #fafbfd;
        font-size: 10px;
        letter-spacing: .6px;
        text-transform: uppercase;
    }

    .data-table tr:last-child td { border-bottom: 0; }
    .data-table th:nth-child(1) { width: 17%; }
    .data-table th:nth-child(2) { width: 17%; }
    .data-table th:nth-child(3) { width: 17%; }
    .data-table th:nth-child(4) { width: 13%; }
    .data-table th:nth-child(5) { width: 11%; }
    .data-table th:nth-child(6) { width: 25%; }

    .data-table td:not(:last-child) {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .control-number {
        color: #1769e8;
        font-weight: 800;
        letter-spacing: .1px;
    }

    .status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 64px;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
    }

    .status.pending { color: #1769e8; background: #eaf2ff; }
    .status.claimed { color: #20754a; background: #eaf8f0; }
    .status.voided { color: #b52a25; background: #fff0ef; }

    .action-group {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
    }

    .action-group form { margin: 0; }

    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 56px;
        height: 32px;
        padding: 0 10px;
        box-sizing: border-box;
        border: 1px solid transparent;
        border-radius: 7px;
        text-decoration: none;
        font-size: 11px;
        font-weight: 800;
        line-height: 1;
        cursor: pointer;
        white-space: nowrap;
    }

    .action-view { color: #1769e8; background: #f3f7fd; border-color: #dbe7f8; }
    .action-edit { color: #1769e8; background: #fff; border-color: #bcd3f7; }
    .action-claim { color: #20754a; background: #ebf8f0; border-color: #ccebd9; }
    .action-void { color: #b52a25; background: #fff0ef; border-color: #f3cfcc; }
    .action-locked { color: #8792a4; background: #f5f7fa; border-color: #e4e8ee; cursor: default; }

    .empty-state {
        padding: 42px 20px !important;
        text-align: center !important;
        color: #7b8799;
    }

    .pagination { margin-top: 18px; }

    @media (max-width: 900px) {
        .toolbar { grid-template-columns: 1fr 150px; }
        .toolbar .secondary-action { width: 100%; }
        .data-table { min-width: 960px; }
        .table-scroll { overflow-x: auto; }
    }

    @media (max-width: 650px) {
        .page-heading { align-items: flex-start; flex-direction: column; }
        .toolbar { grid-template-columns: 1fr; }
        .primary-action { width: 100%; }
    }
</style>
@endpush

<div class="staff-page">
    <div class="page-heading">
        <div>
            <small>Staff Operations</small>
            <h1>Customer Transactions</h1>
            <p>Manage customer service transactions from creation through completion.</p>
        </div>

        <a href="{{ route('transactions.create') }}" class="primary-action">+ New Transaction</a>
    </div>

    @if(session('success'))
        <div class="notice success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="notice error">{{ session('error') }}</div>
    @endif

    <form method="GET" action="{{ route('transactions.index') }}" class="toolbar">
        <input
            class="search-input"
            type="search"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search control number, customer, or service"
        >

        <select class="status-select" name="status" aria-label="Filter by status">
            <option value="">All Statuses</option>
            <option value="Pending" @selected(request('status') === 'Pending')>Pending</option>
            <option value="Claimed" @selected(request('status') === 'Claimed')>Claimed</option>
            <option value="Voided" @selected(request('status') === 'Voided')>Voided</option>
        </select>

        <button type="submit" class="secondary-action">Search</button>

        @if(request()->filled('search') || request()->filled('status'))
            <a href="{{ route('transactions.index') }}" class="secondary-action">Clear</a>
        @endif
    </form>

    <div class="table-card">
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Control Number</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td class="control-number">{{ $transaction->control_number }}</td>
                            <td title="{{ $transaction->customer_name }}">{{ $transaction->customer_name }}</td>
                            <td title="{{ $transaction->service_type }}">{{ $transaction->service_type }}</td>
                            <td>{{ $transaction->transaction_date->format('M d, Y') }}</td>
                            <td>
                                <span class="status {{ strtolower($transaction->status) }}">{{ $transaction->status }}</span>
                            </td>
                            <td>
                                <div class="action-group">
                                    <a class="action-btn action-view" href="{{ route('transactions.show', $transaction) }}">View</a>

                                    @if($transaction->status === 'Pending')
                                        <a class="action-btn action-edit" href="{{ route('transactions.edit', $transaction) }}">Edit</a>

                                        <form method="POST" action="{{ route('transactions.claim', $transaction) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="action-btn action-claim">Claim</button>
                                        </form>

                                        <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" onsubmit="return confirm('Void this pending transaction?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn action-void">Void</button>
                                        </form>
                                    @elseif($transaction->status === 'Claimed')
                                        <span class="action-btn action-locked">Locked</span>
                                    @else
                                        <span class="action-btn action-locked">Voided</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">No transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="pagination">{{ $transactions->links() }}</div>
</div>

</x-staff-layout>
