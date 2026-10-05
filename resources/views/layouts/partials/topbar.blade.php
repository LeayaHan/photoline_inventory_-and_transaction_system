@php
    $user = auth()->user();
    $isManager = $user && $user->isManager();
@endphp

<header class="pl-topbar">
    <a href="{{ route('dashboard') }}" class="pl-brand">
        Photoline Abreeza
        <small>{{ $isManager ? 'Manager Panel' : 'Staff Panel' }}</small>
    </a>

    <nav class="pl-links">
        <a href="{{ route('dashboard') }}"
           class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>

        @if($isManager)
            <a href="{{ route('manager.transactions.index') }}"
               class="{{ request()->routeIs('manager.transactions.*') ? 'active' : '' }}">Transactions</a>
            <a href="{{ route('manager.audits.index') }}"
               class="{{ request()->routeIs('manager.audits.*') ? 'active' : '' }}">Audits</a>
            <a href="{{ route('products.index') }}"
               class="{{ request()->routeIs('products.*') ? 'active' : '' }}">Inventory</a>
            <a href="{{ route('reports.index') }}"
               class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">Reports</a>
            <a href="{{ route('replenishments.index') }}"
               class="{{ request()->routeIs('replenishments.*') ? 'active' : '' }}">Replenishment</a>
            <a href="{{ route('users.index') }}"
               class="{{ request()->routeIs('users.*') ? 'active' : '' }}">Staff</a>
        @else
            <a href="{{ route('transactions.index') }}"
               class="{{ request()->routeIs('transactions.*') ? 'active' : '' }}">Transactions</a>
            <a href="{{ route('audits.index') }}"
               class="{{ request()->routeIs('audits.*') ? 'active' : '' }}">Audits</a>
            <a href="{{ route('products.index') }}"
               class="{{ request()->routeIs('products.*') ? 'active' : '' }}">Inventory</a>
        @endif
    </nav>

    <div class="pl-user">
        <span>{{ $user->name }}</span>
        <span class="role">{{ $isManager ? 'Branch Manager' : 'Staff' }}</span>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="pl-logout">Logout</button>
        </form>
    </div>
</header>