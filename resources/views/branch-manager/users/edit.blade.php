```blade
<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/branch-manager/users.css') }}">

    <div class="users-container">
        <div class="users-header">
            <div>
                <h1 class="users-title">Edit Staff</h1>
            </div>

        </div>

        @if ($errors->any())
            <div class="users-alert users-alert-error">
                <strong>Please correct the following errors:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="staff-form-card">
            <div class="staff-id-label">
                <span>Staff ID:</span>
                <strong>{{ $staff->staff_id }}</strong>
            </div>

            <form
                action="{{ route('branch-manager.users.update', ['staff' => $staff->id]) }}"
                method="POST"
            >
                @csrf
                @method('PUT')

                <div class="staff-id-row">
                    <h2 class="staff-section-title">Personal Information</h2>

                    <div class="staff-name-grid">
                        <div class="staff-field">
                            <label for="first_name">First Name </label>
                            <input
                                type="text"
                                id="first_name"
                                name="first_name"
                                value="{{ old('first_name', $staff->first_name) }}"
                                required
                            >
                        </div>

                        <div class="staff-field">
                            <label for="middle_name">Middle Name</label>
                            <input
                                type="text"
                                id="middle_name"
                                name="middle_name"
                                value="{{ old('middle_name', $staff->middle_name) }}"
                            >
                        </div>

                        <div class="staff-field">
                            <label for="last_name">Last Name </label>
                            <input
                                type="text"
                                id="last_name"
                                name="last_name"
                                value="{{ old('last_name', $staff->last_name) }}"
                                required
                            >
                        </div>
                    </div>
                </div>

                <div class="staff-form-divider"></div>

                <div class="staff-form-section">
                    <h2 class="staff-section-title">Contact Information</h2>

                    <div class="staff-contact-grid">
                        <div class="staff-field">
                            <label for="email">Email Address </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $staff->email) }}"
                                required
                            >
                        </div>

                        <div class="staff-field">
                            <label for="contact_number">Contact Number </label>
                            <input
                                type="text"
                                id="contact_number"
                                name="contact_number"
                                value="{{ old('contact_number', $staff->contact_number) }}"
                                required
                            >
                        </div>

                        <div class="staff-field">
                            <label for="position">Position </label>
                            <select id="position" name="position" required>
                                <option value="">Select Position</option>

                                <option value="Cashier"
                                    {{ old('position', $staff->position) === 'Cashier' ? 'selected' : '' }}>
                                    Cashier
                                </option>

                                <option value="Photographer"
                                    {{ old('position', $staff->position) === 'Photographer' ? 'selected' : '' }}>
                                    Photographer
                                </option>

                                <option value="Inventory Clerk"
                                    {{ old('position', $staff->position) === 'Inventory Clerk' ? 'selected' : '' }}>
                                    Inventory Clerk
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="staff-form-divider"></div>

                <div class="staff-form-section">
                    <h2 class="staff-section-title">Account Security</h2>

                    <div class="staff-field staff-password-field">
                        <label for="password">New Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter a new password if changing it"
                            autocomplete="new-password"
                        >
                        <small>Leave blank to keep the current password.</small>
                    </div>
                </div>

                <div class="staff-form-actions">
                    <button type="submit" class="users-btn users-btn-primary">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
```
