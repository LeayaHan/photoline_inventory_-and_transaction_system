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
        font-size: 29px;
        letter-spacing: -.7px;
    }

    .primary-action,
    .secondary-action,
    .danger-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 0 14px;
        border: 0;
        border-radius: 8px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .primary-action {
        color: #fff;
        background: #1769e8;
    }

    .secondary-action {
        color: #536078;
        background: #eef3f9;
    }

    .danger-action {
        color: #c12721;
        background: #fff0ef;
    }

    .notice {
        padding: 12px 14px;
        margin-bottom: 14px;
        border-radius: 9px;
        font-size: 12px;
    }

    .notice.success {
        color: #206c43;
        background: #ebf8f0;
        border: 1px solid #ccebd9;
    }

    .notice.error {
        color: #a72520;
        background: #fff0ef;
        border: 1px solid #f5cecb;
    }

    .toolbar {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 14px;
        padding: 14px;
        border: 1px solid #e5eaf2;
        border-radius: 12px;
        background: #fff;
    }

    .search-input {
        flex: 1;
        min-height: 40px;
        padding: 0 12px;
        border: 1px solid #dbe2ed;
        border-radius: 8px;
        outline: none;
        font-size: 12px;
    }

    .search-input:focus {
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
    }

    .data-table th,
    .data-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #edf0f5;
        text-align: left;
        font-size: 12px;
    }

    .data-table th {
        color: #7b8799;
        background: #fafbfd;
        font-size: 10px;
        letter-spacing: .6px;
        text-transform: uppercase;
    }

    .data-table tr:last-child td {
        border-bottom: 0;
    }

    .control-number {
        color: #1769e8;
        font-weight: 800;
    }

    .status {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
    }

    .status.pending { color: #1769e8; background: #eaf2ff; }
    .status.claimed { color: #20754a; background: #eaf8f0; }
    .status.voided { color: #b52a25; background: #fff0ef; }

    .row-actions {
        display: flex;
        align-items: center;
        gap: 7px;
        white-space: nowrap;
    }

    .row-actions a {
        color: #1769e8;
        text-decoration: none;
        font-size: 11px;
        font-weight: 700;
    }

    .pagination {
        margin-top: 18px;
    }

    @media (max-width: 800px) {
        .page-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .toolbar {
            flex-wrap: wrap;
        }

        .search-input {
            flex-basis: 100%;
        }
    }
</style>
@endpush

<div class="staff-page">
    <div class="page-heading">
        <div>
            <small>Staff Operations</small>
            <h1>Customer Transactions</h1>
        </div>

        <a href="{{ route('transactions.create') }}" class="primary-action">
            + New Transaction
        </a>
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
            placeholder="Search control number or customer name"
        >

        <button type="submit" class="secondary-action">Search</button>

        @if(request('search'))
            <a href="{{ route('transactions.index') }}" class="secondary-action">Clear</a>
        @endif
    </form>

    <div class="table-card">
        <div style="overflow-x:auto;">
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
                            <td>{{ $transaction->customer_name }}</td>
                            <td>{{ $transaction->service_type }}</td>
                            <td>{{ $transaction->transaction_date->format('M d, Y') }}</td>
                            <td>
                                <span class="status {{ strtolower($transaction->status) }}">
                                    {{ $transaction->status }}
                                </span>
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('transactions.show', $transaction) }}">View</a>
                                    @if($transaction->status !== 'Voided')
                                        <a href="{{ route('transactions.edit', $transaction) }}">Edit</a>

                                        <form
                                            method="POST"
                                            action="{{ route('transactions.destroy', $transaction) }}"
                                            onsubmit="return confirm('Void this transaction?');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="danger-action">Void</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:34px; color:#7b8799;">
                                No transactions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="pagination">
        {{ $transactions->links() }}
    </div>
</div>

</x-staff-layout>
