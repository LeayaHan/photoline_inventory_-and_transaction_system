@props(['links', 'roleLabel'])

<div class="navbar">
    <h2>Photoline Abreeza</h2>

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
        <span>{{ auth()->user()->name }} — {{ $roleLabel }}</span>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-button">Logout</button>
        </form>
    </div>
</div>
