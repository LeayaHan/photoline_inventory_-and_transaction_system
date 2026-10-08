<x-staff-layout title="New Transaction | Photoline">

@push('styles')
<style>
    .form-page { width:min(760px,calc(100% - 36px)); margin:0 auto; padding:34px 0 55px; }
    .form-card { padding:28px; border:1px solid #e5eaf2; border-radius:16px; background:#fff; box-shadow:0 10px 30px rgba(20,45,80,.05); }
    .form-heading small { color:#ed2b24; font-size:10px; font-weight:800; letter-spacing:1.4px; text-transform:uppercase; }
    .form-heading h1 { margin:6px 0 7px; font-size:28px; letter-spacing:-.7px; }
    .form-heading p { margin:0 0 25px; color:#68738a; font-size:13px; }
    .form-group { margin-bottom:18px; }
    .form-group label { display:block; margin-bottom:7px; color:#33405a; font-size:12px; font-weight:700; }
    .form-input { width:100%; min-height:44px; padding:10px 12px; border:1px solid #dbe2ed; border-radius:8px; outline:none; font-size:13px; }
    .form-input:focus { border-color:#1769e8; box-shadow:0 0 0 3px rgba(23,105,232,.09); }
    textarea.form-input { min-height:110px; resize:vertical; }
    .error-box { margin-bottom:18px; padding:12px 14px; border:1px solid #f5cecb; border-radius:9px; color:#a72520; background:#fff0ef; font-size:12px; }
    .error-box ul { margin:7px 0 0 18px; }
    .form-actions { display:flex; gap:9px; margin-top:25px; }
    .form-button, .form-cancel { min-height:43px; display:inline-flex; align-items:center; justify-content:center; padding:0 16px; border:0; border-radius:8px; font-size:12px; font-weight:700; text-decoration:none; cursor:pointer; }
    .form-button { color:#fff; background:#1769e8; }
    .form-button:disabled { opacity:.65; cursor:not-allowed; }
    .form-cancel { color:#536078; background:#eef3f9; }
</style>
@endpush

<div class="form-page">
    <div class="form-card">
        <div class="form-heading">
            <small>Staff Operations</small>
            <h1>New Customer Transaction</h1>
            <p>Record the customer's service request. The control number is generated automatically.</p>
        </div>

        @if($errors->any())
            <div class="error-box">
                <strong>Please fix the following:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('transactions.store') }}" id="transaction-create-form">
            @csrf
            <input type="hidden" name="form_token" value="{{ $formToken }}">

            <div class="form-group">
                <label for="customer_name">Customer Name</label>
                <input class="form-input" id="customer_name" type="text" name="customer_name" value="{{ old('customer_name') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="service_type">Service Type</label>
                <input class="form-input" id="service_type" type="text" name="service_type" value="{{ old('service_type') }}" placeholder="Example: Photo Printing" required>
            </div>

            <div class="form-group">
                <label for="transaction_date">Transaction Date</label>
                <input class="form-input" id="transaction_date" type="date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
            </div>

            <div class="form-group">
                <label for="item_description">Item Description</label>
                <textarea class="form-input" id="item_description" name="item_description" placeholder="Describe the item or service...">{{ old('item_description') }}</textarea>
            </div>

            <div class="form-actions">
                <button class="form-button" type="submit" id="create-transaction-button">Create Transaction</button>
                <a class="form-cancel" href="{{ route('transactions.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('transaction-create-form')?.addEventListener('submit', function () {
        const button = document.getElementById('create-transaction-button');
        if (button) {
            button.disabled = true;
            button.textContent = 'Creating...';
        }
    });
</script>
@endpush

</x-staff-layout>
