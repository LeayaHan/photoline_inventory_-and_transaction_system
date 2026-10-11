<x-guest-layout>
    <div class="photoline-login-heading">
        <p class="photoline-login-kicker">WELCOME BACK</p>

        <h2>Sign in to Photoline</h2>

        <p>Enter your account details to continue to your workspace.</p>
    </div>

    <x-auth-session-status
        class="photoline-status"
        :status="session('status')"
    />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="photoline-form-group">
            <x-input-label
                for="email"
                :value="__('Email address')"
                class="photoline-field-label"
            />

            <x-text-input
                id="email"
                class="block w-full photoline-field"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                placeholder="Enter your email address"
            />

            <x-input-error
                :messages="$errors->get('email')"
                class="photoline-error"
            />
        </div>

        <div class="photoline-form-group photoline-password-group">
            <x-input-label
                for="password"
                :value="__('Password')"
                class="photoline-field-label"
            />

            <x-text-input
                id="password"
                class="block w-full photoline-field"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Enter your password"
            />

            <x-input-error
                :messages="$errors->get('password')"
                class="photoline-error"
            />
        </div>

        <div class="photoline-login-options">
            <label for="remember_me" class="photoline-check">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    value="1"
                >

                <span>Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a
                    href="{{ route('password.request') }}"
                    class="photoline-forgot"
                >
                    Forgot password?
                </a>
            @endif
        </div>

        <button type="submit" class="photoline-login-button">
            LOG IN
        </button>

        <div class="photoline-login-footer">
            <span>PHOTOLINE</span>
            <span>Inventory and Transaction Management System</span>
        </div>
    </form>
</x-guest-layout>