<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/branch-manager/users.css') }}">

    <div class="users-container">
        <div class="users-header">
            <div>
                <h1 class="users-title">Staff Details</h1>
                <p class="users-subtitle">
                    View the information for this staff account.
                </p>
            </div>

            <a href="{{ route('branch-manager.users.index') }}"
               class="users-btn users-btn-secondary">
                Back to Staff
            </a>
        </div>

        @if (session('success'))
            <div class="users-alert users-alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="users-details-card">
            <div class="users-details-heading">
                <div class="users-avatar">
                    {{ strtoupper(substr($staff->first_name ?? 'S', 0, 1)) }}
                </div>

                <div>
                    <h2>
                        {{ $staff->first_name }}
                        {{ $staff->middle_name ? $staff->middle_name . ' ' : '' }}
                        {{ $staff->last_name }}
                    </h2>

                    <p>{{ $staff->staff_id ?? 'No Staff ID' }}</p>
                </div>
            </div>

            <div class="users-details-grid">
                <div class="users-detail-item">
                    <span>Staff ID</span>
                    <strong>{{ $staff->staff_id ?? 'Not available' }}</strong>
                </div>

                <div class="users-detail-item">
                    <span>First Name</span>
                    <strong>{{ $staff->first_name }}</strong>
                </div>

                <div class="users-detail-item">
                    <span>Middle Name</span>
                    <strong>{{ $staff->middle_name ?: 'Not provided' }}</strong>
                </div>

                <div class="users-detail-item">
                    <span>Last Name</span>
                    <strong>{{ $staff->last_name }}</strong>
                </div>

                <div class="users-detail-item">
                    <span>Email Address</span>
                    <strong>{{ $staff->email }}</strong>
                </div>

                <div class="users-detail-item">
                    <span>Contact Number</span>
                    <strong>{{ $staff->contact_number }}</strong>
                </div>

                <div class="users-detail-item">
                    <span>Position</span>
                    <strong>{{ $staff->position }}</strong>
                </div>

                <div class="users-detail-item">
                    <span>Account Created</span>
                    <strong>
                        {{ $staff->created_at?->format('F d, Y') ?? 'Not available' }}
                    </strong>
                </div>

                <div class="users-detail-item">
                    <span>Last Updated</span>
                    <strong>
                        {{ $staff->updated_at?->format('F d, Y') ?? 'Not available' }}
                    </strong>
                </div>
            </div>

            <div class="staff-form-actions">
                <a href="{{ route('branch-manager.users.index') }}"
                   class="users-btn users-btn-secondary">
                    Back
                </a>

                <a href="{{ route('branch-manager.users.edit', ['staff' => $staff->id]) }}"
                   class="users-btn users-btn-primary">
                    Edit Staff
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

