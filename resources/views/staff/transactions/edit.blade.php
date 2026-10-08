<x-staff-layout title="Edit Transaction">

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

    <h1>
        Edit Transaction
    </h1>

    <p>
        Control Number:
        <strong>{{ $transaction->control_number }}</strong>
    </p>

    @if($errors->any())

        <div class="errors">

            <strong>Please fix the following:</strong>

            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif

    <form
        method="POST"
        action="{{ route('transactions.update', $transaction) }}"
    >

        @csrf

        @method('PUT')

        <div class="form-group">

            <label for="customer_name">
                Customer Name
            </label>

            <input
                type="text"
                id="customer_name"
                name="customer_name"
                value="{{ old('customer_name', $transaction->customer_name) }}"
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
                value="{{ old('service_type', $transaction->service_type) }}"
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
                value="{{ old(
                    'transaction_date',
                    $transaction->transaction_date->format('Y-m-d')
                ) }}"
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
            >{{ old('item_description', $transaction->item_description) }}</textarea>

        </div>

        <div class="form-group">

            <label for="status">
                Status
            </label>

            <select
                id="status"
                name="status"
            >

                <option
                    value="Pending"
                    {{ $transaction->status === 'Pending' ? 'selected' : '' }}
                >
                    Pending
                </option>

                <option
                    value="Claimed"
                    {{ $transaction->status === 'Claimed' ? 'selected' : '' }}
                >
                    Claimed
                </option>

                <option
                    value="Voided"
                    {{ $transaction->status === 'Voided' ? 'selected' : '' }}
                >
                    Voided
                </option>

            </select>

        </div>

        <button type="submit">
            Update Transaction
        </button>

        <a href="{{ route('transactions.show', $transaction) }}" class="button back">
            Cancel
        </a>

    </form>

</div>

</x-staff-layout>
