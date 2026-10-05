@extends('layouts.panel')

@section('title', 'Edit Inventory Audit')

@push('styles')
<style>
    .qty { width: 120px; }
</style>
@endpush

@section('content')
<div class="container">

    <div class="page-header">
        <div>
            <h1>Edit Inventory Audit #{{ $audit->id }}</h1>
            <p>Update the audit date or the counted quantities.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="errors">
            <strong>Please fix the following:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('audits.update', $audit) }}">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="form-group" style="max-width:260px;">
                <label for="audit_date">Audit Date</label>
                <input type="date" id="audit_date" name="audit_date"
                       value="{{ old('audit_date', $audit->audit_date->format('Y-m-d')) }}" required>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>POS Quantity</th>
                            <th>Physical Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($audit->details as $index => $detail)
                            <tr>
                                <td>
                                    {{ $detail->product->product_name }}
                                    <input type="hidden" name="products[{{ $index }}][detail_id]" value="{{ $detail->id }}">
                                </td>
                                <td>{{ $detail->product->sku }}</td>
                                <td>
                                    <input type="number" class="qty" min="0"
                                           name="products[{{ $index }}][recorded_qty]"
                                           value="{{ old("products.$index.recorded_qty", $detail->recorded_qty) }}" required>
                                </td>
                                <td>
                                    <input type="number" class="qty" min="0"
                                           name="products[{{ $index }}][counted_qty]"
                                           value="{{ old("products.$index.counted_qty", $detail->counted_qty) }}" required>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn green">Save Changes</button>
                <a href="{{ route('audits.show', $audit) }}" class="btn gray">Cancel</a>
            </div>
        </div>
    </form>

</div>
@endsection
