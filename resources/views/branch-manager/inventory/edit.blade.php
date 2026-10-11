<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/branch-manager/users.css') }}">
    <link rel="stylesheet" href="{{ asset('css/branch-manager/inventory.css') }}">

    <div class="users-container">
        <div class="users-header">
            <div>
                <h1 class="users-title">Edit Inventory Item</h1>
                <p class="users-subtitle">{{ $item->item_code }} &middot; currently {{ $item->quantity }} {{ $item->unit }} in stock.</p>
            </div>

            <a href="{{ route('branch-manager.inventory.index') }}" class="users-btn users-btn-secondary">Back to Inventory</a>
        </div>

        @if (session('success'))
            <div class="users-alert users-alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="users-alert users-alert-error">
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('branch-manager.inventory.update', ['item' => $item->id]) }}" method="POST" class="staff-form-card">
            @csrf
            @method('PUT')
            @include('branch-manager.inventory._form')

            <div class="staff-form-actions">
                <a href="{{ route('branch-manager.inventory.index') }}" class="users-btn users-btn-secondary">Cancel</a>
                <button type="submit" class="users-btn users-btn-primary">Update Item</button>
            </div>
        </form>

        <div class="inv-stock-card">
            <h2>Adjust Stock</h2>
            <p>Add or remove stock directly. Staff have to send a request for this, but you don't.</p>

            <form action="{{ route('branch-manager.inventory.adjust', ['item' => $item->id]) }}" method="POST" class="inv-stock-form">
                @csrf

                <div class="staff-field">
                    <label for="direction">Action</label>
                    <select id="direction" name="direction">
                        <option value="in">Stock in (add)</option>
                        <option value="out">Stock out (remove)</option>
                    </select>
                </div>

                <div class="staff-field">
                    <label for="adj_quantity">Quantity ({{ $item->unit }})</label>
                    <input id="adj_quantity" type="number" min="1" name="quantity" required>
                </div>

                <button type="submit" class="users-btn users-btn-primary">Apply</button>
            </form>
        </div>
    </div>
</x-app-layout>
