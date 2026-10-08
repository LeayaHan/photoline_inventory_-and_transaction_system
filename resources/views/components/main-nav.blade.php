@props([
    'links',
    'roleLabel',
    'theme' => 'staff',
])

<nav class="navbar {{ $theme === 'manager' ? 'navbar-manager' : 'navbar-staff' }}">
    <div class="nav-brand">
        <div class="nav-logo-box">
            <img src="{{ asset('images/photoline-logo.jpg') }}" alt="Photoline">
        </div>

        <div class="nav-brand-text">
            <strong>Photoline</strong>
            <span>Abreeza</span>
        </div>
    </div>

    <div class="nav-role">
        <span class="role-dot"></span>
        {{ $roleLabel }}
    </div>

    <div class="nav-links">
        @foreach ($links as [$label, $route, $pattern])
            <a
                href="{{ route($route) }}"
                @class(['active' => request()->routeIs($pattern)])
                @if (request()->routeIs($pattern)) aria-current="page" @endif
            >
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="user-area">
        <div class="user-info">
            <span class="user-name">{{ auth()->user()->name }}</span>
            <span class="user-role">{{ $roleLabel }}</span>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-button">Logout</button>
        </form>
    </div>
</nav>
