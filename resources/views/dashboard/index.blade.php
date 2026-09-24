<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard</title>

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

        nav form {
            display: inline;
        }

        nav button {
            background: none;
            border: none;
            color: white;
            font-family: Arial, sans-serif;
            font-size: 16px;
            cursor: pointer;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 40px auto;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #eee;
        }

    </style>

</head>

<body>

<nav>

    <a href="{{ route('dashboard') }}">
        Dashboard
    </a>

    <a href="{{ route('transactions.index') }}">
        Transactions
    </a>

    <a href="{{ route('audits.index') }}">
        Audits
    </a>

    <a href="{{ route('reports.index') }}">
        Reports
    </a>

    <form
        method="POST"
        action="{{ route('logout') }}"
    >

        @csrf

        <button type="submit">
            Logout
        </button>

    </form>

</nav>

<div class="container">

    <div class="card">

        <h1>
            Photoline Inventory & Transaction Management System
        </h1>

        <p>
            Dashboard
        </p>

    </div>

    <div class="card">

        <h2>
            Recent Transactions
        </h2>

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
                            {{ $transaction->status }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="4"
                            style="text-align:center;"
                        >
                            No transactions yet.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="card">

        <h2>
            System Modules
        </h2>

        <p>
            <a href="{{ route('transactions.index') }}">
                Customer Service Transactions
            </a>
        </p>

        <p>
            <a href="{{ route('audits.index') }}">
                Inventory Audits
            </a>
        </p>

        <p>
            <a href="{{ route('reports.index') }}">
                Reports
            </a>
        </p>

    </div>

</div>

</body>

</html>