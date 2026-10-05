@extends('layouts.panel')

@section('title', 'Transactions')

@push('styles')
<style>
        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .button,
        button {
            padding: 9px 14px;
            background: #222;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
        }
        .search {
            margin-bottom: 25px;
        }
        .search input {
            width: 300px;
            padding: 9px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background: #eee;
        }
        .actions a {
            margin-right: 10px;
        }
        .void-form {
            display: inline;
        }
        .success {
            background: #e2f5e9;
            border: 1px solid #9bd3ac;
            padding: 12px;
            margin-bottom: 20px;
        }
        .pagination {
            margin-top: 20px;
        }
    
</style>
@endpush

@section('content')
<div class="container">

    <div class="top">

        <h1>Customer Service Transactions</h1>

        <a href="{{ route('transactions.create') }}" class="button">
            + New Transaction
        </a>

    </div>

    @if(session('success'))

        <div class="success">
            {{ session('success') }}
        </div>

    @endif

    <div class="search">

        <form
            method="GET"
            action="{{ route('transactions.index') }}"
        >

            <input
                type="text"
                name="search"
                placeholder="Search control number or customer name"
                value="{{ request('search') }}"
            >

            <button type="submit">
                Search
            </button>

        </form>

    </div>

    <table>

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

                    <td>
                        {{ $transaction->control_number }}
                    </td>

                    <td>
                        {{ $transaction->customer_name }}
                    </td>

                    <td>
                        {{ $transaction->service_type }}
                    </td>

                    <td>
                        {{ $transaction->transaction_date->format('Y-m-d') }}
                    </td>

                    <td>
                        {{ $transaction->status }}
                    </td>

                    <td class="actions">

                        <a
                            href="{{ route('transactions.show', $transaction) }}"
                        >
                            View
                        </a>

                        <a
                            href="{{ route('transactions.edit', $transaction) }}"
                        >
                            Edit
                        </a>

                        <form
                            class="void-form"
                            method="POST"
                            action="{{ route('transactions.destroy', $transaction) }}"
                        >

                            @csrf

                            @method('DELETE')

                            <button type="submit">
                                Void
                            </button>

                        </form>

                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="6"
                        style="text-align: center;"
                    >
                        No transactions found.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

    <div class="pagination">

        {{ $transactions->links() }}

    </div>

</div>
@endsection
