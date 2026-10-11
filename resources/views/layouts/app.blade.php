
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Photoline') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('css/photoline-layout.css') }}">
</head>
<body>
    <div class="photoline-layout">
        <aside class="photoline-sidebar" id="photolineSidebar">
            <a href="{{ route('branch-manager.dashboard') }}" class="photoline-brand">
                <span class="photoline-brand-name">PHOTO<span>LINE</span></span>
                <span class="photoline-brand-subtitle">Inventory & Transaction System</span>
            </a>

            <div class="photoline-menu-label">MAIN MENU</div>

            <nav class="photoline-menu">
                <a href="{{ route('branch-manager.dashboard') }}"
                   class="photoline-nav-link {{ request()->routeIs('branch-manager.dashboard') ? 'active' : '' }}">
                    <span class="photoline-nav-icon">▦</span>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('branch-manager.users.index') }}"
                   class="photoline-nav-link {{ request()->routeIs('branch-manager.users.*') ? 'active' : '' }}">
                    <span class="photoline-nav-icon">♙</span>
                    <span>Users</span>
                </a>

                @php($lowStockTotal = array_sum(\App\Models\InventoryItem::lowStockCounts()))
                <a href="{{ route('branch-manager.inventory.index') }}"
                   class="photoline-nav-link {{ request()->routeIs('branch-manager.inventory.*') ? 'active' : '' }}"
                   @if ($lowStockTotal > 0) title="{{ $lowStockTotal }} item(s) low or out of stock" @endif>
                    <span class="photoline-nav-icon">▣</span>
                    <span>Inventory</span>
                    @if ($lowStockTotal > 0)
                        <span class="photoline-nav-badge">{{ $lowStockTotal }}</span>
                    @endif
                </a>

                @php($pendingInventoryRequests = \App\Models\InventoryRequest::where('status', 'pending')->count())
                <a href="{{ route('branch-manager.inventory-requests.index') }}"
                   class="photoline-nav-link {{ request()->routeIs('branch-manager.inventory-requests.*') ? 'active' : '' }}">
                    <span class="photoline-nav-icon">✉</span>
                    <span>Inventory Requests</span>
                    @if ($pendingInventoryRequests > 0)
                        <span class="photoline-nav-badge">{{ $pendingInventoryRequests }}</span>
                    @endif
                </a>

                <a href="{{ route('branch-manager.transactions.index') }}"
                   class="photoline-nav-link {{ request()->routeIs('branch-manager.transactions.*') ? 'active' : '' }}">
                    <span class="photoline-nav-icon">⇄</span>
                    <span>Transactions</span>
                </a>

                <a href="{{ route('branch-manager.reports.index') }}"
                   class="photoline-nav-link {{ request()->routeIs('branch-manager.reports.*') ? 'active' : '' }}">
                    <span class="photoline-nav-icon">▤</span>
                    <span>Reports</span>
                </a>

                <a href="{{ route('branch-manager.audits.index') }}"
                   class="photoline-nav-link {{ request()->routeIs('branch-manager.audits.*') ? 'active' : '' }}">
                    <span class="photoline-nav-icon">☷</span>
                    <span>Audit Records</span>
                </a>
            </nav>

            <div class="photoline-sidebar-bottom">
                <div class="photoline-account">
                    <div class="photoline-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>

                    <div class="photoline-account-info">
                        <span class="photoline-account-name">{{ auth()->user()->name ?? 'User' }}</span>
                        <span class="photoline-account-role">Signed in</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="photoline-logout">
                        <span class="photoline-nav-icon">↪</span>
                        <span>Log out</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="photoline-main">
            <header class="photoline-topbar">
                <button type="button"
                        class="photoline-menu-toggle"
                        id="photolineMenuToggle"
                        aria-label="Toggle navigation"
                        aria-controls="photolineSidebar"
                        aria-expanded="false">
                    ☰
                </button>

                <div class="photoline-topbar-title">
                    <span class="photoline-topbar-kicker">PHOTO<span>LINE</span></span>
                    <span class="photoline-topbar-divider">/</span>
                    <span>{{ $header ?? 'Workspace' }}</span>
                </div>
            </header>

            @isset($header)
                <div class="photoline-page-heading">
                    {{ $header }}
                </div>
            @endisset

            <main class="photoline-page-content">
                {{ $slot }}
            </main>

            <footer class="photoline-footer">
                <span>Photoline Inventory & Transaction System</span>
            </footer>
        </div>
    </div>

    <script>
        const menuToggle = document.getElementById('photolineMenuToggle');
        const sidebar = document.getElementById('photolineSidebar');

        menuToggle.addEventListener('click', function () {
            const isOpen = sidebar.classList.toggle('sidebar-open');
            menuToggle.setAttribute('aria-expanded', String(isOpen));
        });
    </script>
</body>
</html>