@extends('layouts.panel')

@section('title', 'Edit Inventory Item')

@section('content')
<div class="container" style="max-width:820px;">

    <div class="page-header">
        <div>
            <h1>Edit Inventory Item</h1>
            <p>{{ $product->sku }} &middot; {{ $product->product_name }}</p>
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

    <form method="POST" action="{{ route('products.update', $product) }}" class="card">
        @csrf
        @method('PUT')

        @include('inventory._form', ['product' => $product])

        <div class="form-actions">
            <button type="submit" class="btn green">Save Changes</button>
            <a href="{{ route('products.index') }}" class="btn gray">Cancel</a>
        </div>
    </form>

</div>
@endsection
