@extends('layouts.panel')

@section('title', 'Inventory Audits')

@section('content')
<div class="container">

    <div class="page-header">
        <div>
            <h1>Inventory Audits</h1>
            <p>Count physical stock and compare it against the recorded quantity.</p>
        </div>
        <a href="{{ route('audits.create') }}" class="btn">+ New Audit</a>
    </div>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    <form method="GET" action="{{ route('audits.index') }}" class="filter-bar">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by date (YYYY-MM-DD) or auditor">
        <button type="submit" class="btn">Search</button>
        @if(request('search'))
            <a href="{{ route('audits.index') }}" class="btn gray">Clear</a>
        @endif
    </form>

    <div class="card table-wrap">
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
                        <td>#{{ $audit->id }}</td>
                        <td>{{ $audit->audit_date->format('Y-m-d') }}</td>
                        <td>{{ $audit->user->name }}</td>
                        <td>
                            <span class="badge {{ $audit->status === 'Completed' ? 'green' : 'orange' }}">
                                {{ $audit->status }}
                            </span>
                        </td>
                        <td class="actions">
                            <a href="{{ route('audits.show', $audit) }}">View</a>
                            @if($audit->status === 'Ongoing')
                                <a href="{{ route('audits.edit', $audit) }}">Edit</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">No inventory audits found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">{{ $audits->links() }}</div>
    </div>

</div>
@endsection
