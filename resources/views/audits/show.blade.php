<!DOCTYPE html>
<html>
<head>
    <title>Inventory Audit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }

        nav {
            margin-bottom: 30px;
        }

        nav a {
            margin-right: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        button {
            padding: 8px;
        }

        .success {
            padding: 10px;
            margin-bottom: 15px;
            background: #e8f5e9;
        }

        .shortage {
            font-weight: bold;
        }

        .excess {
            font-weight: bold;
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

<h1>Inventory Audit #{{ $audit->id }}</h1>

@if(session('success'))
    <div class="success">
        {{ session('success') }}
    </div>
@endif

<p>
    <strong>Audit Date:</strong>
    {{ $audit->audit_date->format('Y-m-d') }}
</p>

<p>
    <strong>Auditor:</strong>
    {{ $audit->user->name }}
</p>

<p>
    <strong>Status:</strong>
    {{ $audit->status }}
</p>

@if($audit->status === 'Ongoing')
    <a href="{{ route('audits.edit', $audit) }}">Edit Audit</a>

    <form
        method="POST"
        action="{{ route('audits.complete', $audit) }}"
        style="display:inline;"
    >
        @csrf
        @method('PUT')

        <button type="submit">Complete Audit</button>
    </form>
@endif

<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>SKU</th>
            <th>POS Quantity</th>
            <th>Physical Count</th>
            <th>Discrepancy</th>
        </tr>
    </thead>

    <tbody>
        @foreach($audit->details as $detail)
            <tr>
                <td>{{ $detail->product->product_name }}</td>
                <td>{{ $detail->product->sku }}</td>
                <td>{{ $detail->recorded_qty }}</td>
                <td>{{ $detail->counted_qty }}</td>
                <td>
                    @if($detail->discrepancy < 0)
                        <span class="shortage">
                            {{ $detail->discrepancy }} Shortage
                        </span>
                    @elseif($detail->discrepancy > 0)
                        <span class="excess">
                            +{{ $detail->discrepancy }} Excess
                        </span>
                    @else
                        No Discrepancy
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<br>

<a href="{{ route('audits.index') }}">Back to Audits</a>

</body>
</html>