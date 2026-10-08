<x-staff-layout title="Dashboard">

@push('styles')
<style>
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

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #eee;
        }
</style>
@endpush

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

</div>

</x-staff-layout>
