<x-staff-layout title="Inventory Audits | Photoline">

@push('styles')
<style>
    .staff-page {
        width: min(1180px, calc(100% - 36px));
        margin: 0 auto;
        padding: 32px 0 55px;
    }

    .page-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 18px;
        margin-bottom: 18px;
    }

    .page-heading small {
        color: #ed2b24;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.4px;
        text-transform: uppercase;
    }

    .page-heading h1 {
        margin: 5px 0 0;
        color: #13233f;
        font-size: 29px;
        letter-spacing: -.7px;
    }

    .page-heading p {
        margin: 7px 0 0;
        color: #7b8799;
        font-size: 12px;
    }

    .primary-action,
    .secondary-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 0 14px;
        border: 0;
        border-radius: 8px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .primary-action {
        color: #fff;
        background: #1769e8;
    }

    .primary-action:hover {
        background: #0f58c8;
    }

    .secondary-action {
        color: #536078;
        background: #eef3f9;
    }

    .notice {
        padding: 12px 14px;
        margin-bottom: 14px;
        border-radius: 9px;
        font-size: 12px;
    }

    .notice.success {
        color: #206c43;
        background: #ebf8f0;
        border: 1px solid #ccebd9;
    }

    .notice.error {
        color: #a72520;
        background: #fff0ef;
        border: 1px solid #f5cecb;
    }

    .toolbar {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 14px;
        padding: 14px;
        border: 1px solid #e5eaf2;
        border-radius: 12px;
        background: #fff;
    }

    .search-input {
        flex: 1;
        min-height: 40px;
        padding: 0 12px;
        border: 1px solid #dbe2ed;
        border-radius: 8px;
        outline: none;
        font-size: 12px;
    }

    .search-input:focus {
        border-color: #1769e8;
        box-shadow: 0 0 0 3px rgba(23, 105, 232, .09);
    }

    .table-card {
        overflow: hidden;
        border: 1px solid #e5eaf2;
        border-radius: 14px;
        background: #fff;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th,
    .data-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #edf0f5;
        text-align: left;
        font-size: 12px;
    }

    .data-table th {
        color: #7b8799;
        background: #fafbfd;
        font-size: 10px;
        letter-spacing: .6px;
        text-transform: uppercase;
    }

    .data-table tr:last-child td {
        border-bottom: 0;
    }

    .audit-id {
        color: #1769e8;
        font-weight: 800;
    }

    .status {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
    }

    .status.ongoing {
        color: #1769e8;
        background: #eaf2ff;
    }

    .status.completed {
        color: #20754a;
        background: #eaf8f0;
    }

    .row-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        white-space: nowrap;
    }

    .row-actions a {
        color: #1769e8;
        text-decoration: none;
        font-size: 11px;
        font-weight: 700;
    }

    .row-actions a:hover {
        text-decoration: underline;
    }

    .empty-state {
        padding: 38px 20px !important;
        color: #7b8799;
        text-align: center !important;
    }

    .pagination {
        margin-top: 18px;
    }

    @media (max-width: 800px) {
        .page-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .toolbar {
            flex-wrap: wrap;
        }

        .search-input {
            flex-basis: 100%;
        }
    }
</style>
@endpush

<div class="staff-page">

    <div class="page-heading">
        <div>
            <small>Inventory Control</small>

            <h1>Inventory Audits</h1>

            <p>
                Review physical counts and track inventory discrepancies.
            </p>
        </div>

        <a href="{{ route('audits.create') }}" class="primary-action">
            + Create Audit
        </a>
    </div>

    @if(session('success'))
        <div class="notice success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="notice error">
            {{ session('error') }}
        </div>
    @endif

    <form method="GET" action="{{ route('audits.index') }}" class="toolbar">

        <input
            class="search-input"
            type="search"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search by audit date or auditor"
        >

        <button type="submit" class="secondary-action">
            Search
        </button>

        @if(request('search'))
            <a
                href="{{ route('audits.index') }}"
                class="secondary-action"
            >
                Clear
            </a>
        @endif

    </form>

    <div class="table-card">

        <div style="overflow-x:auto;">

            <table class="data-table">

                <thead>
                    <tr>
                        <th>Audit ID</th>
                        <th>Audit Date</th>
                        <th>Auditor</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($audits as $audit)

                        <tr>

                            <td class="audit-id">
                                #{{ $audit->id }}
                            </td>

                            <td>
                                {{ $audit->audit_date->format('M d, Y') }}
                            </td>

                            <td>
                                {{ $audit->user->name }}
                            </td>

                            <td>

                                <span class="status {{ strtolower($audit->status) }}">
                                    {{ $audit->status }}
                                </span>

                            </td>

                            <td>

                                <div class="row-actions">

                                    <a href="{{ route('audits.show', $audit) }}">
                                        View
                                    </a>

                                    @if($audit->status === 'Ongoing')

                                        <a href="{{ route('audits.edit', $audit) }}">
                                            Edit
                                        </a>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="empty-state">
                                No inventory audits found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <div class="pagination">
        {{ $audits->links() }}
    </div>

</div>

</x-staff-layout>