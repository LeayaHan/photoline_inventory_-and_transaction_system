<x-staff-layout title="Create Transaction">

@push('styles')
<style>
        .container {
            width: 80%;
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
        }

        h1 {
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
        }

        input, textarea, select {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        textarea {
            min-height: 100px;
        }

        button, .button {
            display: inline-block;
            padding: 10px 16px;
            border: none;
            border-radius: 4px;
            background: #222;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }

        .back {
            margin-left: 10px;
            background: #777;
        }

        .errors {
            background: #ffe5e5;
            border: 1px solid #ff9999;
            padding: 10px;
            margin-bottom: 20px;
        }
</style>
@endpush

<div class="container">

    <h1>Create Customer Service Transaction</h1>

    @if($errors->any())

        <div class="errors">
            <strong>Please fix the following:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif

    <form
        method="POST"
        action="{{ route('transactions.store') }}"
    >

        @csrf

        <div class="form-group">

            <label for="customer_name">
                Customer Name
            </label>

            <input
                type="text"
                id="customer_name"
                name="customer_name"
                value="{{ old('customer_name') }}"
                required
            >

        </div>

        <div class="form-group">

            <label for="service_type">
                Service Type
            </label>

            <input
                type="text"
                id="service_type"
                name="service_type"
                placeholder="Example: Photo Printing"
                value="{{ old('service_type') }}"
                required
            >

        </div>

        <div class="form-group">

            <label for="transaction_date">
                Transaction Date
            </label>

            <input
                type="date"
                id="transaction_date"
                name="transaction_date"
                value="{{ old('transaction_date', date('Y-m-d')) }}"
                required
            >

        </div>

        <div class="form-group">

            <label for="item_description">
                Item Description
            </label>

            <textarea
                id="item_description"
                name="item_description"
                placeholder="Describe the item or service..."
            >{{ old('item_description') }}</textarea>

        </div>

        <button type="submit">
            Create Transaction
        </button>

        <a href="{{ route('transactions.index') }}" class="button back">
            Cancel
        </a>

    </form>

</div>

</x-staff-layout>
