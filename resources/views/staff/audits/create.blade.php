<x-staff-layout title="Create Inventory Audit">
    <div class="staffui-pagehead">
        <div>
            <h1>Create Inventory Audit</h1>
        </div>
        <a class="staffui-btn staffui-btn-outline" href="{{ route('staff.audits') }}">Back to Audits</a>
    </div>

    @if ($errors->any())
        <div class="staffui-alert">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('staff.audits.store') }}" class="staffui-tablecard" style="padding:22px;max-width:720px">
        @csrf
        <label for="audit_date">Audit date</label>
        <input id="audit_date" type="date" name="audit_date" value="{{ old('audit_date', now()->toDateString()) }}" required style="display:block;width:100%;margin:8px 0 18px;padding:10px;border:1px solid #d1d5db;border-radius:8px">

        <p>{{ $items->count() }} inventory item(s) will be added to this audit.</p>
        <button class="staffui-btn" type="submit">Create Audit</button>
    </form>
</x-staff-layout>
