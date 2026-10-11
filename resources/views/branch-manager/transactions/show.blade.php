<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/branch-manager/users.css') }}">
    <link rel="stylesheet" href="{{ asset('css/transactions.css') }}">

    <div class="users-container transaction-manager-page transaction-show">
        <div class="users-header">
            <div>
                <h1 class="users-title">Transaction Details</h1>
                <p class="users-subtitle">
                    Control number: {{ $transaction->control_number }}
                    &nbsp;<span class="transaction-status transaction-status-{{ strtolower($transaction->status) }}">{{ $transaction->status }}</span>
                </p>
            </div>
            <a class="users-btn users-btn-primary" href="{{ route('branch-manager.transactions.index') }}">Back to Transactions</a>
        </div>

        <section class="transaction-form-card">
            <div class="transaction-form-section-head"><h2>Customer Information</h2></div>

            <div class="transaction-info-grid">
                <div><span>Customer Name</span><strong>{{ $transaction->customer_name }}</strong></div>
                <div><span>Contact Number</span><strong>{{ $transaction->customer_contact_number ?: '—' }}</strong></div>
                <div><span>Address</span><strong>{{ $transaction->customer_address ?: '—' }}</strong></div>
                <div><span>Transaction Date</span><strong>{{ $transaction->transaction_date->format('F d, Y') }}</strong></div>
                <div><span>Recorded By</span><strong>{{ $transaction->staff?->full_name ?? 'Former staff' }}</strong></div>
                @if ($transaction->void_reason)
                    <div class="transaction-info-wide"><span>Void Reason</span><strong>{{ $transaction->void_reason }}</strong></div>
                @endif
                @if ($transaction->voided_at)
                    <div><span>Voided By</span><strong>{{ $transaction->voidedBy?->full_name ?? 'Former staff' }}</strong></div>
                    <div><span>Voided On</span><strong>{{ $transaction->voided_at->format('M d, Y h:i A') }}</strong></div>
                @endif
            </div>
        </section>

        <section class="transaction-form-card">
            <div class="transaction-form-section-head"><h2>Items and Services</h2></div>

            <div class="transaction-table-wrap">
                <table class="users-table">
                    <thead><tr><th>Item / Service</th><th>Description</th><th>Qty</th><th>Unit Price</th><th>Line Total</th></tr></thead>
                    <tbody>
                        @foreach ($transaction->details as $detail)
                            <tr>
                                <td>{{ $detail->service_type }}</td>
                                <td>{{ $detail->description ?: '—' }}</td>
                                <td>{{ $detail->quantity }}</td>
                                <td>₱{{ number_format((float) $detail->amount, 2) }}</td>
                                <td>₱{{ number_format($detail->quantity * (float) $detail->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <div class="transaction-payments-row">
            <section class="transaction-form-card">
                <div class="transaction-form-section-head"><h2>Payment History</h2></div>

                <div class="transaction-table-wrap">
                    <table class="users-table">
                        <thead><tr><th>Payment Date</th><th>Method</th><th>Recorded By</th><th>Amount</th></tr></thead>
                        <tbody>
                            @forelse ($transaction->payments as $payment)
                                <tr>
                                    <td>{{ $payment->paid_at->format('M d, Y h:i A') }}</td>
                                    <td>{{ $payment->payment_type }}</td>
                                    <td>{{ $payment->staff?->full_name ?? 'Former staff' }}</td>
                                    <td>₱{{ number_format((float) $payment->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4">No payments recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="transaction-form-card">
                <div class="transaction-form-section-head"><h2>Payment Summary</h2></div>

                @include('transactions._payment-summary')
            </section>
        </div>
    </div>
</x-app-layout>