<x-manager-layout title="User Management | Photoline">
@push('styles')
<style>
.users-page{width:min(1200px,calc(100% - 36px));margin:0 auto;padding:32px 0 55px}.users-head{display:flex;justify-content:space-between;align-items:flex-end;gap:20px;margin-bottom:20px}.users-kicker{color:#ed2b24;font-size:10px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase}.users-head h1{margin:5px 0 0;color:#13233f;font-size:29px;letter-spacing:-.7px}.users-head p{margin:7px 0 0;color:#7b8799;font-size:12px}.users-primary{display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 15px;border-radius:8px;background:#1769e8;color:#fff;text-decoration:none;font-size:12px;font-weight:700}.users-primary:hover{background:#0f58c8}.users-notice{padding:12px 14px;margin-bottom:14px;border-radius:9px;font-size:12px}.users-success{color:#206c43;background:#ebf8f0;border:1px solid #ccebd9}.users-error{color:#a72520;background:#fff0ef;border:1px solid #f5cecb}.users-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:14px}.users-stat{padding:16px 18px;border:1px solid #e5eaf2;border-radius:12px;background:#fff}.users-stat span{display:block;color:#7b8799;font-size:10px;font-weight:800;letter-spacing:.7px;text-transform:uppercase}.users-stat strong{display:block;margin-top:7px;color:#13233f;font-size:24px}.users-toolbar{display:flex;align-items:center;gap:9px;margin-bottom:14px;padding:14px;border:1px solid #e5eaf2;border-radius:12px;background:#fff}.users-search{flex:1;min-height:40px;padding:0 12px;border:1px solid #dbe2ed;border-radius:8px;outline:none;font-size:12px}.users-search:focus{border-color:#1769e8;box-shadow:0 0 0 3px rgba(23,105,232,.09)}.users-tool{display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 14px;border:0;border-radius:8px;background:#eef3f9;color:#536078;text-decoration:none;font-size:12px;font-weight:700;cursor:pointer}.users-card{overflow:hidden;border:1px solid #e5eaf2;border-radius:14px;background:#fff}.users-table{width:100%;border-collapse:collapse;table-layout:fixed}.users-table th,.users-table td{padding:14px 15px;border-bottom:1px solid #edf0f5;text-align:left;vertical-align:middle;font-size:12px}.users-table th{background:#fafbfd;color:#7b8799;font-size:10px;letter-spacing:.6px;text-transform:uppercase}.users-table th:nth-child(1){width:12%}.users-table th:nth-child(2){width:23%}.users-table th:nth-child(3){width:16%}.users-table th:nth-child(4){width:18%}.users-table th:nth-child(5){width:13%}.users-table th:nth-child(6){width:18%}.users-table tr:last-child td{border-bottom:0}.employee-id{color:#1769e8;font-weight:800}.staff-name{color:#13233f;font-weight:800}.staff-email{display:block;margin-top:3px;color:#7b8799;font-size:11px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.position{color:#536078;font-weight:600}.contact{color:#536078}.date-hired{color:#536078;white-space:nowrap}.status{display:inline-flex;padding:5px 9px;border-radius:999px;font-size:10px;font-weight:800}.status.active{color:#20754a;background:#eaf8f0}.status.inactive{color:#8a4b10;background:#fff4df}.users-actions{display:flex;align-items:center;gap:8px;white-space:nowrap}.users-action{display:inline-flex;align-items:center;justify-content:center;min-height:32px;padding:0 10px;border-radius:7px;text-decoration:none;font-size:11px;font-weight:700}.users-edit{background:#eef3f9;color:#1769e8}.users-delete{background:#fff0ef;color:#c72c26;border:0;cursor:pointer}.users-actions form{margin:0}.users-empty{padding:42px 20px!important;text-align:center!important;color:#7b8799}.users-pagination{margin-top:18px}@media(max-width:950px){.users-table{min-width:1050px}.users-card{overflow-x:auto}}@media(max-width:700px){.users-head{align-items:flex-start;flex-direction:column}.users-stats{grid-template-columns:1fr}.users-toolbar{flex-wrap:wrap}.users-search{flex-basis:100%}}
</style>
@endpush

<div class="users-page">
    <div class="users-head">
        <div>
            <div class="users-kicker">Staff Administration</div>
            <h1>User Management</h1>
            <p>Manage Photoline staff accounts, contact details, positions, and account status.</p>
        </div>
        <a href="{{ route('users.create') }}" class="users-primary">+ Add Staff</a>
    </div>

    @if(session('success'))
        <div class="users-notice users-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="users-notice users-error">{{ session('error') }}</div>
    @endif

    <div class="users-stats">
        <div class="users-stat"><span>Total Staff</span><strong>{{ $staffTotal }}</strong></div>
        <div class="users-stat"><span>Active Staff</span><strong>{{ $activeStaff }}</strong></div>
        <div class="users-stat"><span>Inactive Staff</span><strong>{{ $inactiveStaff }}</strong></div>
    </div>

    <form method="GET" action="{{ route('users.index') }}" class="users-toolbar">
        <input class="users-search" type="search" name="search" value="{{ request('search') }}" placeholder="Search employee ID, name, position, email, or phone">
        <button type="submit" class="users-tool">Search</button>
        @if(request('search'))
            <a href="{{ route('users.index') }}" class="users-tool">Clear</a>
        @endif
    </form>

    <div class="users-card">
        <div style="overflow-x:auto">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Staff</th>
                        <th>Position</th>
                        <th>Contact</th>
                        <th>Date Hired</th>
                        <th>Status / Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="employee-id">{{ $user->employee_id ?: '—' }}</td>
                            <td>
                                <div class="staff-name">{{ $user->name }}</div>
                                <span class="staff-email">{{ $user->email }}</span>
                            </td>
                            <td class="position">{{ $user->position ?: '—' }}</td>
                            <td class="contact">{{ $user->phone ?: '—' }}</td>
                            <td class="date-hired">{{ $user->date_hired?->format('M d, Y') ?? '—' }}</td>
                            <td>
                                <div style="display:flex;align-items:center;justify-content:space-between;gap:10px">
                                    <span class="status {{ $user->is_active ? 'active' : 'inactive' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <div class="users-actions">
                                        <a href="{{ route('users.edit', $user) }}" class="users-action users-edit">Edit</a>
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this staff account?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="users-action users-delete">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="users-empty">No staff accounts found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="users-pagination">{{ $users->links() }}</div>
</div>
</x-manager-layout>
