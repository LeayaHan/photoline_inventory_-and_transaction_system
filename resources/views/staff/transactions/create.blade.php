<x-staff-layout title="New Transaction">
    <link rel="stylesheet" href="{{ asset('css/transactions.css') }}">
    <div class="staffui-pagehead transaction-pagehead">
        <div><h1>New Transaction</h1></div>
    </div>
    <form method="POST" action="{{ route('staff.transactions.store') }}">
        @csrf
        @include('staff.transactions._form')
    </form>
</x-staff-layout>
