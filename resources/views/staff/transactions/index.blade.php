<x-staff-layout title="Transactions">
    <link rel="stylesheet" href="{{ asset('css/transactions.css') }}">

    <div class="staffui-pagehead transaction-pagehead">
        <div>
            <h1>Customer Transactions</h1>
        </div>
        <a class="staffui-btn" href="{{ route('staff.transactions.create') }}">New Transaction</a>
    </div>

    @if (session('success'))
        <div class="staffui-alert staffui-alert-success">{{ session('success') }}</div>
    @endif

    <div class="transaction-summary-grid">
        <div class="transaction-summary"><span>All Transactions</span><strong>{{ $summary['total'] }}</strong></div>
        <div class="transaction-summary"><span>Pending</span><strong>{{ $summary['pending'] }}</strong></div>
        <div class="transaction-summary"><span>Completed</span><strong>{{ $summary['completed'] }}</strong></div>
        <div class="transaction-summary"><span>Voided</span><strong>{{ $summary['voided'] }}</strong></div>
    </div>

    <form method="GET" class="transaction-filters">
        <label>Search
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Control number or customer name">
        </label>
        <label>Status
            <select name="status">
                <option value="">All statuses</option>
                @foreach (['Pending', 'Completed', 'Voided'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </label>
        <label>From <input type="date" name="date_from" value="{{ request('date_from') }}"></label>
        <label>To <input type="date" name="date_to" value="{{ request('date_to') }}"></label>
        <button class="staffui-btn staffui-btn-outline" type="submit">Filter</button>
        <a class="transaction-clear" href="{{ route('staff.transactions') }}">Clear</a>
    </form>

    <div class="staffui-tablecard transaction-table-wrap">
        <table class="staffui-table">
            <thead>
                <tr>
                    <th>Control Number</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total Due</th>
                    <th>Amount Paid</th>
                    <th>Balance Due</th>
                    <th>Payment Status</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $transaction)
                    <tr>
                        <td class="staffui-code">{{ $transaction->control_number }}</td>
                        <td>{{ $transaction->customer_name }}</td>
                        <td>{{ $transaction->transaction_date->format('M d, Y') }}</td>
                        <td>₱{{ number_format($transaction->total_amount, 2) }}</td>
                        <td>₱{{ number_format($transaction->amount_paid, 2) }}</td>
                        <td>₱{{ number_format($transaction->balance, 2) }}</td>
                        <td><span class="payment-status {{ $transaction->payment_status_class }}">{{ $transaction->payment_status }}</span></td>
                        <td><span class="transaction-status transaction-status-{{ strtolower($transaction->status) }}">{{ $transaction->status }}</span></td>
                        <td><a class="staffui-btn staffui-btn-outline staffui-btn-sm" href="{{ route('staff.transactions.show', $transaction) }}">View Details</a></td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="staffui-emptyrow">No transactions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="staffui-pager">{{ $transactions->links() }}</div>
</x-staff-layout>
