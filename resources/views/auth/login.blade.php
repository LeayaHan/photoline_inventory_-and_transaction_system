<x-guest-layout>

    <div class="photoline-login-heading">
        <p class="photoline-login-kicker">Welcome</p>

        <h2>Sign in to Photoline</h2>

        <p>
            Enter your account details to continue to your workspace.
        </p>
    </div>

    <x-auth-session-status
        class="mb-4 photoline-status"
        :status="session('status')"
    />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label
                for="email"
                :value="__('Email')"
                class="photoline-field-label"
            />

            <x-text-input
                id="email"
                class="block photoline-field"
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
                class="mt-2"
            />
        </div>

        <div class="mt-5">
            <x-input-label
                for="password"
                :value="__('Password')"
                class="photoline-field-label"
            />

            <x-text-input
                id="password"
                class="block photoline-field"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Enter your password"
            />

            <x-input-error
                :messages="$errors->get('password')"
                class="mt-2"
            />
        </div>
        <br>
        <div class="flex items-center justify-between mt-5">
            <label
                for="remember_me"
                class="inline-flex items-center photoline-check"
            >
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-gray-300 shadow-sm focus:ring-blue-500"
                    name="remember"
                >

                <span class="ms-2 text-sm">
                    {{ __('Remember me') }}
                </span>
            </label>

            @if (Route::has('password.request'))
                <a
                    class="text-sm underline rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 photoline-forgot"
                    href="{{ route('password.request') }}"
                >
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>
        <br>
        <div class="mt-7">
            <x-primary-button class="w-full justify-center photoline-login-button">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

</x-guest-layout>
