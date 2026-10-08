<x-staff-layout title="Transaction Details | Photoline">

@push('styles')
<style>
    .details-page { width:min(900px,calc(100% - 36px)); margin:0 auto; padding:34px 0 55px; }
    .details-card { overflow:hidden; border:1px solid #e5eaf2; border-radius:16px; background:#fff; box-shadow:0 10px 30px rgba(20,45,80,.05); }
    .details-head { display:flex; justify-content:space-between; gap:20px; align-items:flex-start; padding:26px 28px; border-bottom:1px solid #edf0f5; }
    .details-head small { color:#ed2b24; font-size:10px; font-weight:800; letter-spacing:1.4px; text-transform:uppercase; }
    .details-head h1 { margin:6px 0 5px; color:#0b2d5c; font-size:27px; letter-spacing:-.6px; }
    .details-head p { margin:0; color:#68738a; font-size:12px; }
    .status { display:inline-flex; align-items:center; justify-content:center; min-width:68px; padding:6px 10px; border-radius:999px; font-size:10px; font-weight:800; }
    .status.pending { color:#1769e8; background:#eaf2ff; }
    .status.claimed { color:#20754a; background:#eaf8f0; }
    .status.voided { color:#b52a25; background:#fff0ef; }
    .notice { margin:18px 28px 0; padding:12px 14px; border-radius:9px; font-size:12px; }
    .notice.success { color:#206c43; background:#ebf8f0; border:1px solid #ccebd9; }
    .notice.error { color:#a72520; background:#fff0ef; border:1px solid #f5cecb; }
    .detail-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:0; padding:8px 28px; }
    .detail-item { padding:18px 0; border-bottom:1px solid #edf0f5; }
    .detail-item:nth-last-child(-n+2) { border-bottom:0; }
    .detail-label { margin-bottom:6px; color:#7b8799; font-size:10px; font-weight:800; letter-spacing:.6px; text-transform:uppercase; }
    .detail-value { color:#172b4d; font-size:13px; font-weight:600; overflow-wrap:anywhere; }
    .detail-value.control { color:#1769e8; font-weight:800; }
    .detail-description { grid-column:1 / -1; }
    .actions { display:flex; align-items:center; gap:8px; flex-wrap:wrap; padding:20px 28px 26px; }
    .action-btn { display:inline-flex; align-items:center; justify-content:center; min-width:78px; height:38px; padding:0 13px; border:1px solid transparent; border-radius:8px; text-decoration:none; font-size:12px; font-weight:800; cursor:pointer; }
    .action-btn.primary { color:#fff; background:#1769e8; }
    .action-btn.neutral { color:#536078; background:#eef3f9; }
    .action-btn.claim { color:#20754a; background:#ebf8f0; border-color:#ccebd9; }
    .action-btn.void { color:#b52a25; background:#fff0ef; border-color:#f3cfcc; }
    .actions form { margin:0; }
    .locked-note { margin-right:auto; color:#7b8799; font-size:11px; }
    @media(max-width:700px) {
        .details-head { flex-direction:column; }
        .detail-grid { grid-template-columns:1fr; }
        .detail-item:nth-last-child(-n+2) { border-bottom:1px solid #edf0f5; }
        .detail-item:last-child { border-bottom:0; }
        .detail-description { grid-column:auto; }
    }
</style>
@endpush

<div class="details-page">
    <div class="details-card">
        <div class="details-head">
            <div>
                <small>Staff Operations</small>
                <h1>Transaction Details</h1>
                <p>Review the transaction record and available workflow actions.</p>
            </div>
            <span class="status {{ strtolower($transaction->status) }}">{{ $transaction->status }}</span>
        </div>

        @if(session('success'))
            <div class="notice success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="notice error">{{ session('error') }}</div>
        @endif

        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">Control Number</div>
                <div class="detail-value control">{{ $transaction->control_number }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Transaction Date</div>
                <div class="detail-value">{{ $transaction->transaction_date->format('M d, Y') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Customer Name</div>
                <div class="detail-value">{{ $transaction->customer_name }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Service Type</div>
                <div class="detail-value">{{ $transaction->service_type }}</div>
            </div>
            <div class="detail-item detail-description">
                <div class="detail-label">Item / Service Description</div>
                <div class="detail-value">{{ $transaction->item_description ?: 'No description provided.' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Recorded By</div>
                <div class="detail-value">{{ $transaction->creator?->name ?? 'Unknown' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Created</div>
                <div class="detail-value">{{ $transaction->created_at?->format('M d, Y h:i A') }}</div>
            </div>
        </div>

        <div class="actions">
            <a href="{{ route('transactions.index') }}" class="action-btn neutral">Back</a>

            @if($transaction->status === 'Pending')
                <a href="{{ route('transactions.edit', $transaction) }}" class="action-btn primary">Edit</a>

                <form method="POST" action="{{ route('transactions.claim', $transaction) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="action-btn claim">Claim</button>
                </form>

                <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" onsubmit="return confirm('Void this pending transaction?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn void">Void</button>
                </form>
            @else
                <span class="locked-note">
                    {{ $transaction->status === 'Claimed' ? 'Claimed transactions are locked.' : 'Voided transactions are locked.' }}
                </span>
            @endif
        </div>
    </div>
</div>

</x-staff-layout>
