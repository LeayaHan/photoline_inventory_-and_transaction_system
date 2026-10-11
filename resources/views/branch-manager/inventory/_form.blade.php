@php($item = $item ?? null)

<div class="staff-form-section">
    <h2 class="staff-section-title">Item Details</h2>

    <div class="staff-contact-grid">
        <div class="staff-field">
            <label for="name">Item Name </label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name', $item?->name) }}"
                placeholder="e.g. 4R Glossy Photo Paper" required>
        </div>

        <div class="staff-field">
            <label for="category">Category </label>
            <select id="category" name="category" required>
                <option value="" disabled {{ old('category', $item?->category) ? '' : 'selected' }}>Select category</option>
                @foreach (\App\Models\InventoryItem::CATEGORIES as $cat)
                    <option value="{{ $cat }}" {{ old('category', $item?->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>

        <div class="staff-field">
            <label for="unit">Unit </label>
            <input
                id="unit"
                type="text"
                name="unit"
                value="{{ old('unit', $item?->unit ?? 'pcs') }}"
                placeholder="pcs, packs, bottles..." required>
        </div>

        <div class="staff-field">
            <label for="unit_price">Unit Price (₱) </label>
            <input
                id="unit_price"
                type="number" step="0.01" min="0"
                name="unit_price"
                value="{{ old('unit_price', $item?->unit_price ?? '0.00') }}" required>
        </div>

        @unless ($item)
            <div class="staff-field">
                <label for="quantity">Opening Stock </label>
                <input id="quantity" type="number" min="0" name="quantity" value="{{ old('quantity', 0) }}" required>
            </div>
        @endunless

        <div class="staff-field">
            <label for="expiry_date">Expiry Date</label>
            <input
                id="expiry_date"
                type="date"
                name="expiry_date"
                value="{{ old('expiry_date', $item?->expiry_date?->format('Y-m-d')) }}">
        </div>

        <div class="staff-field">
            <label for="reorder_level">Reorder Level </label>
            <input
                id="reorder_level"
                type="number" min="0"
                name="reorder_level"
                value="{{ old('reorder_level', $item?->reorder_level ?? 5) }}" required>
        </div>
    </div>

    <div class="staff-field" style="margin-top: 22px;">
        <label for="description">Description</label>
        <textarea
            id="description"
            name="description"
            placeholder="Optional notes about this item">{{ old('description', $item?->description) }}</textarea>
    </div>
</div>
