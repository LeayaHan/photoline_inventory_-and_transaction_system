<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/branch-manager/users.css') }}">

    <div class="users-container">
        <div class="users-header">
            <div>
                <h1 class="users-title">Staff Management</h1>
                <p class="users-subtitle">Manage your branch staff accounts.</p>
            </div>

            <a href="{{ route('branch-manager.users.create') }}"
               class="users-btn users-btn-primary">
                Add Staff
            </a>
        </div>

        @if (session('success'))
            <div class="users-alert users-alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="users-card">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Staff ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Contact Number</th>
                        <th>Position</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($staff as $member)
                        <tr>
                            <td>{{ $member->staff_id }}</td>

                            <td>
                                {{ $member->first_name }}
                                {{ $member->middle_name ? $member->middle_name . ' ' : '' }}{{ $member->last_name }}
                            </td>

                            <td>{{ $member->email }}</td>
                            <td>{{ $member->contact_number }}</td>
                            <td>{{ $member->position }}</td>

                            <td>
                                <div class="users-actions">
                                    <a
                                        href="{{ route('branch-manager.users.show', ['staff' => $member->id]) }}"
                                        class="users-link users-link-view"
                                    >
                                        View
                                    </a>

                                    <a
                                        href="{{ route('branch-manager.users.edit', ['staff' => $member->id]) }}"
                                        class="users-link users-link-edit"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        action="{{ route('branch-manager.users.destroy', ['staff' => $member->id]) }}"
                                        method="POST"
                                        onsubmit="return confirm('Delete this staff account?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="users-link users-link-delete"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="users-empty">
                                No staff accounts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="users-pagination">
            {{ $staff->links() }}
        </div>
    </div>
</x-app-layout>
