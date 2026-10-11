<x-staff-layout title="Inventory Audits">
    <link rel="stylesheet" href="{{ asset('css/audits.css') }}">

    <div class="staffui-pagehead audit-pagehead">
        <div>
            <h1>Inventory Audits</h1>
            <p>Review your audits, continue unfinished counts, and view finalized reports.</p>
        </div>
        <a class="staffui-btn" href="{{ route('staff.audits.create') }}">Create Audit</a>
    </div>

    @if (session('success'))
        <div class="staffui-alert staffui-alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="staffui-alert">{{ session('error') }}</div>
    @endif

    <div class="audit-summary-grid">
        <div class="audit-summary"><span>Audits</span><strong>{{ $summary['audits'] }}</strong></div>
        <div class="audit-summary"><span>Items Counted</span><strong>{{ $summary['items_checked'] }}</strong></div>
        <div class="audit-summary"><span>Shortages</span><strong>{{ $summary['shortages'] }}</strong></div>
        <div class="audit-summary"><span>Excesses</span><strong>{{ $summary['excesses'] }}</strong></div>
        <div class="audit-summary"><span>Matched</span><strong>{{ $summary['matched'] }}</strong></div>
    </div>

    <form method="GET" class="audit-filters">
        <label>From <input type="date" name="date_from" value="{{ request('date_from') }}"></label>
        <label>To <input type="date" name="date_to" value="{{ request('date_to') }}"></label>
        <label>Product <input type="text" name="product" value="{{ request('product') }}" placeholder="Name or item code"></label>
        <label>Auditor <input type="text" name="auditor" value="{{ request('auditor') }}" placeholder="Auditor name"></label>
        <label>Status
            <select name="status">
                <option value="all" @selected(request('status', 'all') === 'all')>All statuses</option>
                <option value="draft" @selected(request('status') === 'draft')>In progress</option>
                <option value="finalized" @selected(request('status') === 'finalized')>Finalized</option>
            </select>
        </label>
        <button class="staffui-btn staffui-btn-outline" type="submit">Filter</button>
        <a class="audit-clear staffui-btn" href="{{ route('staff.audits') }}">Clear</a>
    </form>

    <div class="staffui-tablecard" style="overflow-x:auto">
        <table class="staffui-table">
            <thead>
                <tr><th>Audit No.</th><th>Date</th><th>Auditor</th><th>Items</th><th>Discrepancies</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
                @forelse ($audits as $audit)
                    <tr>
                        <td class="staffui-code">{{ $audit->audit_number }}</td>
                        <td>{{ $audit->audit_date->format('M d, Y') }}</td>
                        <td>{{ $audit->auditor_name }}</td>
                        <td>{{ $audit->items_count }}</td>
                        <td>{{ $audit->items->whereIn('status', ['Shortage', 'Excess'])->count() }}</td>
                        <td><span class="audit-status audit-status-{{ $audit->isFinalized() ? 'finalized' : 'in_progress' }}">{{ $audit->isFinalized() ? 'Finalized' : 'In Progress' }}</span></td>
                        <td>
                            <a class="staffui-btn staffui-btn-outline staffui-btn-sm" href="{{ route('staff.audits.show', $audit) }}">{{ $audit->isFinalized() ? 'View Report' : 'Edit Audit' }}</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="staffui-emptyrow">No audit records match your filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="staffui-pager">{{ $audits->links() }}</div>
</x-staff-layout>
