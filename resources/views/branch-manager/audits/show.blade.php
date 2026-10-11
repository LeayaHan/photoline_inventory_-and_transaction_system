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
            <a class="users-btn users-btn-secondary" href="{{ route('branch-manager.audits.index') }}">Back to Audits</a>
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

        @if ($audit->isFinalized())
            <p style="margin-top:16px">
                <button class="users-btn users-btn-primary" onclick="window.print()" type="button">Print Audit Report</button>
            </p>
        @endif
    </div>

    @if ($audit->isFinalized())
        <section class="audit-print-report" aria-label="Printable inventory audit report">
            <header class="audit-print-header">
                <div class="audit-print-brand">PHOTOLINE</div>
                <h1>INVENTORY AUDIT REPORT</h1>
                <p>Inventory and Transaction Management System</p>
            </header>

            <div class="audit-print-details">
                <div><strong>Audit Number:</strong> {{ $audit->audit_number }}</div>
                <div><strong>Audit Date:</strong> {{ $audit->audit_date->format('F d, Y') }}</div>
                <div><strong>Auditor:</strong> {{ $audit->auditor_name }}</div>
                <div><strong>Status:</strong> Finalized</div>
                <div><strong>Items in Audit:</strong> {{ $audit->items->count() }}</div>
                <div><strong>Finalized On:</strong> {{ $audit->finalized_at?->format('F d, Y g:i A') ?? '—' }}</div>
            </div>

            <h2 class="audit-print-section-title">Inventory Count Details</h2>
            <table class="audit-print-table">
                <thead>
                    <tr>
                        <th>Item Code</th>
                        <th>Product Name</th>
                        <th>Recorded Qty.</th>
                        <th>Physical Qty.</th>
                        <th>Variance</th>
                        <th>Result</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($audit->items as $item)
                        @php
                            $variance = is_null($item->physical_quantity) ? null : $item->physical_quantity - $item->recorded_quantity;
                        @endphp
                        <tr>
                            <td>{{ $item->item_code }}</td>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->recorded_quantity }} {{ $item->unit }}</td>
                            <td>{{ $item->physical_quantity ?? '—' }}</td>
                            <td>{{ is_null($variance) ? '—' : ($variance > 0 ? '+' : '') . $variance }}</td>
                            <td>
                                @if (is_null($item->physical_quantity))
                                    Not counted
                                @elseif ($variance < 0)
                                    Shortage
                                @elseif ($variance > 0)
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

            <footer class="audit-print-signatures">
                <div><span></span><strong>Auditor's Signature</strong><small>{{ $audit->auditor_name }}</small></div>
                <div><span></span><strong>Reviewed By</strong><small>Branch Manager</small></div>
            </footer>
            <div class="audit-print-footer">Photoline Inventory &amp; Transaction Management System · Inventory Audit Report</div>
        </section>
    @endif
</x-app-layout>
