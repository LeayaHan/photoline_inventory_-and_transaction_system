<x-manager-layout title="Add Staff | Photoline">
@push('styles')
<style>
.staff-form-page{width:min(900px,calc(100% - 36px));margin:0 auto;padding:32px 0 55px}.form-head{margin-bottom:20px}.form-kicker{color:#ed2b24;font-size:10px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase}.form-head h1{margin:5px 0;color:#13233f;font-size:29px;letter-spacing:-.7px}.form-head p{margin:0;color:#7b8799;font-size:12px}.form-card{background:#fff;border:1px solid #e5eaf2;border-radius:14px;padding:24px}.form-section{padding-bottom:22px;margin-bottom:22px;border-bottom:1px solid #edf0f5}.form-section:last-of-type{border-bottom:0;margin-bottom:0;padding-bottom:0}.section-title{margin-bottom:15px;color:#13233f;font-size:13px;font-weight:800}.section-grid{display:grid;gap:14px}.two-grid{grid-template-columns:1fr 1fr}.name-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px}.form-group{margin-bottom:14px}.form-group:last-child{margin-bottom:0}label{display:block;margin-bottom:7px;color:#536078;font-size:11px;font-weight:700}input{width:100%;min-height:40px;padding:0 12px;border:1px solid #dbe2ed;border-radius:8px;box-sizing:border-box;outline:none;background:#fff;color:#13233f;font-size:12px}input:focus{border-color:#1769e8;box-shadow:0 0 0 3px rgba(23,105,232,.09)}.error{margin-top:5px;color:#c72c26;font-size:11px}.form-actions{display:flex;gap:9px;margin-top:22px}.btn{display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 15px;border:0;border-radius:8px;text-decoration:none;cursor:pointer;font-size:12px;font-weight:700}.btn-primary{background:#1769e8;color:#fff}.btn-secondary{background:#eef3f9;color:#536078}@media(max-width:700px){.two-grid,.name-grid{grid-template-columns:1fr}}
</style>
@endpush
<div class="staff-form-page">
    <div class="form-head"><div class="form-kicker">Staff Administration</div><h1>Add Staff</h1><p>Create a complete Photoline staff profile and login account.</p></div>
    <div class="form-card">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            @include('manager.users._fields')
            <div class="form-actions"><button type="submit" class="btn btn-primary">Create Staff Account</button><a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a></div>
        </form>
    </div>
</div>
</x-manager-layout>
