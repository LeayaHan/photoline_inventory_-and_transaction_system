@extends('layouts.panel')

@section('title', 'Transaction Details')

@push('styles')
<style>
        .row {
            display: flex;
            margin-bottom: 15px;
        }
        .label {
            width: 180px;
            font-weight: bold;
        }
        .button {
            display: inline-block;
            padding: 10px 15px;
            background: #222;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 8px;
        }
        .back {
            background: #777;
        }
        .success {
            background: #e2f5e9;
            border: 1px solid #9bd3ac;
            padding: 12px;
            margin-bottom: 20px;
        }
    
</style>
@endpush

@section('content')
<div class="container">

    <h1>Transaction Details</h1>

    @if(session('success'))

        <div class="success">
            {{ session('success') }}
        </div>

    @endif

    <div class="row">

        <div class="label">
            Control Number:
        </div>

        <div>
            {{ $transaction->control_number }}
        </div>

    </div>

    <div class="row">

        <div class="label">
            Customer Name:
        </div>

        <div>
            {{ $transaction->customer_name }}
        </div>

    </div>

    <div class="row">

        <div class="label">
            Service Type:
        </div>

        <div>
            {{ $transaction->service_type }}
        </div>

    </div>

    <div class="row">

        <div class="label">
            Transaction Date:
        </div>

        <div>
            {{ $transaction->transaction_date->format('Y-m-d') }}
        </div>

    </div>

    <div class="row">

        <div class="label">
            Item Description:
        </div>

        <div>
            {{ $transaction->item_description ?: 'None' }}
        </div>

    </div>

    <div class="row">

        <div class="label">
            Status:
        </div>

        <div>
            {{ $transaction->status }}
        </div>

    </div>

    <div style="margin-top: 30px;">

        <a
            href="{{ route('transactions.edit', $transaction) }}"
            class="button"
        >
            Edit
        </a>

        <a
            href="{{ route('transactions.index') }}"
            class="button back"
        >
            Back
        </a>

    </div>

</div>
@endsection
