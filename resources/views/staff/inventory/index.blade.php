<x-staff-layout title="Inventory | Photoline">
@push('styles')
<style>
.inventory-page{width:min(1180px,calc(100% - 36px));margin:auto;padding:32px 0 55px}.inventory-heading{display:flex;justify-content:space-between;align-items:flex-end;gap:18px;margin-bottom:18px}.inventory-heading small{color:#ed2b24;font-size:10px;font-weight:800;letter-spacing:1.4px;text-transform:uppercase}.inventory-heading h1{margin:6px 0 7px;font-size:29px;letter-spacing:-.7px}.inventory-heading p{margin:0;color:#68738a;font-size:13px}.inventory-button{min-height:40px;display:inline-flex;align-items:center;padding:0 14px;border-radius:8px;color:#fff;background:#1769e8;text-decoration:none;font-size:12px;font-weight:700}.notice{padding:12px 14px;margin-bottom:14px;border-radius:9px;font-size:12px}.notice.success{color:#206c43;background:#ebf8f0;border:1px solid #ccebd9}.notice.error{color:#a72520;background:#fff0ef;border:1px solid #f5cecb}.inventory-toolbar{display:flex;gap:9px;margin-bottom:14px;padding:14px;border:1px solid #e5eaf2;border-radius:12px;background:#fff}.inventory-search{flex:1;min-height:40px;padding:0 12px;border:1px solid #dbe2ed;border-radius:8px;outline:0;font-size:12px}.inventory-search:focus{border-color:#1769e8;box-shadow:0 0 0 3px rgba(23,105,232,.09)}.inventory-tool{min-height:40px;padding:0 14px;border:0;border-radius:8px;background:#eef3f9;color:#536078;font-size:12px;font-weight:700;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center}.inventory-card{overflow:hidden;border:1px solid #e5eaf2;border-radius:14px;background:#fff}.inventory-table{width:100%;border-collapse:collapse}.inventory-table th,.inventory-table td{padding:14px 16px;border-bottom:1px solid #edf0f5;text-align:left;font-size:12px}.inventory-table th{color:#7b8799;background:#fafbfd;font-size:10px;letter-spacing:.6px;text-transform:uppercase}.inventory-table tr:last-child td{border-bottom:0}.sku{color:#1769e8;font-weight:800}.inventory-actions{display:flex;align-items:center;gap:8px}.inventory-actions a{color:#1769e8;text-decoration:none;font-size:11px;font-weight:700}.delete-button{border:0;padding:6px 9px;border-radius:7px;color:#b52a25;background:#fff0ef;font-size:10px;font-weight:700;cursor:pointer}.pagination{margin-top:18px}@media(max-width:700px){.inventory-heading{align-items:flex-start;flex-direction:column}.inventory-toolbar{flex-wrap:wrap}.inventory-search{flex-basis:100%}}
</style>
@endpush
<div class="inventory-page">
    <div class="inventory-heading"><div><small>Staff Inventory</small><h1>Inventory Items</h1><p>Maintain the products and supplies available for inventory audits.</p></div><a href="{{ route('inventory.create') }}" class="inventory-button">+ Add Inventory Item</a></div>
    @if(session('success'))<div class="notice success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="notice error">{{ session('error') }}</div>@endif
    <form method="GET" action="{{ route('inventory.index') }}" class="inventory-toolbar">
        <input class="inventory-search" type="search" name="search" value="{{ request('search') }}" placeholder="Search SKU, item name, or category">
        <button type="submit" class="inventory-tool">Search</button>
        @if(request('search'))<a href="{{ route('inventory.index') }}" class="inventory-tool">Clear</a>@endif
    </form>
    <div class="inventory-card"><div style="overflow-x:auto"><table class="inventory-table"><thead><tr><th>SKU</th><th>Item</th><th>Category</th><th>Unit</th><th>Actions</th></tr></thead><tbody>
        @forelse($products as $product)
        <tr><td class="sku">{{ $product->sku }}</td><td>{{ $product->product_name }}</td><td>{{ $product->category }}</td><td>{{ $product->unit }}</td><td><div class="inventory-actions"><a href="{{ route('inventory.edit',$product) }}">Edit</a><form method="POST" action="{{ route('inventory.destroy',$product) }}" onsubmit="return confirm('Delete this inventory item?');">@csrf @method('DELETE')<button class="delete-button" type="submit">Delete</button></form></div></td></tr>
        @empty
        <tr><td colspan="5" style="text-align:center;padding:34px;color:#7b8799">No inventory items found.</td></tr>
        @endforelse
    </tbody></table></div></div>
    <div class="pagination">{{ $products->links() }}</div>
</div>
</x-staff-layout>
