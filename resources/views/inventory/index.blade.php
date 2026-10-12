@extends('layouts.panel')

@section('title', 'Inventory')

@section('content')
<div class="container">

    <div class="page-header">
        <div>
            <h1>Inventory</h1>
            <p>Add, update and remove the items that are counted in inventory audits.</p>
        </div>
        <a href="{{ route('products.create') }}" class="btn">+ Add Item</a>
    </div>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    <form method="GET" action="{{ route('products.index') }}" class="filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search SKU, item name or category">
        <button type="submit" class="btn">Search</button>
        @if(request('search'))
            <a href="{{ route('products.index') }}" class="btn gray">Clear</a>
        @endif
    </form>

    <div class="card table-wrap">
        <table>
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Unit</th>
                    <th>In Stock</th>
                    <th>Reorder Level</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $product->sku }}</td>
                        <td>{{ $product->product_name }}</td>
                        <td>{{ $product->category }}</td>
                        <td>{{ $product->unit }}</td>
                        <td>
                            {{ $product->quantity }}
                            @if($product->isLowStock())
                                <span class="badge red">Low</span>
                            @endif
                        </td>
                        <td>{{ $product->reorder_level }}</td>
                        <td class="actions">
                            <a href="{{ route('products.edit', $product) }}">Edit</a>
                            <form method="POST" action="{{ route('products.destroy', $product) }}"
                                  onsubmit="return confirm('Delete this inventory item?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty">
                            No inventory items found.
                            <a href="{{ route('products.create') }}">Add your first item</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">{{ $products->links() }}</div>
    </div>

</div>
@endsection
