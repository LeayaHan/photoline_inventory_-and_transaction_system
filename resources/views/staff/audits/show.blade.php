@extends('layouts.panel')

@section('title', 'Inventory Audit #' . $audit->id)

@push('styles')
<style>
    .meta { display: flex; gap: 40px; flex-wrap: wrap; margin-bottom: 20px; }
    .meta .lbl { display: block; color: var(--pl-muted); font-size: 12px; text-transform: uppercase; margin-bottom: 3px; }
    .shortage { color: #991b1b; font-weight: bold; }
    .excess { color: #92400e; font-weight: bold; }
</style>
@endpush

@section('content')
<div class="container">

    <div class="page-header">
        <div>
            <h1>Inventory Audit #{{ $audit->id }}</h1>
        </div>
        <a href="{{ route('audits.index') }}" class="btn gray">&larr; Back to Audits</a>
    </div>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="meta">
            <div><span class="lbl">Audit Date</span>{{ $audit->audit_date->format('Y-m-d') }}</div>
            <div><span class="lbl">Auditor</span>{{ $audit->user->name }}</div>
            <div>
                <span class="lbl">Status</span>
                <span class="badge {{ $audit->status === 'Completed' ? 'green' : 'orange' }}">{{ $audit->status }}</span>
            </div>
        </div>

        @if($audit->status === 'Ongoing')
            <div class="form-actions" style="margin:0 0 20px;">
                <a href="{{ route('audits.edit', $audit) }}" class="btn orange">Edit Audit</a>

                <form method="POST" action="{{ route('audits.complete', $audit) }}"
                      onsubmit="return confirm('Mark this audit as completed? It can no longer be edited afterwards.');">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn green">Complete Audit</button>
                </form>
            </div>
        @endif

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>POS Quantity</th>
                        <th>Physical Count</th>
                        <th>Discrepancy</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($audit->details as $detail)
                        <tr>
                            <td>{{ $detail->product->product_name }}</td>
                            <td>{{ $detail->product->sku }}</td>
                            <td>{{ $detail->recorded_qty }}</td>
                            <td>{{ $detail->counted_qty }}</td>
                            <td>
                                @if($detail->discrepancy < 0)
                                    <span class="shortage">{{ $detail->discrepancy }} Shortage</span>
                                @elseif($detail->discrepancy > 0)
                                    <span class="excess">+{{ $detail->discrepancy }} Excess</span>
                                @else
                                    No Discrepancy
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
