<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Transaction Details</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
        }

        nav {
            background: #222;
            padding: 15px 30px;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
        }

        .container {
            width: 80%;
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
        }

        .row {
            display: flex;
            margin-bottom: 15px;
        }

        .label {
            width: 180px;
            font-weight: bold;
        }

        .button {
            display: inline-block;
            padding: 10px 15px;
            background: #222;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 8px;
        }

        .back {
            background: #777;
        }

        .success {
            background: #e2f5e9;
            border: 1px solid #9bd3ac;
            padding: 12px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

<nav>
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <a href="{{ route('transactions.index') }}">Transactions</a>
    <a href="{{ route('audits.index') }}">Audits</a>
    <a href="{{ route('reports.index') }}">Reports</a>
</nav>

<div class="container">

    <h1>Transaction Details</h1>

    @if(session('success'))

        <div class="success">
            {{ session('success') }}
        </div>

    @endif

    <div class="row">

        <div class="label">
            Control Number:
        </div>

        <div>
            {{ $transaction->control_number }}
        </div>

    </div>

    <div class="row">

        <div class="label">
            Customer Name:
        </div>

        <div>
            {{ $transaction->customer_name }}
        </div>

    </div>

    <div class="row">

        <div class="label">
            Service Type:
        </div>

        <div>
            {{ $transaction->service_type }}
        </div>

    </div>

    <div class="row">

        <div class="label">
            Transaction Date:
        </div>

        <div>
            {{ $transaction->transaction_date->format('Y-m-d') }}
        </div>

    </div>

    <div class="row">

        <div class="label">
            Item Description:
        </div>

        <div>
            {{ $transaction->item_description ?: 'None' }}
        </div>

    </div>

    <div class="row">

        <div class="label">
            Status:
        </div>

        <div>
            {{ $transaction->status }}
        </div>

    </div>

    <div style="margin-top: 30px;">

        <a
            href="{{ route('transactions.edit', $transaction) }}"
            class="button"
        >
            Edit
        </a>

        <a
            href="{{ route('transactions.index') }}"
            class="button back"
        >
            Back
        </a>

    </div>

</div>

</body>
</html>