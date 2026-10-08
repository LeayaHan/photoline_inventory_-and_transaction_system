<x-staff-layout title="Inventory Audits | Photoline">
@push('styles')
<style>
.audit-page{width:min(1180px,calc(100% - 36px));margin:0 auto;padding:32px 0 55px}.audit-heading{display:flex;justify-content:space-between;align-items:flex-end;gap:18px;margin-bottom:18px}.audit-heading small{color:#ed2b24;font-size:10px;font-weight:800;letter-spacing:1.4px;text-transform:uppercase}.audit-heading h1{margin:5px 0 0;color:#13233f;font-size:29px;letter-spacing:-.7px}.audit-heading p{margin:7px 0 0;color:#7b8799;font-size:12px}.audit-primary,.audit-secondary{min-height:40px;display:inline-flex;align-items:center;justify-content:center;padding:0 14px;border:0;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;cursor:pointer}.audit-primary{color:#fff;background:#1769e8}.audit-primary:hover{background:#0f58c8}.audit-secondary{color:#536078;background:#eef3f9}.audit-notice{padding:12px 14px;margin-bottom:14px;border-radius:9px;font-size:12px}.audit-notice.success{color:#206c43;background:#ebf8f0;border:1px solid #ccebd9}.audit-notice.error{color:#a72520;background:#fff0ef;border:1px solid #f5cecb}.audit-toolbar{display:flex;align-items:center;gap:9px;margin-bottom:14px;padding:14px;border:1px solid #e5eaf2;border-radius:12px;background:#fff}.audit-search{flex:1;min-height:40px;padding:0 12px;border:1px solid #dbe2ed;border-radius:8px;outline:none;font-size:12px}.audit-search:focus{border-color:#1769e8;box-shadow:0 0 0 3px rgba(23,105,232,.09)}.audit-card{overflow:hidden;border:1px solid #e5eaf2;border-radius:14px;background:#fff}.audit-table{width:100%;border-collapse:collapse;table-layout:fixed}.audit-table th,.audit-table td{padding:14px 16px;border-bottom:1px solid #edf0f5;text-align:left;font-size:12px;vertical-align:middle}.audit-table th{color:#7b8799;background:#fafbfd;font-size:10px;letter-spacing:.6px;text-transform:uppercase}.audit-table th:nth-child(1){width:16%}.audit-table th:nth-child(2){width:21%}.audit-table th:nth-child(3){width:29%}.audit-table th:nth-child(4){width:14%}.audit-table th:nth-child(5){width:20%}.audit-table tr:last-child td{border-bottom:0}.audit-id{color:#1769e8;font-weight:800}.audit-status{display:inline-flex;padding:5px 9px;border-radius:999px;font-size:10px;font-weight:700}.audit-status.ongoing{color:#1769e8;background:#eaf2ff}.audit-status.completed{color:#20754a;background:#eaf8f0}.audit-actions{display:flex;align-items:center;gap:10px;white-space:nowrap}.audit-actions a{color:#1769e8;text-decoration:none;font-size:11px;font-weight:700}.audit-actions a:hover{text-decoration:underline}.audit-empty{padding:38px 20px!important;color:#7b8799;text-align:center!important}.audit-pagination{margin-top:18px}@media(max-width:800px){.audit-heading{align-items:flex-start;flex-direction:column}.audit-toolbar{flex-wrap:wrap}.audit-search{flex-basis:100%}.audit-card{overflow-x:auto}.audit-table{min-width:850px}}
</style>
@endpush

<div class="audit-page">
<div class="audit-heading"><div><small>Inventory Control</small><h1>Inventory Audits</h1><p>Record physical counts and review stock discrepancies.</p></div><a href="{{ route('audits.create') }}" class="audit-primary">+ Create Audit</a></div>

@if(session('success'))
<div class="audit-notice success">{{ session('success') }}</div>
@endif

@if(session('error'))
<div class="audit-notice error">{{ session('error') }}</div>
@endif

<form method="GET" action="{{ route('audits.index') }}" class="audit-toolbar">
<input class="audit-search" type="search" name="search" value="{{ request('search') }}" placeholder="Search audit ID, date, auditor, SKU, or product">
<button type="submit" class="audit-secondary">Search</button>
@if(request('search'))
<a href="{{ route('audits.index') }}" class="audit-secondary">Clear</a>
@endif
</form>

<div class="audit-card">
<div style="overflow-x:auto">
<table class="audit-table">
<thead>
<tr>
<th>Audit</th>
<th>Audit Date</th>
<th>Auditor</th>
<th>Status</th>
<th>Actions</th>
</tr>
</thead>

<tbody>
@forelse($audits as $audit)
<tr>
<td class="audit-id">#{{ $audit->id }}</td>
<td>{{ $audit->audit_date->format('M d, Y') }}</td>
<td>{{ $audit->user?->name ?? 'Unknown' }}</td>
<td>
<span class="audit-status {{ strtolower($audit->status) }}">
{{ $audit->status }}
</span>
</td>
<td>
<div class="audit-actions">
<a href="{{ route('audits.show',$audit) }}">View</a>
@if($audit->status === 'Ongoing')
<a href="{{ route('audits.edit',$audit) }}">Edit</a>
@endif
</div>
</td>
</tr>

@empty
<tr>
<td colspan="5" class="audit-empty">No inventory audits found.</td>
</tr>
@endforelse
</tbody>
</table>
</div>
</div>

<div class="audit-pagination">
{{ $audits->links() }}
</div>

</div>
</x-staff-layout>