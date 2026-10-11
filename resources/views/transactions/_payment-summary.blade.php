{{-- Payment summary: shown on both the staff and the branch manager transaction pages --}}
<div class="payment-summary">
    {{-- Subtotal only matters when a discount changes it --}}
    @if ($transaction->discount_amount > 0)
        <div><span>Subtotal</span><strong>₱{{ number_format($transaction->subtotal, 2) }}</strong></div>
        <div><span>Discount</span><strong>−₱{{ number_format($transaction->discount_amount, 2) }}</strong></div>
    @endif

    <div class="payment-summary-total"><span>Total Due</span><strong>₱{{ number_format($transaction->total_amount, 2) }}</strong></div>
    <div><span>Amount Paid</span><strong>₱{{ number_format($transaction->amount_paid, 2) }}</strong></div>
    <div class="payment-summary-balance {{ $transaction->balance > 0 ? 'is-owing' : 'is-clear' }}"><span>Balance Due</span><strong>₱{{ number_format($transaction->balance, 2) }}</strong></div>
    <div><span>Payment Status</span><span class="payment-status {{ $transaction->payment_status_class }}">{{ $transaction->payment_status }}</span></div>
</div>
