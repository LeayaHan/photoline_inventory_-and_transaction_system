<x-staff-layout title="Transaction Details">
    <link rel="stylesheet" href="{{ asset('css/transactions.css') }}">

    <div class="transaction-show">
    <div class="staffui-pagehead transaction-pagehead">
        <div>
            <h1>Transaction Details</h1>
            <p>
                Control number: <strong>{{ $transaction->control_number }}</strong>
                &nbsp;<span class="transaction-status transaction-status-{{ strtolower($transaction->status) }}">{{ $transaction->status }}</span>
            </p>
        </div>

        <div class="transaction-pagehead-actions">
            @if ($transaction->isEditable())
                <a class="staffui-btn staffui-btn-outline" href="{{ route('staff.transactions.edit', $transaction) }}">Edit Transaction</a>
            @endif
            <a class="staffui-btn staffui-btn-outline" href="{{ route('staff.transactions') }}">Back to Transactions</a>
        </div>
    </div>

    @if (session('success'))
        <div class="staffui-alert staffui-alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="transaction-errors"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

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
            <table class="staffui-table">
                <thead><tr><th>Item / Service</th><th>Size / Layout</th><th>Notes</th><th>Qty</th><th>Unit Price</th><th>Line Total</th></tr></thead>
                <tbody>
                    @foreach ($transaction->details as $detail)
                        <tr>
                            <td>{{ $detail->service_type }}</td>
                            <td>{{ $detail->service_size ?: '—' }}@if ($detail->print_layout) / {{ $detail->print_layout }}@endif</td>
                            <td>{{ $detail->order_notes ?: $detail->description ?: '—' }}</td>
                            <td>{{ $detail->quantity }}</td>
                            <td>₱{{ number_format((float) $detail->amount, 2) }}</td>
                            <td>₱{{ number_format($detail->quantity * (float) $detail->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    {{-- Left: payment history and what staff can do. Right: money summary and "record payment". --}}
    <div class="transaction-detail-layout">
        <div class="transaction-detail-main">
            <section class="transaction-form-card">
                <div class="transaction-form-section-head"><h2>Payment History</h2></div>

                <div class="transaction-table-wrap">
                    <table class="staffui-table">
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

            {{-- Only a pending transaction can be completed or voided. Completed and voided transactions are final. --}}
            @if ($transaction->isVoidable())
                @php
                    $voidWarning = 'Void this transaction? This action cannot be undone.';
                @endphp

                @if ($transaction->status === 'Pending')
                    <section class="transaction-action-card">
                        <h2>Complete Transaction</h2>

                        @if ($transaction->balance > 0)
                            {{-- the balance itself is already shown in the summary on the right --}}
                            <p>Available once the balance is fully paid.</p>
                            <button class="staffui-btn" type="button" disabled>Mark as Completed</button>
                        @else
                            <p>Deducts the used stock from inventory. A completed transaction can no longer be edited.</p>
                            <form method="POST" action="{{ route('staff.transactions.complete', $transaction) }}" data-confirm="Complete this transaction? Inventory will be deducted and it can no longer be edited." data-confirm-title="Complete transaction" data-confirm-ok="Complete">
                                @csrf
                                <button class="staffui-btn" type="submit">Mark as Completed</button>
                            </form>
                        @endif
                    </section>
                @endif

                <section class="transaction-action-card transaction-void-card">
                    <h2>Void Transaction</h2>
                    <p>
                        Only a pending transaction can be voided. No stock was deducted yet, so nothing is returned.
                        @if ($transaction->amount_paid > 0)
                            Payments stay in the history, so refund the customer if needed.
                        @endif
                        A reason is required. This cannot be undone.
                    </p>

                    <form method="POST" action="{{ route('staff.transactions.void', $transaction) }}" class="transaction-void-row" data-confirm="{{ $voidWarning }}" data-confirm-title="Void transaction" data-confirm-ok="Void">
                        @csrf
                        <input type="text" name="void_reason" maxlength="255" required value="{{ old('void_reason') }}" placeholder="Reason for voiding (required)" aria-label="Reason for voiding">
                        <button class="transaction-danger-button" type="submit">Void Transaction</button>
                    </form>
                </section>
            @endif
        </div>

        <aside class="transaction-detail-side">
            <section class="transaction-form-card">
                <div class="transaction-form-section-head"><h2>Payment Summary</h2></div>
                @include('transactions._payment-summary')
            </section>

            {{-- another payment can be added any time while there is a balance --}}
            @if ($transaction->status !== 'Voided' && $transaction->balance > 0)
                <section class="transaction-form-card">
                    <div class="transaction-form-section-head"><h2>Record Payment</h2></div>

                    <form method="POST" action="{{ route('staff.transactions.payments.store', $transaction) }}" class="transaction-record-payment">
                        @csrf

                        <label class="transaction-field">Amount (₱)
                            <input type="number" name="amount" min="0.01" max="{{ number_format($transaction->balance, 2, '.', '') }}" step="0.01" required value="{{ old('amount') }}">
                        </label>

                        <label class="transaction-field">Payment Method
                            <select name="payment_type" required>
                                @foreach (\App\Models\TransactionPayment::METHODS as $method)
                                    <option value="{{ $method }}" @selected(old('payment_type') === $method)>{{ $method }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="transaction-field">Payment Date
                            <input type="datetime-local" name="paid_at" required value="{{ old('paid_at', now()->format('Y-m-d\TH:i')) }}">
                        </label>

                        <button class="staffui-btn" type="submit">Record Payment</button>
                    </form>
                </section>
            @endif
        </aside>
    </div>
    </div>
</x-staff-layout>
