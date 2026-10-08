<x-staff-layout title="Edit Inventory Audit | Photoline">
@push('styles')
<style>
.audit-edit-page{width:min(1180px,calc(100% - 36px));margin:auto;padding:32px 0 55px}.audit-edit-card{padding:28px;border:1px solid #e5eaf2;border-radius:16px;background:#fff}.audit-edit-heading small{color:#ed2b24;font-size:10px;font-weight:800;letter-spacing:1.4px;text-transform:uppercase}.audit-edit-heading h1{margin:6px 0 7px;font-size:29px}.audit-edit-heading p{margin:0 0 24px;color:#68738a;font-size:13px}.audit-error{margin-bottom:18px;padding:12px 14px;border:1px solid #f5cecb;border-radius:9px;color:#a72520;background:#fff0ef;font-size:12px}.audit-error div+div{margin-top:4px}.audit-date{margin-bottom:20px}.audit-date label{display:block;margin-bottom:7px;color:#33405a;font-size:12px;font-weight:700}.audit-date input{min-height:42px;padding:8px 11px;border:1px solid #dbe2ed;border-radius:8px}.audit-table-wrap{overflow-x:auto;border:1px solid #e5eaf2;border-radius:12px}.audit-table{width:100%;border-collapse:collapse}.audit-table th,.audit-table td{padding:13px 14px;border-bottom:1px solid #edf0f5;text-align:left;font-size:12px}.audit-table th{color:#7b8799;background:#fafbfd;font-size:10px;text-transform:uppercase;letter-spacing:.6px}.audit-table tr:last-child td{border-bottom:0}.recorded-qty{font-weight:800;color:#1c2e4a}.recorded-label{display:block;margin-top:3px;color:#8a95a6;font-size:10px}.qty-input{width:120px;min-height:38px;padding:8px 10px;border:1px solid #dbe2ed;border-radius:7px}.audit-actions{display:flex;gap:9px;margin-top:20px}.audit-button,.audit-cancel{min-height:42px;display:inline-flex;align-items:center;justify-content:center;padding:0 15px;border:0;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;cursor:pointer}.audit-button{color:#fff;background:#1769e8}.audit-cancel{color:#536078;background:#eef3f9}
</style>
@endpush
<div class="audit-edit-page"><div class="audit-edit-card">
<div class="audit-edit-heading"><small>Staff Inventory</small><h1>Edit Inventory Audit #{{ $audit->id }}</h1><p>Update the physical counts. Recorded quantities are kept as the original inventory snapshot.</p></div>
@if($errors->any())<div class="audit-error">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
<form method="POST" action="{{ route('audits.update', $audit) }}">@csrf @method('PUT')
<div class="audit-date"><label for="audit_date">Audit Date</label><input id="audit_date" type="date" name="audit_date" value="{{ old('audit_date', $audit->audit_date->format('Y-m-d')) }}" required></div>
<div class="audit-table-wrap"><table class="audit-table"><thead><tr><th>Product</th><th>SKU</th><th>Recorded Qty</th><th>Physical Count</th></tr></thead><tbody>
@foreach($audit->details as $index=>$detail)
<tr><td>{{ $detail->product->product_name }}<input type="hidden" name="products[{{ $index }}][detail_id]" value="{{ $detail->id }}"></td><td>{{ $detail->product->sku }}</td><td><span class="recorded-qty">{{ $detail->recorded_qty }}</span><span class="recorded-label">Original audit snapshot</span></td><td><input class="qty-input" type="number" name="products[{{ $index }}][counted_qty]" min="0" value="{{ old("products.$index.counted_qty", $detail->counted_qty) }}" required></td></tr>
@endforeach
</tbody></table></div>
<div class="audit-actions"><button class="audit-button" type="submit">Save Changes</button><a class="audit-cancel" href="{{ route('audits.show', $audit) }}">Cancel</a></div>
</form></div></div>
</x-staff-layout>
