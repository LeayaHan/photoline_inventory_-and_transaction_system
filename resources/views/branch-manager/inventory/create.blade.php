<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/branch-manager/users.css') }}">
    <link rel="stylesheet" href="{{ asset('css/branch-manager/inventory.css') }}">

    <div class="users-container">
        <div class="users-header">
            <div>
                <h1 class="users-title">Add Inventory Item</h1>
                <p class="users-subtitle">Add a new item to your branch inventory.</p>
            </div>

            <a href="{{ route('branch-manager.inventory.index') }}" class="users-btn users-btn-secondary">Back to Inventory</a>
        </div>

        @if ($errors->any())
            <div class="users-alert users-alert-error">
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('branch-manager.inventory.store') }}" method="POST" class="staff-form-card">
            @csrf
            @include('branch-manager.inventory._form')

            <div class="staff-form-actions">
                <a href="{{ route('branch-manager.inventory.index') }}" class="users-btn users-btn-secondary">Cancel</a>
                <button type="submit" class="users-btn users-btn-primary">Save Item</button>
            </div>
        </form>
    </div>
</x-app-layout>
