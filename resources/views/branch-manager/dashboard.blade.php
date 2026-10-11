@php
    $money = fn ($v) => '₱' . number_format((float) $v, 2);
    $day = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->format('M d, Y') : '—';
@endphp

<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/branch-manager/dashboard.css') }}">

    <section class="bmdash-page">
        <header class="bmdash-heading">
            <div>
                <p class="bmdash-eyebrow">BRANCH MANAGER / OVERVIEW</p>
                <h1>Dashboard</h1>
            </div>
            <div class="bmdash-date"><span>Today</span><strong>{{ now()->format('D, M j, Y') }}</strong></div>
        </header>

        <section class="bmdash-section" aria-labelledby="bmdash-summary-title">
            <div class="bmdash-section-heading"><div><h2 id="bmdash-summary-title">At a glance</h2></div></div>
            <div class="bmdash-stats">
                <article class="bmdash-stat"><span class="bmdash-stat-label">Sales today</span><strong>{{ $money($sales['today']['billed']) }}</strong><span class="bmdash-stat-foot">{{ $sales['today']['count'] }} completed transaction(s)</span></article>
                <article class="bmdash-stat"><span class="bmdash-stat-label">Sales this month</span><strong>{{ $money($sales['month']['billed']) }}</strong><span class="bmdash-stat-foot">{{ $sales['month']['count'] }} completed transaction(s)</span></article>
                <article class="bmdash-stat"><span class="bmdash-stat-label">Collected this month</span><strong>{{ $money($sales['month']['collected']) }}</strong><span class="bmdash-stat-foot">Payments received</span></article>
                <article class="bmdash-stat bmdash-stat-attention"><span class="bmdash-stat-label">Low / out of stock</span><strong>{{ $summary['low_stock'] }}</strong><span class="bmdash-stat-foot">Items needing stock review</span></article>
                <article class="bmdash-stat"><span class="bmdash-stat-label">Pending requests</span><strong>{{ $summary['pending_requests'] }}</strong><span class="bmdash-stat-foot">Awaiting manager action</span></article>
                <article class="bmdash-stat"><span class="bmdash-stat-label">Draft audits</span><strong>{{ $summary['draft_audits'] }}</strong><span class="bmdash-stat-foot">Not yet finalized</span></article>
                <article class="bmdash-stat"><span class="bmdash-stat-label">Staff accounts</span><strong>{{ $summary['staff'] }}</strong><span class="bmdash-stat-foot">Registered staff profiles</span></article>
            </div>
        </section>

        <div class="bmdash-grid">
            <section class="bmdash-panel">
                <div class="bmdash-panel-heading"><div><h2>Stock alerts</h2></div><span class="bmdash-count">{{ $stockAlertTotal }}</span></div>
                <div class="bmdash-table-wrap"><table class="bmdash-table"><thead><tr><th>Item</th><th>On hand</th><th>Alert</th></tr></thead><tbody>
                    @forelse ($stockAlerts as $item)
                        <tr><td class="bmdash-primary-cell">{{ $item->name }}</td><td>{{ number_format($item->quantity) }} {{ $item->unit }}</td><td><div class="bmdash-flags">@foreach ($item->flags as $flag)<span class="bmdash-flag {{ in_array($flag, ['Out of stock', 'Expired'], true) ? 'is-critical' : 'is-warning' }}">{{ $flag }}</span>@endforeach</div></td></tr>
                    @empty<tr><td colspan="3" class="bmdash-empty">All clear — no stock alerts.</td></tr>@endforelse
                </tbody></table></div>
                @if ($stockAlertTotal > $stockAlerts->count())<p class="bmdash-panel-note">Showing {{ $stockAlerts->count() }} of {{ $stockAlertTotal }} alerts.</p>@endif
                <div class="bmdash-panel-footer"><a class="bmdash-view-all" href="{{ route('branch-manager.inventory.index') }}">View All Inventory <span aria-hidden="true">→</span></a></div>
            </section>

            <section class="bmdash-panel">
                <div class="bmdash-panel-heading"><div><h2>Recent inventory requests</h2></div></div>
                <div class="bmdash-table-wrap"><table class="bmdash-table"><thead><tr><th>Item</th><th>Request</th><th>Staff</th><th>Status</th></tr></thead><tbody>
                    @forelse ($recentRequests as $request)<tr><td class="bmdash-primary-cell">{{ $request->item_name }}</td><td>{{ $request->type_label }}</td><td>{{ $request->staff?->full_name ?? '—' }}</td><td><span class="bmdash-status status-{{ strtolower($request->status) }}">{{ ucfirst($request->status) }}</span></td></tr>
                    @empty<tr><td colspan="4" class="bmdash-empty">No inventory requests yet.</td></tr>@endforelse
                </tbody></table></div>
                <div class="bmdash-panel-footer"><a class="bmdash-view-all" href="{{ route('branch-manager.inventory-requests.index') }}">View All Requests <span aria-hidden="true">→</span></a></div>
            </section>

            <section class="bmdash-panel">
                <div class="bmdash-panel-heading"><div><h2>Recent transactions</h2></div></div>
                <div class="bmdash-table-wrap"><table class="bmdash-table"><thead><tr><th>Control no.</th><th>Customer</th><th>Date</th><th class="is-number">Amount</th><th>Status</th></tr></thead><tbody>
                    @forelse ($recentTransactions as $transaction)<tr><td class="bmdash-primary-cell">{{ $transaction->control_number }}</td><td>{{ $transaction->customer_name }}</td><td>{{ $day($transaction->transaction_date) }}</td><td class="is-number">{{ $money($transaction->total_amount) }}</td><td><span class="bmdash-status status-{{ strtolower($transaction->status) }}">{{ $transaction->status }}</span></td></tr>
                    @empty<tr><td colspan="5" class="bmdash-empty">No transactions recorded yet.</td></tr>@endforelse
                </tbody></table></div>
                <div class="bmdash-panel-footer"><a class="bmdash-view-all" href="{{ route('branch-manager.transactions.index') }}">View All Transactions <span aria-hidden="true">→</span></a></div>
            </section>

            <section class="bmdash-panel">
                <div class="bmdash-panel-heading"><div><h2>Recent audits</h2></div></div>
                <div class="bmdash-table-wrap"><table class="bmdash-table"><thead><tr><th>Audit no.</th><th>Auditor</th><th>Date</th><th>Status</th></tr></thead><tbody>
                    @forelse ($recentAudits as $audit)<tr><td class="bmdash-primary-cell">{{ $audit->audit_number }}</td><td>{{ $audit->auditor_name }}</td><td>{{ $day($audit->audit_date) }}</td><td><span class="bmdash-status {{ $audit->isFinalized() ? 'status-completed' : 'status-pending' }}">{{ $audit->isFinalized() ? 'Finalized' : 'Draft' }}</span></td></tr>
                    @empty<tr><td colspan="4" class="bmdash-empty">No audits recorded yet.</td></tr>@endforelse
                </tbody></table></div>
                <div class="bmdash-panel-footer"><a class="bmdash-view-all" href="{{ route('branch-manager.audits.index') }}">View All Audits <span aria-hidden="true">→</span></a></div>
            </section>
        </div>
    </section>
</x-app-layout>
