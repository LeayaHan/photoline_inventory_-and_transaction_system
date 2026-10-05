@extends('layouts.panel')

@section('title', 'Dashboard')

@section('content')
<div class="container">

    <div class="page-header">
        <div>
            <h1>Welcome, {{ auth()->user()->name }}</h1>
            <p>Photoline Inventory &amp; Transaction Management System</p>
        </div>
    </div>

    <div class="grid three" style="margin-bottom:22px;">
        <div class="card" style="margin:0;">
            <h2>Transactions</h2>
            <p style="color:var(--pl-muted);">Record and manage customer service transactions.</p>
            <a href="{{ route('transactions.create') }}" class="btn">+ New Transaction</a>
        </div>

        <div class="card" style="margin:0;">
            <h2>Inventory Audits</h2>
            <p style="color:var(--pl-muted);">Count physical stock against recorded quantities.</p>
            <a href="{{ route('audits.create') }}" class="btn green">+ New Audit</a>
        </div>

        <div class="card" style="margin:0;">
            <h2>Inventory</h2>
            <p style="color:var(--pl-muted);">Add and update the items kept in the branch.</p>
            <a href="{{ route('products.index') }}" class="btn orange">Manage Inventory</a>
        </div>
    </div>

    <div class="card table-wrap">

        <h2>Recent Transactions</h2>

        <table>
            <thead>
                <tr>
                    <th>Control Number</th>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->control_number }}</td>
                        <td>{{ $transaction->customer_name }}</td>
                        <td>{{ $transaction->service_type }}</td>
                        <td>{{ $transaction->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">No transactions yet.</td></tr>
                @endforelse
            </tbody>
        </table>

    </div>

</div>
@endsection
