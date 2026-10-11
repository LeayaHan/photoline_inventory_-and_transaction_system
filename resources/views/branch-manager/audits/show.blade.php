<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/branch-manager/users.css') }}">
    <link rel="stylesheet" href="{{ asset('css/branch-manager/audits.css') }}">

    <div class="audit-manager-screen users-container audit-manager-page">
        <div class="users-header">
            <div>
                <h1 class="users-title">{{ $audit->audit_number }}</h1>
                <p class="users-subtitle">
                    {{ $audit->audit_date->format('M d, Y') }} · {{ $audit->auditor_name }} · {{ ucfirst($audit->status) }}
                </p>
            </div>
            <a class="users-btn users-btn-primary" href="{{ route('branch-manager.audits.index') }}">Back to Audits</a>
        </div>

        @if ($audit->isFinalized())
            <div class="users-alert users-alert-success">
                Finalized on {{ $audit->finalized_at?->format('M d, Y g:i A') }}. This record is read-only.
            </div>
        @else
            <div class="users-alert">This audit is still a draft. The auditor may continue counting items.</div>
        @endif

        <div class="users-card" style="overflow-x:auto">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Item</th>
                        <th>Recorded</th>
                        <th>Physical Count</th>
                        <th>Difference</th>
                        <th>Result</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($audit->items as $item)
                        <tr>
                            <td>{{ $item->item_code }}</td>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->recorded_quantity }} {{ $item->unit }}</td>
                            <td>{{ $item->physical_quantity ?? 'Not counted' }}</td>
                            <td>{{ is_null($item->physical_quantity) ? '—' : ($item->physical_quantity - $item->recorded_quantity) }}</td>
                            <td>
                                @if (is_null($item->physical_quantity))
                                    Not counted
                                @elseif ($item->physical_quantity < $item->recorded_quantity)
                                    Shortage
                                @elseif ($item->physical_quantity > $item->recorded_quantity)
                                    Excess
                                @else
                                    Match
                                @endif
                            </td>
                            <td>{{ $item->remarks ?: '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
