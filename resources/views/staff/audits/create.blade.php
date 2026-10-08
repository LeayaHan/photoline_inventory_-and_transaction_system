<x-staff-layout title="Create Inventory Audit">

@push('styles')
<style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        input, button {
            padding: 8px;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .page-card {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
        }
</style>
@endpush

<div class="page-card">

<h1>Create Inventory Audit</h1>

@if($errors->any())
    <div class="error">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('audits.store') }}">
    @csrf

    <label>Audit Date</label>
    <input
        type="date"
        name="audit_date"
        value="{{ old('audit_date', date('Y-m-d')) }}"
        required
    >

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
            @forelse($products as $index => $product)
                <tr>
                    <td>
                        {{ $product->product_name }}

                        <input
                            type="hidden"
                            name="products[{{ $index }}][product_id]"
                            value="{{ $product->id }}"
                        >
                    </td>

                    <td>
                        {{ $product->sku }}
                    </td>

                    <td>
                        <input
                            type="number"
                            name="products[{{ $index }}][recorded_qty]"
                            min="0"
                            value="{{ old("products.$index.recorded_qty", 0) }}"
                            required
                        >
                    </td>

                    <td>
                        <input
                            type="number"
                            name="products[{{ $index }}][counted_qty]"
                            min="0"
                            value="{{ old("products.$index.counted_qty", 0) }}"
                            required
                        >
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        No products available.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <br>

    <button type="submit">Create Audit</button>

    <a href="{{ route('audits.index') }}">Cancel</a>
</form>

</div>

</x-staff-layout>
