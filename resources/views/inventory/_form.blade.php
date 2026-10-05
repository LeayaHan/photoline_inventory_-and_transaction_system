{{-- Shared form fields for create / edit inventory item. Expects optional $product. --}}
@php $p = $product ?? null; @endphp

<div class="form-row">
    <div class="form-group">
        <label for="sku">SKU</label>
        <input type="text" id="sku" name="sku" value="{{ old('sku', $p?->sku) }}" placeholder="e.g. CAM-001" required>
    </div>
    <div class="form-group">
        <label for="product_name">Item Name</label>
        <input type="text" id="product_name" name="product_name" value="{{ old('product_name', $p?->product_name) }}" placeholder="e.g. Digital Camera" required>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="category">Category</label>
        <input type="text" id="category" name="category" value="{{ old('category', $p?->category) }}" placeholder="e.g. Camera" required>
    </div>
    <div class="form-group">
        <label for="unit">Unit</label>
        <input type="text" id="unit" name="unit" value="{{ old('unit', $p?->unit ?? 'piece') }}" placeholder="e.g. piece, box, pack" required>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="quantity">Quantity in Stock</label>
        <input type="number" id="quantity" name="quantity" min="0" value="{{ old('quantity', $p?->quantity ?? 0) }}" required>
        <div class="hint">Used as the POS quantity when you start an audit.</div>
    </div>
    <div class="form-group">
        <label for="reorder_level">Reorder Level</label>
        <input type="number" id="reorder_level" name="reorder_level" min="0" value="{{ old('reorder_level', $p?->reorder_level ?? 5) }}" required>
        <div class="hint">At or below this quantity the item appears under Replenishment.</div>
    </div>
</div>
