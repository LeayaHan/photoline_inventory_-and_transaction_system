<x-manager-layout title="Inventory Audit Records - Photoline Abreeza">

@push('styles')
<style>
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

        th, td {
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

        .status.ongoing {
            background: #fef3c7;
            color: #92400e;
        }

        .status.completed {
            background: #d1fae5;
            color: #065f46;
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

        @media (max-width: 900px) {
.page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

        .table-card {
                overflow-x: auto;
            }

        table {
                min-width: 850px;
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
@endpush

<div class="container">

        <div class="page-header">

            <div>

                <h1>
                    Inventory Audit Records
                </h1>

                <p>
                    Review inventory audit records and inventory discrepancies.
                </p>

            </div>

        </div>


        <div class="search-box">

            <form
                method="GET"
                action="{{ route('manager.audits.index') }}"
            >

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by date, auditor, product, or SKU..."
                >

                <button
                    type="submit"
                    class="search-button"
                >
                    Search
                </button>

                @if(request('search'))

                    <a
                        href="{{ route('manager.audits.index') }}"
                        class="clear-button"
                    >
                        Clear
                    </a>

                @endif

            </form>

        </div>


        <div class="table-card">

            @if($audits->count())

                <table>

                    <thead>

                        <tr>

                            <th>
                                Audit ID
                            </th>

                            <th>
                                Audit Date
                            </th>

                            <th>
                                Auditor
                            </th>

                            <th>
                                Items
                            </th>

                            <th>
                                Discrepancies
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

                        @foreach($audits as $audit)

                            @php
                                $discrepancyCount = $audit->details
                                    ->where('discrepancy', '!=', 0)
                                    ->count();
                            @endphp

                            <tr>

                                <td>
                                    #{{ $audit->id }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($audit->audit_date)->format('M d, Y') }}
                                </td>

                                <td>
                                    {{ $audit->user->name ?? 'N/A' }}
                                </td>

                                <td>
                                    {{ $audit->details->count() }}
                                </td>

                                <td>
                                    {{ $discrepancyCount }}
                                </td>

                                <td>

                                    @if($audit->status === 'Completed')

                                        <span class="status completed">
                                            Completed
                                        </span>

                                    @else

                                        <span class="status ongoing">
                                            Ongoing
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    <a
                                        href="{{ route('manager.audits.show', $audit) }}"
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
                    {{ $audits->links() }}
                </div>

            @else

                <div class="empty">

                    No inventory audit records found.

                </div>

            @endif

        </div>

    </div>

</x-manager-layout>
