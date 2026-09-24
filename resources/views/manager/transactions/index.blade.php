<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Transaction Records - Photoline Abreeza</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f6f8;
            color: #1f2937;
        }

        .navbar {
            background: #111827;
            color: white;
            padding: 18px 30px;

            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .navbar h2 {
            margin: 0;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 14px;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }

        .user-area {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .user-area span {
            font-size: 14px;
        }

        .logout-button {
            background: #dc2626;
            color: white;
            border: none;

            padding: 9px 14px;
            border-radius: 6px;

            cursor: pointer;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 35px auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;

            margin-bottom: 25px;
        }

        .page-header h1 {
            margin: 0 0 8px;
        }

        .page-header p {
            margin: 0;
            color: #6b7280;
        }

        .back-button {
            display: inline-block;

            padding: 10px 16px;

            background: #374151;
            color: white;

            text-decoration: none;
            border-radius: 6px;
        }

        .search-box {
            background: white;
            padding: 20px;

            border-radius: 10px;

            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);

            margin-bottom: 25px;
        }

        .search-box form {
            display: flex;
            gap: 10px;
        }

        .search-box input {
            flex: 1;

            padding: 11px 12px;

            border: 1px solid #d1d5db;
            border-radius: 6px;

            font-size: 14px;
        }

        .search-button {
            padding: 11px 18px;

            background: #2563eb;
            color: white;

            border: none;
            border-radius: 6px;

            cursor: pointer;
        }

        .clear-button {
            display: inline-block;

            padding: 11px 18px;

            background: #6b7280;
            color: white;

            text-decoration: none;

            border-radius: 6px;
        }

        .table-card {
            background: white;

            border-radius: 10px;

            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);

            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 14px 16px;

            text-align: left;

            border-bottom: 1px solid #e5e7eb;

            font-size: 14px;
        }

        th {
            background: #f9fafb;
            font-weight: bold;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .status {
            display: inline-block;

            padding: 5px 10px;

            border-radius: 20px;

            font-size: 12px;
            font-weight: bold;
        }

        .status.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status.claimed {
            background: #d1fae5;
            color: #065f46;
        }

        .status.voided {
            background: #fee2e2;
            color: #991b1b;
        }

        .view-button {
            display: inline-block;

            padding: 7px 12px;

            background: #2563eb;
            color: white;

            text-decoration: none;

            border-radius: 5px;

            font-size: 13px;
        }

        .empty {
            padding: 40px;

            text-align: center;

            color: #6b7280;
        }

        .pagination {
            padding: 20px;
        }

        @media (max-width: 1000px) {

            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-links {
                flex-wrap: wrap;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .table-card {
                overflow-x: auto;
            }

            table {
                min-width: 900px;
            }
        }

        @media (max-width: 600px) {

            .search-box form {
                flex-direction: column;
            }

            .container {
                width: 94%;
            }
        }
    </style>
</head>

<body>

    <div class="navbar">

        <h2>
            Photoline Abreeza
        </h2>

        <div class="nav-links">

            <a href="{{ route('dashboard') }}">
                Dashboard
            </a>

            <a href="{{ route('manager.transactions.index') }}">
                Transactions
            </a>

            <a href="{{ route('manager.audits.index') }}">
                Inventory Audits
            </a>

            <a href="{{ route('reports.index') }}">
                Reports
            </a>

        </div>

        <div class="user-area">

            <span>
                {{ auth()->user()->name }} — Branch Manager
            </span>

            <form method="POST" action="{{ route('logout') }}">

                @csrf

                <button type="submit" class="logout-button">
                    Logout
                </button>

            </form>

        </div>

    </div>


    <div class="container">

        <div class="page-header">

            <div>

                <h1>
                    Transaction Records
                </h1>

                <p>
                    Search and review customer service transaction records.
                </p>

            </div>

            <a
                href="{{ route('dashboard') }}"
                class="back-button"
            >
                Back to Dashboard
            </a>

        </div>


        <div class="search-box">

            <form
                method="GET"
                action="{{ route('manager.transactions.index') }}"
            >

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by control number or customer name..."
                >

                <button
                    type="submit"
                    class="search-button"
                >
                    Search
                </button>

                @if(request('search'))

                    <a
                        href="{{ route('manager.transactions.index') }}"
                        class="clear-button"
                    >
                        Clear
                    </a>

                @endif

            </form>

        </div>


        <div class="table-card">

            @if($transactions->count())

                <table>

                    <thead>

                        <tr>

                            <th>
                                Control Number
                            </th>

                            <th>
                                Customer
                            </th>

                            <th>
                                Service
                            </th>

                            <th>
                                Transaction Date
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($transactions as $transaction)

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
                                    {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}
                                </td>

                                <td>

                                    @if($transaction->status === 'Pending')

                                        <span class="status pending">
                                            Pending
                                        </span>

                                    @elseif($transaction->status === 'Claimed')

                                        <span class="status claimed">
                                            Claimed
                                        </span>

                                    @else

                                        <span class="status voided">
                                            {{ $transaction->status }}
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    <a
                                        href="{{ route('manager.transactions.show', $transaction) }}"
                                        class="view-button"
                                    >
                                        View Details
                                    </a>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

                <div class="pagination">
                    {{ $transactions->links() }}
                </div>

            @else

                <div class="empty">
                    No transaction records found.
                </div>

            @endif

        </div>

    </div>

</body>

</html>