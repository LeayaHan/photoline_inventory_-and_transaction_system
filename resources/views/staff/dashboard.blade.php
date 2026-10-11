@php
    $money = fn ($v) => '₱' . number_format((float) $v, 2);
    $day = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->format('M d, Y') : '—';
@endphp

<x-staff-layout title="Dashboard">
    <link rel="stylesheet" href="{{ asset('css/staff-dashboard.css') }}">

    <section class="sdash-page">
        <header class="sdash-heading">
            <div><p class="sdash-eyebrow">STAFF PORTAL / OVERVIEW</p><h1>Welcome, {{ $staff->first_name }}!</h1><p class="sdash-subtitle">Here’s a quick look at your current workload and recent activity.</p></div>
            <div class="sdash-date"><span>Today</span><strong>{{ now()->format('D, M j, Y') }}</strong></div>
        </header>

        <section class="sdash-section" aria-labelledby="sdash-summary-title">
            <div class="sdash-section-heading"><div><h2 id="sdash-summary-title">Your workload</h2><p>Activity associated with your staff account.</p></div></div>
            <div class="sdash-stats">
                <article class="sdash-stat"><span class="sdash-stat-label">Pending transactions</span><strong>{{ $summary['pending_transactions'] }}</strong><span class="sdash-stat-foot">Orders awaiting completion</span></article>
                <article class="sdash-stat sdash-stat-attention"><span class="sdash-stat-label">Outstanding balance</span><strong>{{ $money($summary['balance_owed']) }}</strong><span class="sdash-stat-foot">Across your pending transactions</span></article>
                <article class="sdash-stat"><span class="sdash-stat-label">Completed today</span><strong>{{ $summary['completed_today'] }}</strong><span class="sdash-stat-foot">Transactions marked completed</span></article>
                <article class="sdash-stat"><span class="sdash-stat-label">Draft audits</span><strong>{{ $summary['draft_audits'] }}</strong><span class="sdash-stat-foot">Audits not yet finalized</span></article>
                <article class="sdash-stat"><span class="sdash-stat-label">Pending requests</span><strong>{{ $summary['pending_requests'] }}</strong><span class="sdash-stat-foot">Awaiting manager review</span></article>
            </div>
        </section>

        <div class="sdash-grid">
            <section class="sdash-panel"><div class="sdash-panel-heading"><div><h2>My recent transactions</h2><p>Your latest customer service records.</p></div></div>
                <div class="sdash-table-wrap"><table class="sdash-table"><thead><tr><th>Control no.</th><th>Customer</th><th class="is-number">Balance</th><th>Status</th></tr></thead><tbody>
                    @forelse ($recentTransactions as $transaction)<tr><td class="sdash-primary-cell">{{ $transaction->control_number }}</td><td>{{ $transaction->customer_name }}</td><td class="is-number">{{ $transaction->status === 'Voided' ? '—' : $money($transaction->balance) }}</td><td><span class="sdash-status status-{{ strtolower($transaction->status) }}">{{ $transaction->status }}</span></td></tr>
                    @empty<tr><td colspan="4" class="sdash-empty">No transactions recorded yet.</td></tr>@endforelse
                </tbody></table></div>
                <div class="sdash-panel-footer"><a class="sdash-view-all" href="{{ route('staff.transactions') }}">View All Transactions <span aria-hidden="true">→</span></a></div>
            </section>

            <section class="sdash-panel"><div class="sdash-panel-heading"><div><h2>My recent inventory requests</h2><p>Latest requests submitted by you.</p></div></div>
                <div class="sdash-table-wrap"><table class="sdash-table"><thead><tr><th>Item</th><th>Type</th><th>Submitted</th><th>Status</th></tr></thead><tbody>
                    @forelse ($recentRequests as $request)<tr><td class="sdash-primary-cell">{{ $request->item_name }}</td><td>{{ $request->type_label }}</td><td>{{ $day($request->created_at) }}</td><td><span class="sdash-status status-{{ strtolower($request->status) }}">{{ ucfirst($request->status) }}</span></td></tr>
                    @empty<tr><td colspan="4" class="sdash-empty">No inventory requests yet.</td></tr>@endforelse
                </tbody></table></div>
                <div class="sdash-panel-footer"><a class="sdash-view-all" href="{{ route('staff.inventory.requests') }}">View All Requests <span aria-hidden="true">→</span></a></div>
            </section>

            <section class="sdash-panel"><div class="sdash-panel-heading"><div><h2>My recent audits</h2><p>Latest audit sessions you created.</p></div></div>
                <div class="sdash-table-wrap"><table class="sdash-table"><thead><tr><th>Audit no.</th><th>Date</th><th>Status</th></tr></thead><tbody>
                    @forelse ($recentAudits as $audit)<tr><td class="sdash-primary-cell">{{ $audit->audit_number }}</td><td>{{ $day($audit->audit_date) }}</td><td><span class="sdash-status {{ $audit->isFinalized() ? 'status-completed' : 'status-pending' }}">{{ $audit->isFinalized() ? 'Finalized' : 'Draft' }}</span></td></tr>
                    @empty<tr><td colspan="3" class="sdash-empty">No audits recorded yet.</td></tr>@endforelse
                </tbody></table></div>
                <div class="sdash-panel-footer"><a class="sdash-view-all" href="{{ route('staff.audits') }}">View All Audits <span aria-hidden="true">→</span></a></div>
            </section>
        </div>
    </section>
</x-staff-layout>
