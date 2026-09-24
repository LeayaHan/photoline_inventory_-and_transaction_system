<!DOCTYPE html>
<html>
<head>
    <title>Edit Inventory Audit</title>
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

        input, button {
            padding: 8px;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<nav>
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <a href="{{ route('transactions.index') }}">Transactions</a>
    <a href="{{ route('audits.index') }}">Audits</a>
</nav>

<h1>Edit Inventory Audit #{{ $audit->id }}</h1>

@if($errors->any())
    <div class="error">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('audits.update', $audit) }}">
    @csrf
    @method('PUT')

    <div>
        <label>Audit Date</label>

        <input
            type="date"
            name="audit_date"
            value="{{ old('audit_date', $audit->audit_date->format('Y-m-d')) }}"
            required
        >
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th>POS Quantity</th>
                <th>Physical Count</th>
            </tr>
        </thead>

        <tbody>
            @foreach($audit->details as $index => $detail)
                <tr>
                    <td>
                        {{ $detail->product->product_name }}

                        <input
                            type="hidden"
                            name="products[{{ $index }}][detail_id]"
                            value="{{ $detail->id }}"
                        >
                    </td>

                    <td>
                        {{ $detail->product->sku }}
                    </td>

                    <td>
                        <input
                            type="number"
                            name="products[{{ $index }}][recorded_qty]"
                            min="0"
                            value="{{ old("products.$index.recorded_qty", $detail->recorded_qty) }}"
                            required
                        >
                    </td>

                    <td>
                        <input
                            type="number"
                            name="products[{{ $index }}][counted_qty]"
                            min="0"
                            value="{{ old("products.$index.counted_qty", $detail->counted_qty) }}"
                            required
                        >
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br>

    <button type="submit">Save Changes</button>

    <a href="{{ route('audits.show', $audit) }}">Cancel</a>
</form>

</body>
</html>