<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/branch-manager/users.css') }}">
    <link rel="stylesheet" href="{{ asset('css/branch-manager/audits.css') }}">

    <div class="users-container audit-manager-page">
        <div class="users-header">
            <div>
                <h1 class="users-title">Inventory Audits</h1>
            </div>
        </div>

        <div class="audit-summary-grid">
            <div class="audit-summary">
                <span>Audits</span>
                <strong>{{ $summary['audits'] }}</strong>
            </div>

            <div class="audit-summary">
                <span>Items Counted</span>
                <strong>{{ $summary['items_checked'] }}</strong>
            </div>

            <div class="audit-summary">
                <span>Shortages</span>
                <strong>{{ $summary['shortages'] }}</strong>
            </div>

            <div class="audit-summary">
                <span>Excesses</span>
                <strong>{{ $summary['excesses'] }}</strong>
            </div>

            <div class="audit-summary">
                <span>Matched</span>
                <strong>{{ $summary['matched'] }}</strong>
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
                Status
                <select name="status">
                    <option value="">All (drafts and finalized)</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft (ongoing)</option>
                    <option value="finalized" {{ request('status') === 'finalized' ? 'selected' : '' }}>Finalized</option>
                </select>
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
                                {{ $audit->isFinalized() ? 'Finalized' : 'Draft (ongoing)' }}
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
                                No audit records found.
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
