<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/branch-manager/users.css') }}">
    <link rel="stylesheet" href="{{ asset('css/branch-manager/audits.css') }}">

    <div class="users-container audit-manager-page">
        <div class="users-header">
            <div>
                <h1 class="users-title">Inventory Audits</h1>
            </div>
        </div>

        <form method="GET" class="audit-filters audit-manager-filters">
            <label>
                From
                <input
                    type="date"
                    name="date_from"
                    value="{{ request('date_from') }}"
                >
            </label>

            <label>
                To
                <input
                    type="date"
                    name="date_to"
                    value="{{ request('date_to') }}"
                >
            </label>

            <label>
                Product
                <input
                    type="text"
                    name="product"
                    value="{{ request('product') }}"
                    placeholder="Name or item code"
                >
            </label>

            <label>
                Auditor
                <input
                    type="text"
                    name="auditor"
                    value="{{ request('auditor') }}"
                    placeholder="Auditor name"
                >
            </label>

            <button
                class="users-btn users-btn-primary"
                type="submit"
            >
                Filter
            </button>

            <a
                class="audit-clear"
                href="{{ route('branch-manager.audits.index') }}"
            >
                Clear
            </a>
        </form>

        <div class="users-card inv-table-wrap">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Audit No.</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Auditor</th>
                        <th>Items Counted</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($audits as $audit)
                        <tr>
                            <td class="inv-code">
                                {{ $audit->audit_number }}
                            </td>

                            <td>
                                {{ $audit->audit_date->format('M d, Y') }}
                            </td>

                            <td>
                                Finalized
                            </td>

                            <td>
                                {{ $audit->auditor_name }}
                            </td>

                            <td>
                                {{ $audit->counted_items_count }}
                                /
                                {{ $audit->items_count }}
                            </td>

                            <td>
                                <a
                                    class="users-btn users-btn-primary"
                                    href="{{ route('branch-manager.audits.show', $audit) }}"
                                >
                                    View Audit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="users-empty">
                                No finalized audits found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="users-pagination">
            {{ $audits->links() }}
        </div>
    </div>
</x-app-layout>
