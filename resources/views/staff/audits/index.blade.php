<x-staff-layout title="Inventory Audits">

@push('styles')
<style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        input, button {
            padding: 8px;
        }

        .success {
            padding: 10px;
            margin-bottom: 15px;
            background: #e8f5e9;
        }

        .actions a {
            margin-right: 10px;
        }

        .page-card {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
        }
</style>
@endpush

<div class="page-card">

<h1>Inventory Audits</h1>

@if(session('success'))
    <div class="success">
        {{ session('success') }}
    </div>
@endif

<form method="GET" action="{{ route('audits.index') }}">
    <input
        type="text"
        name="search"
        value="{{ request('search') }}"
        placeholder="Search date or auditor"
    >

    <button type="submit">Search</button>

    <a href="{{ route('audits.create') }}">Create Audit</a>
</form>

<table>
    <thead>
        <tr>
            <th>Audit ID</th>
            <th>Audit Date</th>
            <th>Auditor</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @forelse($audits as $audit)
            <tr>
                <td>{{ $audit->id }}</td>
                <td>{{ $audit->audit_date->format('Y-m-d') }}</td>
                <td>{{ $audit->user->name }}</td>
                <td>{{ $audit->status }}</td>
                <td class="actions">
                    <a href="{{ route('audits.show', $audit) }}">View</a>

                    @if($audit->status === 'Ongoing')
                        <a href="{{ route('audits.edit', $audit) }}">Edit</a>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">No inventory audits found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<br>

{{ $audits->links() }}

</div>

</x-staff-layout>
