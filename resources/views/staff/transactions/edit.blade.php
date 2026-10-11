<x-staff-layout title="Edit Transaction">
    <link rel="stylesheet" href="{{ asset('css/transactions.css') }}">
    <div class="staffui-pagehead transaction-pagehead">
        <div><h1>Edit Transaction</h1><p>Control number: <strong>{{ $transaction->control_number }}</strong></p></div>
    </div>
    <form method="POST" action="{{ route('staff.transactions.update', $transaction) }}">
        @csrf
        @method('PUT')
        @include('staff.transactions._form')
    </form>
</x-staff-layout>
