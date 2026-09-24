<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manager Dashboard - Photoline Abreeza</title>

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
        }

        .navbar h2 {
            margin: 0;
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

        .welcome {
            margin-bottom: 30px;
        }

        .welcome h1 {
            margin: 0 0 8px;
        }

        .welcome p {
            margin: 0;

            color: #6b7280;
        }

        .grid {
            display: grid;

            grid-template-columns: repeat(2, 1fr);

            gap: 20px;
        }

        .card {
            background: white;

            border-radius: 10px;

            padding: 25px;

            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }

        .card h2 {
            margin-top: 0;
            margin-bottom: 10px;
        }

        .card p {
            color: #6b7280;

            line-height: 1.5;
        }

        .button {
            display: inline-block;

            margin-top: 12px;

            padding: 10px 16px;

            background: #2563eb;

            color: white;

            text-decoration: none;

            border-radius: 6px;
        }

        .button.green {
            background: #059669;
        }

        .button.secondary {
            background: #374151;
        }

        .button.orange {
            background: #d97706;
        }

        @media (max-width: 700px) {

            .grid {
                grid-template-columns: 1fr;
            }

            .navbar {
                flex-direction: column;

                gap: 15px;

                align-items: flex-start;
            }
        }
    </style>
</head>

<body>

    <div class="navbar">

        <h2>
            Photoline Abreeza
        </h2>

        <div class="user-area">

            <span>
                {{ auth()->user()->name }} — Branch Manager
            </span>

            <form
                method="POST"
                action="{{ route('logout') }}"
            >

                @csrf

                <button
                    type="submit"
                    class="logout-button"
                >
                    Logout
                </button>

            </form>

        </div>

    </div>


    <div class="container">

        <div class="welcome">

            <h1>
                Manager Dashboard
            </h1>

            <p>
                Branch management and operational oversight
            </p>

        </div>


        <div class="grid">


            {{-- TRANSACTIONS --}}

            <div class="card">

                <h2>
                    Transactions
                </h2>

                <p>
                    Search and review customer service transaction
                    records handled by branch staff.
                </p>

                <a
                    href="{{ route('manager.transactions.index') }}"
                    class="button"
                >
                    View Transactions
                </a>

            </div>


            {{-- INVENTORY AUDITS --}}

            <div class="card">

                <h2>
                    Inventory Audits
                </h2>

                <p>
                    Review inventory audit records, physical counts,
                    POS quantities, and recorded discrepancies.
                </p>

                <a
                    href="{{ route('manager.audits.index') }}"
                    class="button green"
                >
                    View Audits
                </a>

            </div>


            {{-- REPORTS --}}

            <div class="card">

                <h2>
                    Reports
                </h2>

                <p>
                    Review summarized transaction history and
                    inventory discrepancy information.
                </p>

                <a
                    href="{{ route('reports.index') }}"
                    class="button secondary"
                >
                    View Reports
                </a>

            </div>


            {{-- STAFF ACCOUNTS --}}

            <div class="card">

                <h2>
                    Staff Accounts
                </h2>

                <p>
                    Create, update, and manage staff accounts
                    authorized to use the system.
                </p>

                <a
                    href="{{ route('users.index') }}"
                    class="button orange"
                >
                    Manage Staff
                </a>

            </div>


            {{-- REPLENISHMENT --}}

            <div class="card">

                <h2>
                    Replenishment
                </h2>

                <p>
                    Review branch replenishment requests
                    and stock-related actions.
                </p>

                <a
                    href="{{ route('replenishments.index') }}"
                    class="button"
                >
                    Replenishments
                </a>

            </div>


        </div>

    </div>

</body>

</html>