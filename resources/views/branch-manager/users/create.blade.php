
<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/branch-manager/users.css') }}">

    <div class="users-container">
        <div class="users-header">
            <div>
                <h1 class="users-title">Add Staff</h1>
            </div>

            <a href="{{ route('branch-manager.users.index') }}"
               class="users-btn users-btn-secondary">
                Back to Staff
            </a>
        </div>

        @if ($errors->any())
            <div class="users-alert users-alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('branch-manager.users.store') }}"
              method="POST"
              class="staff-form-card">
            @csrf

            <div class="staff-form-section">
                <h2 class="staff-section-title">Personal Information</h2>

                <div class="staff-name-grid">
                    <div class="staff-field">
                        <label for="first_name">First Name </label>
                        <input id="first_name" type="text" name="first_name"
                               value="{{ old('first_name') }}"
                               placeholder="Enter first name" required>
                    </div>

                    <div class="staff-field">
                        <label for="middle_name">Middle Name</label>
                        <input id="middle_name" type="text" name="middle_name"
                               value="{{ old('middle_name') }}"
                               placeholder="Enter middle name">
                    </div>

                    <div class="staff-field">
                        <label for="last_name">Last Name </label>
                        <input id="last_name" type="text" name="last_name"
                               value="{{ old('last_name') }}"
                               placeholder="Enter last name" required>
                    </div>
                </div>
            </div>

            <div class="staff-form-divider"></div>

            <div class="staff-form-section">
                <h2 class="staff-section-title">Contact Information</h2>

                <div class="staff-contact-grid">
                    <div class="staff-field">
                        <label for="email">Email Address </label>
                        <input id="email" type="email" name="email"
                               value="{{ old('email') }}"
                               placeholder="name@example.com"
                               autocomplete="email" required>
                    </div>

                    <div class="staff-field">
                        <label for="contact_number">Contact Number </label>
                        <input id="contact_number" type="tel" name="contact_number"
                               value="{{ old('contact_number') }}"
                               placeholder="Enter contact number" required>
                    </div>

                    <div class="staff-field">
                        <label for="position">Position </label>
                        <select id="position" name="position" required>
                            <option value="" disabled {{ old('position') ? '' : 'selected' }}>
                                Select position
                            </option>
                            <option value="Photographer"
                                {{ old('position') === 'Photographer' ? 'selected' : '' }}>
                                Photographer
                            </option>
                            <option value="Inventory Clerk"
                                {{ old('position') === 'Inventory Clerk' ? 'selected' : '' }}>
                                Inventory Clerk
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="staff-form-divider"></div>

            <div class="staff-form-section">
                <h2 class="staff-section-title">Account Security</h2>

                <div class="staff-password-field">
                    <div class="staff-field">
                        <label for="password">Temporary Password </label>
                        <input id="password" type="password" name="password"
                               placeholder="Enter temporary password"
                               autocomplete="new-password"
                               minlength="8" required>
                        <small>Use at least 8 characters.</small>
                    </div>
                </div>
            </div>

            <div class="staff-form-actions">
                <button type="submit" class="users-btn users-btn-primary">
                    Create Staff Account
                </button>
            </div>
        </form>
    </div>
</x-app-layout>