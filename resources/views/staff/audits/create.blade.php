@extends('layouts.panel')

@section('title', 'New Inventory Audit')

@push('styles')
<style>
    .qty { width: 120px; }
</style>
@endpush

@section('content')
<div class="container">

    <div class="page-header">
        <div>
            <h1>New Inventory Audit</h1>
            <p>The POS quantity is filled in from your inventory. Enter the physical count for each item.</p>
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

    @if($products->isEmpty())
        <div class="card">
            <p class="empty">
                There are no inventory items yet.
                <a href="{{ route('products.create') }}">Add an inventory item</a> before starting an audit.
            </p>
        </div>
    @else
        <form method="POST" action="{{ route('audits.store') }}">
            @csrf

            <div class="card">
                <div class="form-group" style="max-width:260px;">
                    <label for="audit_date">Audit Date</label>
                    <input type="date" id="audit_date" name="audit_date"
                           value="{{ old('audit_date', date('Y-m-d')) }}" required>
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
                            @foreach($products as $index => $product)
                                <tr>
                                    <td>
                                        {{ $product->product_name }}
                                        <input type="hidden" name="products[{{ $index }}][product_id]" value="{{ $product->id }}">
                                    </td>
                                    <td>{{ $product->sku }}</td>
                                    <td>
                                        <input type="number" class="qty" min="0"
                                               name="products[{{ $index }}][recorded_qty]"
                                               value="{{ old("products.$index.recorded_qty", $product->quantity) }}" required>
                                    </td>
                                    <td>
                                        <input type="number" class="qty" min="0"
                                               name="products[{{ $index }}][counted_qty]"
                                               value="{{ old("products.$index.counted_qty") }}" required>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn green">Create Audit</button>
                    <a href="{{ route('audits.index') }}" class="btn gray">Cancel</a>
                </div>
            </div>
        </form>
    @endif

</div>
@endsection
