<x-staff-layout title="Audit Details">
    <link rel="stylesheet" href="{{ asset('css/audits.css') }}">

    <div class="staffui-pagehead audit-pagehead">
        <div>
            <h1>{{ $audit->audit_number }}</h1>
            <p>{{ $audit->audit_date->format('M d, Y') }} · Auditor: {{ $audit->auditor_name }} · Status: <strong>{{ $audit->isFinalized() ? 'Finalized' : 'In Progress' }}</strong></p>
        </div>
        <div class="audit-actions">
            @if ($audit->isFinalized())
                <button class="staffui-btn staffui-btn-outline" onclick="window.print()" type="button">Print Report</button>
            @endif
            <a class="staffui-btn staffui-btn-outline" href="{{ route('staff.audits') }}">Back to Audits</a>
        </div>
    </div>

    @if (session('success'))<div class="staffui-alert staffui-alert-success">{{ session('success') }}</div>@endif
    @if ($errors->any())<div class="staffui-alert">{{ $errors->first() }}</div>@endif
    @if ($audit->isFinalized())<div class="staffui-alert staffui-alert-success">This audit is finalized and read-only.</div>@endif

    <form id="audit-update-form" method="POST" action="{{ route('staff.audits.update', $audit) }}">
        @csrf
        @method('PUT')
        <div class="staffui-tablecard" style="overflow-x:auto">
            <table class="staffui-table">
                <thead><tr><th>Item Code</th><th>Item</th><th>Recorded</th><th>Physical Count</th><th>Difference</th><th>Result</th><th>Item Remarks</th></tr></thead>
                <tbody>
                    @foreach ($audit->items as $item)
                        @php
                            $physical = $item->physical_quantity;
                            $difference = is_null($physical) ? null : ((int) $physical - (int) $item->recorded_quantity);
                            $result = is_null($physical) ? 'Not Counted' : ($difference < 0 ? 'Shortage' : ($difference > 0 ? 'Excess' : 'Matched'));
                        @endphp
                        <tr>
                            <td>{{ $item->item_code }}</td>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->recorded_quantity }} {{ $item->unit }}</td>
                            <td>
                                @if ($audit->isFinalized())
                                    {{ $physical ?? '—' }} {{ $item->unit }}
                                @else
                                    <input type="number" min="0" step="1" name="items[{{ $item->id }}][physical_quantity]" value="{{ old('items.' . $item->id . '.physical_quantity', $physical) }}" aria-label="Physical count for {{ $item->item_name }}" style="width:125px;padding:8px;border:1px solid #d1d5db;border-radius:6px">
                                @endif
                            </td>
                            <td>{{ is_null($difference) ? '—' : (($difference > 0 ? '+' : '') . $difference . ' ' . $item->unit) }}</td>
                            <td>{{ $result }}</td>
                            <td>
                                @if ($audit->isFinalized())
                                    {{ $item->remarks ?: '—' }}
                                @else
                                    <input type="text" name="items[{{ $item->id }}][remarks]" value="{{ old('items.' . $item->id . '.remarks', $item->remarks) }}" aria-label="Remarks for {{ $item->item_name }}" style="width:180px;padding:8px;border:1px solid #d1d5db;border-radius:6px">
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>

    <div class="audit-form-actions" style="margin-top:16px">
            @if (! $audit->isFinalized())
                <button class="staffui-btn staffui-btn-outline" type="submit" form="audit-update-form">Save</button>
                <form id="finalize-audit-form" method="POST" action="{{ route('staff.audits.finalize', $audit) }}" data-confirm="Finalize this audit? It will become read-only." data-confirm-title="Finalize audit" data-confirm-ok="Finalize">
                    @csrf
                    <button class="staffui-btn" type="submit">Finalize Audit</button>
                </form>
            @endif
    </div>
@if (! $audit->isFinalized())
<script>
    document.getElementById('finalize-audit-form').addEventListener('submit', function (event) {
        const countInputs = document.querySelectorAll('#audit-update-form input[name$="[physical_quantity]"]');
        const missingCount = Array.from(countInputs).some(input => input.value.trim() === '');

        if (missingCount) {
            event.preventDefault();
            alert('Please enter a physical count for every inventory item before finalizing this audit.');
            return;
        }
    });
</script>
@endif
</x-staff-layout>
