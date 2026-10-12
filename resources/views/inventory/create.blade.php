@extends('layouts.panel')

@section('title', 'Add Inventory Item')

@section('content')
<div class="container" style="max-width:820px;">

    <div class="page-header">
        <div>
            <h1>Add Inventory Item</h1>
            <p>This item will be available in new inventory audits.</p>
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

    <form method="POST" action="{{ route('products.store') }}" class="card">
        @csrf

        @include('inventory._form')

        <div class="form-actions">
            <button type="submit" class="btn green">Save Item</button>
            <a href="{{ route('products.index') }}" class="btn gray">Cancel</a>
        </div>
    </form>

</div>
@endsection
