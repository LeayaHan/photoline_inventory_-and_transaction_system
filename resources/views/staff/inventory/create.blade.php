<x-staff-layout title="Add Inventory Item | Photoline">
@push('styles')
<style>
.inventory-form-page{width:min(760px,calc(100% - 36px));margin:auto;padding:34px 0 55px}.inventory-form-card{padding:28px;border:1px solid #e5eaf2;border-radius:16px;background:#fff;box-shadow:0 10px 30px rgba(20,45,80,.05)}.inventory-form-heading small{color:#ed2b24;font-size:10px;font-weight:800;letter-spacing:1.4px;text-transform:uppercase}.inventory-form-heading h1{margin:6px 0 7px;font-size:28px}.inventory-form-heading p{margin:0 0 25px;color:#68738a;font-size:13px}.inventory-form-group{margin-bottom:17px}.inventory-form-group label{display:block;margin-bottom:7px;color:#33405a;font-size:12px;font-weight:700}.inventory-input{width:100%;min-height:44px;padding:10px 12px;border:1px solid #dbe2ed;border-radius:8px;outline:0;font-size:13px}.inventory-input:focus{border-color:#1769e8;box-shadow:0 0 0 3px rgba(23,105,232,.09)}.inventory-errors{margin-bottom:18px;padding:12px 14px;border:1px solid #f5cecb;border-radius:9px;color:#a72520;background:#fff0ef;font-size:12px}.inventory-errors ul{margin:7px 0 0 18px}.inventory-actions{display:flex;gap:9px;margin-top:24px}.inventory-save,.inventory-cancel{min-height:43px;display:inline-flex;align-items:center;justify-content:center;padding:0 16px;border:0;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;cursor:pointer}.inventory-save{color:#fff;background:#1769e8}.inventory-cancel{color:#536078;background:#eef3f9}
</style>
@endpush
<div class="inventory-form-page"><div class="inventory-form-card"><div class="inventory-form-heading"><small>Staff Inventory</small><h1>Add Inventory Item</h1><p>This item will become available when creating inventory audits.</p></div>
@if($errors->any())<div class="inventory-errors"><strong>Please fix the following:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('inventory.store') }}">@csrf
<div class="inventory-form-group"><label for="sku">SKU</label><input class="inventory-input" id="sku" name="sku" value="{{ old('sku') }}" placeholder="Example: CAM-002" required></div>
<div class="inventory-form-group"><label for="product_name">Item Name</label><input class="inventory-input" id="product_name" name="product_name" value="{{ old('product_name') }}" placeholder="Example: Instant Camera" required></div>
<div class="inventory-form-group"><label for="category">Category</label><input class="inventory-input" id="category" name="category" value="{{ old('category') }}" placeholder="Example: Camera" required></div>
<div class="inventory-form-group"><label for="unit">Unit</label><input class="inventory-input" id="unit" name="unit" value="{{ old('unit','piece') }}" placeholder="Example: piece" required></div>
<div class="inventory-actions"><button class="inventory-save" type="submit">Save Inventory Item</button><a class="inventory-cancel" href="{{ route('inventory.index') }}">Cancel</a></div>
</form></div></div>
</x-staff-layout>
