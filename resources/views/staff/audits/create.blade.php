<x-staff-layout title="Create Inventory Audit | Photoline">
@push('styles')
<style>
.audit-page{width:min(1180px,calc(100% - 36px));margin:auto;padding:32px 0 55px}.audit-card{padding:28px;border:1px solid #e5eaf2;border-radius:16px;background:#fff}.audit-heading small{color:#ed2b24;font-size:10px;font-weight:800;letter-spacing:1.4px;text-transform:uppercase}.audit-heading h1{margin:6px 0 7px;font-size:29px}.audit-heading p{margin:0 0 24px;color:#68738a;font-size:13px}.audit-error{margin-bottom:18px;padding:12px 14px;border:1px solid #f5cecb;border-radius:9px;color:#a72520;background:#fff0ef;font-size:12px}.audit-error ul{margin:7px 0 0 18px}.audit-date{margin-bottom:20px}.audit-date label{display:block;margin-bottom:7px;color:#33405a;font-size:12px;font-weight:700}.audit-date input{min-height:42px;padding:8px 11px;border:1px solid #dbe2ed;border-radius:8px}.audit-table-wrap{overflow-x:auto;border:1px solid #e5eaf2;border-radius:12px}.audit-table{width:100%;border-collapse:collapse}.audit-table th,.audit-table td{padding:13px 14px;border-bottom:1px solid #edf0f5;text-align:left;font-size:12px}.audit-table th{color:#7b8799;background:#fafbfd;font-size:10px;text-transform:uppercase;letter-spacing:.6px}.audit-table tr:last-child td{border-bottom:0}.qty-input{width:120px;min-height:38px;padding:8px 10px;border:1px solid #dbe2ed;border-radius:7px}.audit-actions{display:flex;gap:9px;margin-top:20px}.audit-button,.audit-cancel{min-height:42px;display:inline-flex;align-items:center;justify-content:center;padding:0 15px;border:0;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;cursor:pointer}.audit-button{color:#fff;background:#1769e8}.audit-button:disabled{opacity:.65;cursor:not-allowed}.audit-cancel{color:#536078;background:#eef3f9}@media(max-width:650px){.audit-page{width:min(100% - 24px,1180px);padding-top:20px}.audit-card{padding:20px}.qty-input{width:95px}}
</style>
@endpush
<div class="audit-page"><div class="audit-card">
<div class="audit-heading"><small>Staff Inventory</small><h1>Create Inventory Audit</h1><p>Enter the recorded quantity and physical count for each inventory item.</p></div>
@if($errors->any())<div class="audit-error"><strong>Please fix the following:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('audits.store') }}" id="audit-create-form">@csrf<input type="hidden" name="form_token" value="{{ $formToken }}">
<div class="audit-date"><label for="audit_date">Audit Date</label><input id="audit_date" type="date" name="audit_date" value="{{ old('audit_date',date('Y-m-d')) }}" required></div>
<div class="audit-table-wrap"><table class="audit-table"><thead><tr><th>Product</th><th>SKU</th><th>Unit</th><th>Recorded Qty</th><th>Physical Count</th></tr></thead><tbody>
@forelse($products as $index=>$product)
<tr><td>{{ $product->product_name }}<input type="hidden" name="products[{{ $index }}][product_id]" value="{{ $product->id }}"></td><td>{{ $product->sku }}</td><td>{{ $product->unit }}</td><td><input class="qty-input" type="number" name="products[{{ $index }}][recorded_qty]" min="0" value="{{ old("products.$index.recorded_qty",0) }}" required></td><td><input class="qty-input" type="number" name="products[{{ $index }}][counted_qty]" min="0" value="{{ old("products.$index.counted_qty",0) }}" required></td></tr>
@empty
<tr><td colspan="5" style="text-align:center;padding:30px;color:#7b8799">No inventory items are available. Add an item from Inventory first.</td></tr>
@endforelse
</tbody></table></div>
@if($products->isNotEmpty())<div class="audit-actions"><button class="audit-button" id="create-audit-button" type="submit">Create Audit</button><a class="audit-cancel" href="{{ route('audits.index') }}">Cancel</a></div>@else<a class="audit-cancel" href="{{ route('inventory.create') }}">Add Inventory Item</a>@endif
</form></div></div>
@push('scripts')<script>document.getElementById('audit-create-form')?.addEventListener('submit',function(){const b=document.getElementById('create-audit-button');if(b){b.disabled=true;b.textContent='Creating...';}});</script>@endpush
</x-staff-layout>
