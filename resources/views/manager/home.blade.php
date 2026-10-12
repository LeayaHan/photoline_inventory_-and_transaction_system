@extends('layouts.panel')

@section('title', 'Manager Dashboard')

@push('styles')
<style>
    .dash-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 34px;
    }
    .dash-head h1 { margin: 0 0 4px; font-size: 28px; line-height: 1.2; }
    .dash-head p { margin: 0; color: var(--pl-muted); }
    .dash-date { color: var(--pl-muted); font-size: 14px; }

    .dash-section + .dash-section { margin-top: 34px; }
    .dash-section h2 { margin: 0 0 4px; font-size: 18px; }
    .dash-section > p { margin: 0 0 16px; color: var(--pl-muted); font-size: 14px; }

    .dash-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }
    .dash-card {
        display: flex;
        flex-direction: column;
        background: #fff;
        border: 1px solid var(--pl-line);
        border-radius: 10px;
        padding: 22px;
        transition: border-color .15s;
    }
    .dash-card:hover { border-color: #bfdbfe; }
    .dash-card h3 { margin: 0 0 8px; font-size: 17px; }
    .dash-card p { margin: 0 0 20px; color: var(--pl-muted); font-size: 14px; }
    .dash-card .btn { margin-top: auto; align-self: flex-start; }

    @media (max-width: 760px) { .dash-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="container">

    <div class="dash-head">
        <div>
            <h1>Manager Dashboard</h1>
            <p>Branch management and operational oversight</p>
        </div>
        <div class="dash-date">{{ now()->format('l, j F Y') }}</div>
    </div>

    <section class="dash-section">
        <h2>Daily operations</h2>
        <p>What happens at the counter and on the shelves.</p>

        <div class="dash-grid">
            <div class="dash-card">
                <h3>Transactions</h3>
                <p>Search and review customer service transaction records handled by branch staff.</p>
                <a href="{{ route('manager.transactions.index') }}" class="btn">View transactions</a>
            </div>

            <div class="dash-card">
                <h3>Inventory audits</h3>
                <p>Review audit records, physical counts, POS quantities, and recorded discrepancies.</p>
                <a href="{{ route('manager.audits.index') }}" class="btn">View audits</a>
            </div>

            <div class="dash-card">
                <h3>Replenishment</h3>
                <p>Review branch replenishment requests and stock-related actions.</p>
                <a href="{{ route('replenishments.index') }}" class="btn">View replenishments</a>
            </div>
        </div>
    </section>

    <section class="dash-section">
        <h2>Records and setup</h2>
        <p>Summaries, the item list, and who can use the system.</p>

        <div class="dash-grid">
            <div class="dash-card">
                <h3>Reports</h3>
                <p>Review summarized transaction history and inventory discrepancy information.</p>
                <a href="{{ route('reports.index') }}" class="btn gray">View reports</a>
            </div>

            <div class="dash-card">
                <h3>Inventory</h3>
                <p>Add, update and remove the items that are counted in inventory audits.</p>
                <a href="{{ route('products.index') }}" class="btn gray">Manage inventory</a>
            </div>

            <div class="dash-card">
                <h3>Staff accounts</h3>
                <p>Create, update, and manage staff accounts authorized to use the system.</p>
                <a href="{{ route('users.index') }}" class="btn gray">Manage staff</a>
            </div>
        </div>
    </section>

</div>
@endsection