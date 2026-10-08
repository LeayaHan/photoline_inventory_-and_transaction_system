<x-manager-layout title="Edit Staff Account">

@push('styles')
<style>
    .container {
        width: 90%;
        max-width: 750px;
        margin: 40px auto;
    }

    .card {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    h1 {
        margin-top: 0;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        font-weight: bold;
        margin-bottom: 7px;
    }

    input {
        width: 100%;
        padding: 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 15px;
        box-sizing: border-box;
    }

    input:focus {
        outline: none;
        border-color: #2563eb;
    }

    .name-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 15px;
    }

    .error {
        color: #dc2626;
        margin-top: 5px;
        font-size: 13px;
    }

    .buttons {
        display: flex;
        gap: 10px;
        margin-top: 25px;
    }

    .btn {
        padding: 11px 18px;
        border-radius: 6px;
        border: none;
        text-decoration: none;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-primary {
        background: #2563eb;
        color: white;
    }

    .btn-secondary {
        background: #e5e7eb;
        color: #111827;
    }

    @media (max-width: 640px) {
        .name-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

<div class="container">

    <div class="card">

        <h1>Edit Staff Account</h1>

        <p>
            Update the staff member's account information.
        </p>

        <form
            action="{{ route('users.update', $user) }}"
            method="POST"
        >

            @csrf
            @method('PUT')

            <div class="form-group">

                <br>

                <div class="name-grid">

                    <div>
                        <label for="first_name">
                            First Name
                        </label>

                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            value="{{ old('first_name', $user->first_name) }}"
                            required
                        >

                        @error('first_name')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="middle_name">
                            Middle Name
                        </label>

                        <input
                            type="text"
                            id="middle_name"
                            name="middle_name"
                            value="{{ old('middle_name', $user->middle_name) }}"
                            placeholder="Optional"
                        >

                        @error('middle_name')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name">
                            Last Name
                        </label>

                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            value="{{ old('last_name', $user->last_name) }}"
                            required
                        >

                        @error('last_name')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

            </div>

            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    required
                >

                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror

            </div>

            <div class="form-group">

                <label for="password">
                    New Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Leave blank to keep current password"
                >

                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror

            </div>

            <div class="form-group">

                <label for="password_confirmation">
                    Confirm New Password
                </label>

                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Re-enter new password"
                >

            </div>

            <div class="buttons">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Save Changes
                </button>

                <a
                    href="{{ route('users.index') }}"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>

</x-manager-layout>