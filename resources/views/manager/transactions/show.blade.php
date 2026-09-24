<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Transaction Details - Photoline Abreeza</title>

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
            max-width: 1000px;

            margin: 35px auto;
        }

        .page-header {
            margin-bottom: 25px;
        }

        .page-header h1 {
            margin: 0 0 8px;
        }

        .page-header p {
            margin: 0;
            color: #6b7280;
        }

        .card {
            background: white;

            padding: 25px;

            border-radius: 10px;

            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);

            margin-bottom: 20px;
        }

        .card h2 {
            margin-top: 0;
        }

        .details {
            display: grid;

            grid-template-columns: repeat(2, 1fr);

            gap: 18px;
        }

        .detail {
            padding: 15px;

            background: #f9fafb;

            border-radius: 7px;
        }

        .detail-label {
            display: block;

            font-size: 12px;

            color: #6b7280;

            margin-bottom: 6px;
        }

        .detail-value {
            font-size: 16px;

            font-weight: bold;
        }

        .status {
            display: inline-block;

            padding: 6px 12px;

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

        .description {
            line-height: 1.6;

            white-space: pre-wrap;
        }

        .back-button {
            display: inline-block;

            padding: 10px 16px;

            background: #374151;
            color: white;

            text-decoration: none;

            border-radius: 6px;
        }

        @media (max-width: 700px) {

            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-links {
                flex-wrap: wrap;
            }

            .details {
                grid-template-columns: 1fr;
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

            <h1>
                Transaction Details
            </h1>

            <p>
                Read-only transaction record for Branch Manager review.
            </p>

        </div>


        <div class="card">

            <h2>
                Transaction Information
            </h2>

            <div class="details">

                <div class="detail">

                    <span class="detail-label">
                        Control Number
                    </span>

                    <span class="detail-value">
                        {{ $transaction->control_number }}
                    </span>

                </div>


                <div class="detail">

                    <span class="detail-label">
                        Customer Name
                    </span>

                    <span class="detail-value">
                        {{ $transaction->customer_name }}
                    </span>

                </div>


                <div class="detail">

                    <span class="detail-label">
                        Service Type
                    </span>

                    <span class="detail-value">
                        {{ $transaction->service_type }}
                    </span>

                </div>


                <div class="detail">

                    <span class="detail-label">
                        Transaction Date
                    </span>

                    <span class="detail-value">
                        {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}
                    </span>

                </div>


                <div class="detail">

                    <span class="detail-label">
                        Status
                    </span>

                    <span>

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

                    </span>

                </div>


                <div class="detail">

                    <span class="detail-label">
                        Created
                    </span>

                    <span class="detail-value">
                        {{ $transaction->created_at?->format('M d, Y h:i A') }}
                    </span>

                </div>

            </div>

        </div>


        <div class="card">

            <h2>
                Item / Service Description
            </h2>

            <div class="description">

                {{ $transaction->item_description ?: 'No description provided.' }}

            </div>

        </div>


        <a
            href="{{ route('manager.transactions.index') }}"
            class="back-button"
        >
            Back to Transactions
        </a>

    </div>

</body>

</html>